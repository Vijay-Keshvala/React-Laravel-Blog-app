<?php 
use App\Http\Controllers\BlogController;
use App\Http\Controllers\TempImgController;
use Illuminate\Support\Facades\Route;

Route::get('blogs', [BlogController::class, 'index']);
Route::post('blogs', [BlogController::class, 'store']);
Route::post('save-temp-img', [TempImgController::class, 'store']);
Route::get('blogs/{id}', [BlogController::class, 'show']);
Route::put('blogs/{id}', [BlogController::class, 'update']);
Route::delete('blogs/{id}', [BlogController::class, 'destroy']);