<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Speeds up the admin SSG Code list and Scan History pages.
 *
 * ssg_code_details (~13M rows):
 *   - total_used            : default list (ORDER BY total_used DESC, id DESC)
 *   - status, total_used    : Status = Used/Unused filter + same ordering
 *   - updated_at            : Status = "Multiple used" (ORDER BY updated_at DESC)
 *   - product_id, total_used: Product / SAP Code filter + ordering
 *
 * code_verify_logs (~1.7M rows, previously only PRIMARY):
 *   - code                  : Scan History + unique-attempt counts (code_id is never populated)
 *
 * MariaDB 10.4 builds these online (ALGORITHM=INPLACE, LOCK=NONE), so reads/writes continue.
 * Run ONLY this file:  php artisan migrate --path=database/migrations/2026_11_22_120000_add_report_indexes_to_ssg_code_details_and_code_verify_logs.php
 */
class AddReportIndexesToSsgCodeDetailsAndCodeVerifyLogs extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE code_verify_logs ADD INDEX idx_cvl_code (code), ALGORITHM=INPLACE, LOCK=NONE');

        DB::statement('ALTER TABLE ssg_code_details
            ADD INDEX idx_ssg_total_used (total_used),
            ADD INDEX idx_ssg_status_total_used (status, total_used),
            ADD INDEX idx_ssg_updated_at (updated_at),
            ADD INDEX idx_ssg_product_total_used (product_id, total_used),
            ALGORITHM=INPLACE, LOCK=NONE');
    }

    public function down()
    {
        DB::statement('ALTER TABLE code_verify_logs DROP INDEX idx_cvl_code');

        DB::statement('ALTER TABLE ssg_code_details
            DROP INDEX idx_ssg_total_used,
            DROP INDEX idx_ssg_status_total_used,
            DROP INDEX idx_ssg_updated_at,
            DROP INDEX idx_ssg_product_total_used');
    }
}
