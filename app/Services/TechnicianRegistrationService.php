<?php

namespace App\Services;

use App\Models\Technician;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Admin-side technician registration.
 *
 * Mirrors the Apon app sign-up flow (lib/views/signup/Signup.dart + POST /api/signup):
 * HRIS employee check -> retailer check -> FO reference check -> create User + Technician (role Technician).
 * The app's API (Api\UserAPIController@store) is intentionally left untouched.
 */
class TechnicianRegistrationService
{
    const TECHNICIAN_ROLE_ID = 15; // "Technician" (same as Api\UserAPIController@store)
    const BANGLADESH_COUNTRY_ID = 1;
    const TIMEOUT_SECONDS = 15;

    /** true if the mobile belongs to an SSG employee (HRIS). */
    public function isSsgEmployee(string $mobile): bool
    {
        $res = Http::timeout(self::TIMEOUT_SECONDS)->acceptJson()
            ->get(config('services.hris.validate_mobile_url'), ['mobile' => '88' . $mobile]);
        $this->assertOk($res, 'HRIS');

        return (int) $res->json('status') === 1;
    }

    /** true if the mobile is already a registered retailer (ssforce). */
    public function isRetailer(string $mobile): bool
    {
        $res = Http::timeout(self::TIMEOUT_SECONDS)->acceptJson()
            ->get(config('services.ssforce.url') . '/retailer-mobile-no-check', ['mobile_no' => '88' . $mobile]);
        $this->assertOk($res, 'Retailer');

        return $res->json('success') === true;
    }

    /**
     * FOs working in an area (same list the app shows after area selection).
     * `employee_id` is the FO ID accepted by fo-check.
     */
    public function foListByArea(int $areaId): array
    {
        $res = Http::timeout(self::TIMEOUT_SECONDS)->acceptJson()
            ->get(config('services.ssforce.url') . '/apps-fo-list-area-wise', ['area_id' => $areaId]);
        $this->assertOk($res, 'FO list');

        return collect($res->json('data') ?? [])
            ->map(function ($fo) {
                return [
                    'fo_id' => (string) ($fo['employee_id'] ?? ''),
                    'name' => $fo['display_name'] ?? '',
                    'phone' => $fo['cell_phone'] ?? '',
                ];
            })
            ->filter(function ($fo) { return $fo['fo_id'] !== ''; })
            ->values()
            ->all();
    }

    /** FO reference data (fo / tsm / point) from ssforce, or null if the FO id is invalid. */
    public function foReference(string $foId): ?array
    {
        $res = Http::timeout(self::TIMEOUT_SECONDS)->acceptJson()
            ->post(config('services.ssforce.url') . '/fo-check', ['foid' => $foId]);
        $this->assertOk($res, 'FO');

        return $res->json('status') === true ? ($res->json('data') ?? []) : null;
    }

    /**
     * Create the technician exactly like the app does, except: no OTP/SMS, phone marked verified.
     *
     * @param array $data validated: name, mobile, password, division_id, district_id, thana_id, area_id
     * @param array $reference FO reference data from foReference()
     */
    public function register(array $data, array $reference): User
    {
        return DB::transaction(function () use ($data, $reference) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['mobile'],          // app stores the mobile in `email`
                'phone_number' => $data['mobile'],
                'password' => bcrypt($data['password']),
                'status' => 1,                       // active (same as app)
                'phone_verification_status' => 1,    // admin-registered: no OTP needed
            ]);

            Technician::create([
                'user_id' => $user->id,
                'country_id' => self::BANGLADESH_COUNTRY_ID,
                'total_point' => 0,
                'referral_code' => $user->id . '' . mt_rand(1, 99999),
                'total_redeem_value' => 0,
                'current_point' => 0,
                'payment_gateway' => 1, // bKash
                'gatway_number' => '',
                'nid_font' => '',
                'nid_back' => '',
                'pending_point' => 0,
                'father_name' => '',
                'permanent_address' => '',
                'current_address' => '',
                'birthday' => '',
                'occupation' => '',
                'nid_number' => '',
                'blood_group' => '',
                'experience' => '',
                'organization' => '',
                'dealer_code' => '',
                'dealer_name' => '',
                'zone' => '',
                'education' => '',
                'division_id' => $data['division_id'],
                'district_id' => $data['district_id'],
                'upazilla_id' => $data['thana_id'],
                'union_id' => $data['area_id'],
                'fo_code' => $reference['fo']['email'] ?? '',
                'fo_name' => $reference['fo']['display_name'] ?? '',
                'tsm_code' => $reference['tsm']['email'] ?? '',
                'tsm_name' => $reference['tsm']['display_name'] ?? '',
                'point_code' => $reference['point']['sap_code'] ?? '',
                'point_name' => $reference['point']['point_name'] ?? '',
            ]);

            DB::table('model_has_roles')->where('model_id', $user->id)->delete();
            $user->assignRole(self::TECHNICIAN_ROLE_ID);

            return $user;
        });
    }

    private function assertOk($res, string $source): void
    {
        if (!$res->successful()) {
            throw new RuntimeException("{$source} check failed (HTTP {$res->status()})");
        }
    }
}
