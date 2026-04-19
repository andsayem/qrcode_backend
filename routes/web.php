<?php

use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ChannelController;
use App\Http\Controllers\Backend\HomeController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\RedeemController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\RequestCodeController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SSGCodeController;
use App\Http\Controllers\Backend\TechnicianController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\VendorController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ChannelSettingsController;
use App\Http\Controllers\Frontend\CheckCodeController;
use App\Http\Controllers\Reports\MonthlyRedeemPieChartController;
use App\Http\Controllers\Reports\MonthlyVerifiedProductPieChartController;
use App\Http\Controllers\Reports\MonthWiseEarningsSettlementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\TechnicianNomineeController;
use App\Http\Controllers\Backend\Gift\GiftPolicyController;
use App\Http\Controllers\Backend\Gift\GiftController;
use App\Http\Controllers\Backend\Gift\GiftTransactionController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Auth::routes(['register' => false]); 
Route::get('/optimize', function () {
    $exitCode = \Illuminate\Support\Facades\Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});


Route::middleware(['auth'])->group(function () { 
    Route::get('/dashboard', \App\Http\Controllers\HomeController::class)->name('admin.dashboard'); 
    //Mgt Dashboard

     Route::get('/mgt-dashboard', \App\Http\Controllers\MgtDashboardController::class)->name('admin.mgtdashboard'); 


    Route::prefix('reports')->group(function () {
        Route::get('/monthly-redeem-pie-chart', MonthlyRedeemPieChartController::class)->name('reports.monthly-redeem-pie-chart');
        Route::get('/monthly-verified-product-pie-chart', MonthlyVerifiedProductPieChartController::class)->name('reports.monthly-verified-product-pie-chart');
        Route::get('/month-wise-earnings-settlement', MonthWiseEarningsSettlementController::class)->name('reports.month-wise-earnings-settlement');
    });
});


Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
    //technicians 
    Route::get('/get_technicians', [HomeController::class, 'getTechniciansData'])->name('admin.getTechniciansData');
    //getQrcodeData

    Route::get('/get_qrcode_data', [HomeController::class, 'getQrcodeData'])->name('admin.getQrcodeData');

    Route::get('/get_qr_point_data', [HomeController::class, 'getQrPointData'])->name('admin.getQrPointData');

    //getRedeemData 

    Route::get('/get_redeem_data', [HomeController::class, 'getRedeemData'])->name('admin.getRedeemData');
    // getChartData
    Route::get('/getChartData', [HomeController::class, 'getChartData'])->name('admin.getChartData');
    Route::get('/getVerifiedProduct', [HomeController::class, 'getVerifiedProduct'])->name('admin.getVerifiedProduct');

    Route::get('/getRedeemSummary', [RedeemController::class, 'getRedeemSummary'])->name('admin.redeem.getRedeemSummary');
    Route::get('/getPointSummary', [RedeemController::class, 'getPointSummary'])->name('admin.point.getPointSummary');



    Route::resource('users', UserController::class, ['names' => 'admin.users'])->parameters(['users' => 'id']);

    Route::resource('technicians', TechnicianController::class, ['names' => 'admin.technicians'])->parameters(['technicians' => 'id']);
    Route::get('new_technician', [UserController::class, 'new_technician'])->name('admin.users.new_technician');
    Route::post('store_technician', [UserController::class, 'store_technician'])->name('admin.technician.store_technician');
    Route::get('technician_download', [UserController::class, 'technician_download'])->name('admin.users.technician_download');
    Route::get('technician-user', [UserController::class,  'technicianUser'])->name('admin.users.technician_user');

    //getSsforcethana
    Route::get('divisions', [UserController::class,  'getSsforceDivisions'])->name('admin.users.getSsforceDivisions');

    Route::get('district', [UserController::class,  'getSsforceDistrict'])->name('admin.users.getSsforceDistrict');
    //getSsforcethana
    Route::get('thana', [UserController::class,  'getSsforcethana'])->name('admin.users.getSsforcethana');
    //getSsforcearea
    Route::get('area', [UserController::class,  'getSsforcearea'])->name('admin.users.getSsforcearea');

    Route::get('redeem-report', [UserController::class,  'redeemreport'])->name('admin.users.redeem_report');
    Route::resource('roles', RoleController::class, ['names' => 'admin.roles']);
    //Route::post('password-change', [\App\Http\Controllers\Backend\PasswordChangeController::class, 'passwordUpdate'])->name('password.update');
    Route::post('admin/password-change', [\App\Http\Controllers\Backend\PasswordChangeController::class, 'update'])->name('admin.password.update');

    Route::resource('categories', CategoryController::class, ['names' => 'admin.categories']);
    Route::resource('channels', ChannelController::class, ['names' => 'admin.channels']);
    //Route::resource('channelSettings', ChannelSettingsController::class); 
    Route::get('channel_settings_update/{id}', [ChannelSettingsController::class, 'edit'])->name('admin.channel_settings.update');
    // Route::post('channel_settings_update/{id}', [ChannelSettingsController::class , 'update'])->name('admin.channel_settings.update'); 
    Route::put('channel_settings_update/{id}', [ChannelSettingsController::class, 'update'])->name('admin.channel_settings.update');
    Route::resource('products', ProductController::class, ['names' => 'admin.products']);
    //download
    Route::get('products_download', [ProductController::class, 'download'])
        ->name('admin.products.download');
    Route::post('products_upload', [ProductController::class, 'upload'])
    ->name('admin.products.upload');

    Route::resource('vendors', VendorController::class, ['names' => 'admin.vendors']);
    Route::resource('redeem', RedeemController::class, ['names' => 'admin.redeem']);
    Route::get('user_point', [RedeemController::class, 'user_point'])->name('admin.user_point.user_point');
    Route::get('user_point_monthly', [RedeemController::class, 'user_point_monthly'])->name('admin.user_point_monthly.user_point_monthly');
    Route::get(
        'user-point-monthly/download',
        [RedeemController::class, 'user_point_monthly_download']
    )->name('admin.user_point_monthly.download');

    Route::get('pending_redeem', [RedeemController::class, 'pending_redeem'])->name('admin.redeem.pending_redeem');
    Route::get('pending_redeem_list', [RedeemController::class, 'pending_redeem_list'])->name('admin.redeem.pending_redeem_list');
    Route::get('pending_redeem_delete/{id}', [RedeemController::class, 'pending_redeem_delete'])->name('admin.redeem.pending_redeem_delete');
    Route::post('approval_redeem', [RedeemController::class, 'approval_redeem'])->name('admin.redeem.approval');
    Route::get('redeem_request_download', [RedeemController::class, 'redeem_request_download'])->name('admin.redeem.redeem_request_download');
    Route::get('redeem_paid_download', [RedeemController::class, 'redeem_paid_download'])->name('admin.redeem.redeem_paid_download');
    Route::post('redeem_paid_list', [RedeemController::class, 'redeem_paid_list'])->name('admin.redeem.redeem_paid_list');

    //redeem_db_paid_list
    Route::post('redeem_db_paid_list', [RedeemController::class, 'redeem_db_paid_list'])->name('admin.redeem.redeem_db_paid_list');
    Route::resource('requestcodes', RequestCodeController::class, ['names' => 'admin.requestcodes']);


    Route::get('code_generator_delete/{id}', [RequestCodeController::class, 'code_generator_delete'])->name('requestcodes.code_generator_delete');
    Route::get('code_generator', [RequestCodeController::class, 'code_generator'])->name('requestcodes.code_generator');
    Route::get('code_generator_v2', [RequestCodeController::class, 'code_generator_v2'])->name('requestcodes.code_generator_v2');
    Route::get('code_generator_v3', [RequestCodeController::class, 'code_generator_v3'])->name('requestcodes.code_generator_v3');

    //Route::get('code_generation_sync', [App\Console\Commands\CodeGeneration::class, 'handle']);


    Route::post('/requestcodes_approval', [RequestCodeController::class, 'requestcodes_approval'])->name('requestcodes.approval');
    //requestcodes_print
    Route::post('/requestcodes_print', [RequestCodeController::class, 'requestcodes_print'])->name('requestcodes.print');
    Route::post('/requestcodes_vendor', [RequestCodeController::class, 'requestcodes_vendor'])->name('requestcodes.vendor');
    Route::get('/code_generation', [RequestCodeController::class, 'code_generation'])->name('code_generation');
    Route::get('/download_codes/{id}', [RequestCodeController::class, 'download_codes'])->name('download_codes');
    Route::resource('ssgcodes', SSGCodeController::class, ['names' => 'admin.ssgcodes']);
    Route::get('verified-product', [SSGCodeController::class, 'verifiedProduct'])->name('admin.verified-product');
    Route::resource('reports', ReportController::class, ['names' => 'admin.reports']);
    Route::get('code-sample-file-download', [\App\Http\Controllers\Backend\SSGCodeController::class, 'codeSampleFile'])->name('code-sample-file.download');
    Route::post('/ssg_code_upload', [SSGCodeController::class, 'ssg_code_upload'])->name('admin.ssgcodes.upload');
    Route::get('/ssg_code_printed/{id}', [SSGCodeController::class, 'ssg_code_printed'])->name('admin.ssgcodes.printed');


    Route::get('/code-print-status-list', [\App\Http\Controllers\Backend\CodePrintStatusReportController::class, 'index'])->name('admin.code-print-status-list.index');
    Route::get('/code-print-status-list/download', [\App\Http\Controllers\Backend\CodePrintStatusReportController::class, 'downloadCSV'])->name('admin.code-print-status-list.download');
    Route::get('report_download', [ReportController::class, 'report_download'])->name('report_download');
    Route::get('report_download_generate', [ReportController::class, 'report_download_generate'])->name('report_download_generate');
    Route::get('report_download_lock', [ReportController::class, 'report_download_lock'])->name('report_download_lock');
    //admin.channels.create
    Route::resource('campaigns', CampaignController::class);


    Route::get('technicians-active/{id}', [TechnicianController::class, 'activeTechnicians'])->name('admin.technicians.active');
    Route::post('technicians-bulk-active', [TechnicianController::class, 'bulkActiveTechnicians'])->name('admin.technicians.bulkactive');
    Route::get('phone-verification-active/{id}', [TechnicianController::class, 'phoneVerification'])->name('admin.technicians.phone_verification');
    Route::resource('campaignCategories', App\Http\Controllers\CampaignCategoryController::class);

    Route::resource('settings', App\Http\Controllers\SettingsController::class, ['names' => 'admin.settings']);
    Route::resource('feedback', App\Http\Controllers\Backend\FeedbackController::class, ['names' => 'admin.feedback']);
    Route::resource('learnings', \App\Http\Controllers\Backend\LearningController::class, ['names' => 'admin.learnings']);
    Route::resource('offers', \App\Http\Controllers\Backend\OfferController::class, ['names' => 'admin.offers']);
    Route::get('feedback-reply/{id}', [App\Http\Controllers\Backend\FeedbackController::class, 'reply']);
    Route::get('earn-vs-settlement-report', [App\Http\Controllers\Backend\HomeController::class, 'earnVSSettlementReport']);
    Route::resource('notification', App\Http\Controllers\Backend\NotificationController::class);

      // Show SMS send form + template selection
    Route::get('sms', [App\Http\Controllers\Backend\SmsController::class, 'index'])
        ->name('admin.sms.index');  // <-- add admin. prefix in name

    // Process SMS sending (manual or bulk via CSV)
    Route::post('sms/send', [App\Http\Controllers\Backend\SmsController::class, 'send'])
        ->name('admin.sms.send');   // <-- add admin. prefix in name

    // View SMS logs
    Route::get('sms/logs', [App\Http\Controllers\Backend\SmsController::class, 'logs'])
        ->name('admin.sms.logs');   // <-- add admin. prefix in name 
        
    Route::get('technician-nominee/{userId}', [TechnicianNomineeController::class, 'index']); 
    Route::post('technician-nominee/store', [TechnicianNomineeController::class, 'store']);
    Route::put('technician-nominee/update/{id}', [TechnicianNomineeController::class, 'update']); 
    Route::delete('technician-nominee/delete/{id}', [TechnicianNomineeController::class, 'destroy']);

    Route::resource('gift-policies', GiftPolicyController::class, ['names' => 'admin.gift-policies']);
    Route::resource('gifts', GiftController::class, ['names' => 'admin.gifts']);


    Route::prefix('gift')->name('admin.gift.')->group(function () {

    Route::get('/transactions', [GiftTransactionController::class, 'index'])
        ->name('transactions.index');

    Route::post('/request', [GiftTransactionController::class, 'store'])
        ->name('transactions.store');

    Route::post('/approve/{id}', [GiftTransactionController::class, 'approve'])
        ->name('transactions.approve');

    Route::post('/reject/{id}', [GiftTransactionController::class, 'reject'])
        ->name('transactions.reject');

    Route::post('/send/{id}', [GiftTransactionController::class, 'send'])
        ->name('transactions.send');

    Route::post('/received/{id}', [GiftTransactionController::class, 'received'])
        ->name('transactions.received');

    Route::get('/show/{id}', [GiftTransactionController::class, 'show'])
        ->name('transactions.show');
});
});


