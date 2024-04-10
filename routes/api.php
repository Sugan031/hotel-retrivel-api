<?php

use App\Http\Controllers\HotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('search',[HotelController::class,'getValuesFromHotelMaster']);
Route::put('update',[HotelController::class,'UpdateValueToDb']);
Route::put('delete',[HotelController::class,'DeleteRowsFromDb']);
Route::post('reference',[HotelController::class,'getValuesFromDbForRef']);