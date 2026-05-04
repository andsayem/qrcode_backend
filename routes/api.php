<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\TechnicianNomineeController;
use App\Http\Controllers\Backend\Gift\GiftTransactionController;
use App\Http\Controllers\Api\GiftController;
use App\Http\Controllers\Api\GiftTransactionController as ApiGiftTransactionController;

// Route::get('/login', function(){
//     print_r('Test');
// });
//Route::get('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
});
// Route::post('signup',  function(){
//     print_r('djbf');
// });
Route::get('checksms', [App\Http\Controllers\Api\UserAPIController::class, 'checksms']);
Route::post('signup', [App\Http\Controllers\Api\UserAPIController::class, 'store']);
Route::post('varify_password_otp', [App\Http\Controllers\Api\UserAPIController::class, 'varifyPasswordOtp']);
Route::post('resend_otp', [App\Http\Controllers\Api\UserAPIController::class, 'resend_otp']);
Route::post('reset-password', [App\Http\Controllers\Api\UserAPIController::class, 'resetPassword']);
// User Phone number check and OTP SMS send 
Route::post('check-exists-user', [App\Http\Controllers\Api\UserAPIController::class, 'checkExistsUser']);

//Push Pull sms
Route::get('/pushpull', [\App\Http\Controllers\Api\PushPullSmsController::class, 'pushpull'])->name('pushpull');
//Route::post('/pushpull_post', [\App\Http\Controllers\Api\PushPullSmsController::class, 'pushpull'])->name('pushpull');





Route::group([
    'middleware' => 'auth:api'
], function () {
    Route::get('all_learning_and_tutorials', [App\Http\Controllers\Api\LearningAndTutorialAPIController::class, 'index']);
    Route::get('check_user_birthday', [App\Http\Controllers\Api\UserAPIController::class, 'checkUserBirthdayOrNot']);
    Route::get('hero_of_day', [App\Http\Controllers\Api\UserPointAPIController::class, 'getHeroOfDay']);
    Route::get('heroes_previous_month', [App\Http\Controllers\Api\UserPointAPIController::class, 'getHeroesOfPreviousMonth']);
    Route::get('get-offers', [App\Http\Controllers\Api\OfferAPIController::class, 'index']);
    Route::get('all_campaigns', [App\Http\Controllers\Api\CampaignAPIController::class, 'All_Campaigns']);
    Route::get('profile', [App\Http\Controllers\Api\UserAPIController::class, 'Profile']);
    Route::post('profile-image-change', [App\Http\Controllers\Api\UserAPIController::class, 'changeImage']);
    Route::post('change-password', [App\Http\Controllers\Api\UserAPIController::class, 'changePassword']);
    Route::resource('user_points', App\Http\Controllers\Api\UserPointAPIController::class);
    Route::post('scan_qr_code', [App\Http\Controllers\Api\UserPointAPIController::class, 'scanQrCode']);
    Route::get('app_dashboard', [App\Http\Controllers\Api\AppDashboardController::class, 'index']);
    Route::resource('user_redeem_requests', App\Http\Controllers\Api\UserRedeemRequestAPIController::class);
    Route::resource('technicians', App\Http\Controllers\Api\TechnicianAPIController::class);
    Route::get('technician_info', [App\Http\Controllers\Api\TechnicianAPIController::class, 'showInfo']);
    Route::get('technician_info_check', [App\Http\Controllers\Api\TechnicianAPIController::class, 'infocheck']);
    Route::get('campaigns', [App\Http\Controllers\Api\TechnicianAPIController::class, 'campaigns']);
    Route::post('technician_update', [App\Http\Controllers\Api\TechnicianAPIController::class, 'technician_update']);
    Route::resource('feedback', App\Http\Controllers\Api\FeedbackAPIController::class);
    Route::resource('settings', App\Http\Controllers\Api\SettingsAPIController::class);
    Route::resource('notifications', App\Http\Controllers\Api\NotificationAPIController::class);
    Route::post('cancel_redeem/{id}', [App\Http\Controllers\Api\UserRedeemRequestAPIController::class, 'cancelRedeem']);
    // Route::post('/redeem', [GiftTransactionController::class, 'redeemApi']);
    Route::get('/gifts', [GiftController::class, 'index']);
    Route::get('/transactions', [ApiGiftTransactionController::class, 'index']);
    Route::get('/transaction_details/{id}', [ApiGiftTransactionController::class, 'transactionDetails']);
    Route::post('/redeem', [GiftTransactionController::class, 'redeemApi']);

    // Technician Nominee API
    Route::prefix('technician-nominee')->group(function () {
        Route::get('/{userId}', [TechnicianNomineeController::class, 'index']);        // List nominees for a technician
        Route::post('/store', [TechnicianNomineeController::class, 'store']);          // Create a nominee
        Route::put('/update/{id}', [TechnicianNomineeController::class, 'update']);    // Update nominee
        Route::delete('/delete/{id}', [TechnicianNomineeController::class, 'destroy']); // Delete nominee
    });
});
Route::get('ssforce_user_redeem_requests', [App\Http\Controllers\Api\UserRedeemRequestAPIController::class, 'ssforceIndex']);
Route::get('ssforce_user_redeem_requests_blank_gateway_number', [App\Http\Controllers\Api\UserRedeemRequestAPIController::class, 'ssforceUserRedeemRequestsBlankGatewayNumber']);
Route::post('redeem-request-generate-otp', [App\Http\Controllers\Api\UserRedeemRequestAPIController::class, 'generateOTP']);
Route::post('redeem-request-otp-check', [App\Http\Controllers\Api\UserRedeemRequestAPIController::class, 'otpCheck']);
Route::get('redeem-history', [App\Http\Controllers\Api\UserRedeemRequestAPIController::class, 'redeemHistory']);
Route::get('redeem-dashboard', [App\Http\Controllers\Api\UserRedeemRequestAPIController::class, 'redeemDashboard']);

