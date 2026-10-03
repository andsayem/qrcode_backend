<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Lottery\LotteryController;
use App\Http\Controllers\Backend\Lottery\LotteryGiftAssignController;
use App\Http\Controllers\Backend\Lottery\LotteryGiftController;
use App\Http\Controllers\Backend\Lottery\LotteryWinnerController;

/*
|--------------------------------------------------------------------------
| Lottery Routes
|--------------------------------------------------------------------------
|
| Here is the lottery related routes for the application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

// Loaded at the end of web.php outside its auth group, so auth is applied here
Route::group(['as' => 'admin.', 'middleware' => 'auth'], function () {

    // Lottery Gifts
    Route::resource('lottery-gifts', LotteryGiftController::class);

    // Lotteries
    Route::resource('lotteries', LotteryController::class);

    // Gift assign page
    Route::get(
        'lotteries-gift-assign/{lottery}',
        [LotteryGiftAssignController::class, 'index']
    )->name('lottery-gift-assign.index');

    Route::post(
        'lotteries-gift-assign/{lottery}',
        [LotteryGiftAssignController::class, 'store']
    )->name('lottery-gift-assign.store');

    Route::delete(
        'lottery-gift-assign/{id}',
        [LotteryGiftAssignController::class, 'destroy']
    )->name('lottery-gift-assign.destroy');

    // Lottery Winners
    Route::resource('lottery-winners', LotteryWinnerController::class)
        ->only(['index', 'show', 'destroy'])
        ->parameters(['lottery-winners' => 'winner']);

    // Eligible technicians list and Excel export
    Route::get('lotteries/{lottery}/eligible', [LotteryController::class, 'eligible'])
        ->name('lotteries.eligible');

    Route::get('lotteries/{lottery}/eligible/export', [LotteryController::class, 'eligibleExport'])
        ->name('lotteries.eligible.export');

    // Draw page (GET)
    Route::get('lotteries/{lottery}/draw', [LotteryController::class, 'draw'])
        ->name('lotteries.draw');

    // Start lottery (pending -> running)
    Route::post('lotteries/{lottery}/start', [LotteryController::class, 'start'])
        ->name('lotteries.start');

    // Draw next winner (POST only, so a refresh or link cannot trigger a draw)
    Route::post('lotteries/{lottery}/draw-next', [LotteryController::class, 'drawNext'])
        ->name('lotteries.draw-next');
});