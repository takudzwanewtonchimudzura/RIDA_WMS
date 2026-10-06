<?php
use App\Http\Controllers\Api\BoreholeApiController;

Route::get('boreholes', [BoreholeApiController::class, 'index']);
Route::post('boreholes', [BoreholeApiController::class, 'store']);
Route::get('boreholes/{borehole}', [BoreholeApiController::class, 'show']);