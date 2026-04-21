<?php
/*
use Illuminate\Support\Facades\Route;
use MobicardApi\ScanApi\Http\Controllers\ScanApiController;

$prefix = config('scanapi.routes.prefix', 'mobicard');
$middleware = config('scanapi.routes.middleware', ['web', 'scanapi.config']);

Route::group([
    'prefix' => $prefix,
    'middleware' => $middleware
], function () {
    Route::get('scan', [ScanApiController::class, 'index'])->name('scanapi.scan');
    Route::post('broker/scan', [ScanApiController::class, 'scanCard'])->name('scanapi.broker.scan');
    Route::post('broker/upload', [ScanApiController::class, 'uploadCard'])->name('scanapi.broker.upload');
});
*/


use Illuminate\Support\Facades\Route;
use MobicardApi\ScanApi\Http\Controllers\ScanApiController;

$prefix = config('scanapi.routes.prefix', 'mobicard');

Route::group([
    'prefix' => $prefix,
    'middleware' => ['web'] // Only use web middleware
], function () {
    Route::get('scan', [ScanApiController::class, 'index'])->name('scanapi.scan');
    Route::post('broker/scan', [ScanApiController::class, 'scanCard'])->name('scanapi.broker.scan');
    Route::post('broker/upload', [ScanApiController::class, 'uploadCard'])->name('scanapi.broker.upload');
});