Route::get('/district-update-division', [UserController::class, 'districtUpdateDivisionWise']);

// =================frontend=================


Route::get('/verify/{unique_code?}', [CheckCodeController::class, 'checkCodeURL'])->name('checkCodeURL');
Route::post('/verify/validate', [CheckCodeController::class, 'checkCodeURLValidate'])->name('checkCodeURLValidate');
Route::get('/verify/success/{unique_code}', [CheckCodeController::class, 'checkCodeURLValidateSuccess'])->name('checkCodeURLValidate.success');
Route::get('/verify/fail/{unique_code}', [CheckCodeController::class, 'checkCodeURLValidateFail'])->name('checkCodeURLValidate.fail');
Route::get('/verify/already_check/{unique_code}', [CheckCodeController::class, 'alreadyCheck'])->name('checkCodeURLValidate.already_check');


Route::get('user_redeem_requests_storess', [App\Http\Controllers\Api\UserRedeemRequestAPIController::class, 'storess']);
// ================frontend=================

//Clear Cache facade value:
Route::get('/clear-all', function () {
    $exitCode = Artisan::call('cache:clear');
    echo '<h1>Cache facade value cleared</h1>';
    $exitCode = Artisan::call('optimize');
    echo  '<h1>Reoptimized class loader</h1>';
    $exitCode = Artisan::call('route:cache');
    echo  '<h1>Routes cached</h1>';
    $exitCode = Artisan::call('route:clear');
    echo  '<h1>Route cache cleared</h1>';
    $exitCode = Artisan::call('view:clear');
    echo  '<h1>View cache cleared</h1>';
    $exitCode = Artisan::call('config:cache');
    echo  '<h1>Clear Config cleared</h1>';
});