//
Route::get('technician/details/{user_id}', [App\Http\Controllers\Api\TechnicianAPIController::class, 'details']);
Route::resource('campaign_categories', App\Http\Controllers\Api\CampaignCategoryAPIController::class);
Route::resource('campaign_details', App\Http\Controllers\Api\CampaignDetailsAPIController::class);
Route::get('id_card/{id}', [App\Http\Controllers\Api\UserAPIController::class, 'id_card']);
Route::post('scan_qr_code_general', [App\Http\Controllers\Api\UserPointAPIController::class, 'scanQrCodeGeneral']);
Route::get('file_sync', [App\Console\Commands\FileGenerateCommand::class, 'handle']);
Route::get('code_generation_sync', [App\Console\Commands\CodeGeneration::class, 'handle']);

Route::get('divisions', [App\Http\Controllers\Api\UserAPIController::class, 'divisions']);
Route::get('districts/{id}', [App\Http\Controllers\Api\UserAPIController::class, 'districts']);
Route::get('unions/{id}', [App\Http\Controllers\Api\UserAPIController::class, 'unions']);
Route::get('upazilas/{id}', [App\Http\Controllers\Api\UserAPIController::class, 'upazilas']);
Route::get('get_technician', [App\Http\Controllers\Api\TechnicianAPIController::class, 'getTechnician']);
Route::get('technician-info', [App\Http\Controllers\Api\TechnicianAPIController::class, 'technicianInfo']);
Route::post('technician_status_update/{id}', [App\Http\Controllers\Api\TechnicianAPIController::class, 'technician_status_update']);
Route::post('user_redeem_request_demo', [App\Http\Controllers\Api\UserRedeemRequestAPIController::class, 'demoRequest']);


Route::apiResource('app-version', App\Http\Controllers\Api\AppVersionController::class);

//SummaryReport
Route::get('summary_report', [App\Console\Commands\SummaryReport::class, 'handle']);

Route::get('check_technician_point', [App\Http\Controllers\Api\TechnicianAPIController::class, 'checkTechnicianPoint']);
