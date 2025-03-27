<?php

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\SalesController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('login',[LoginController::class, 'login'])->name('login');
Route::post('register',[LoginController::class, 'register'])->name('register');

Route::middleware('api_token')->group(function(){
    Route::apiResource('sales', SalesController::class)->middleware('verify_admin');
    Route::get('/user',function(){
        // $user = User::all();
        return response()->json(Auth::user());
    })->middleware('api_token');
});
