<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Ooredoo\DailyChecks\Http\Controllers\DailyCheckController;

Route::get('/shops', DailyCheckController::class . '@index');
Route::get('/shops/{id}', DailyCheckController::class . '@shopDetail');
Route::post('/start', DailyCheckController::class . '@startDailyCheck');
Route::post('/file', DailyCheckController::class . '@fileDailyCheck');
Route::post('/save-item', DailyCheckController::class . '@saveDailyCheckItem');
Route::post('/item-create-issue', DailyCheckController::class . '@createDailyCheckIssue');
Route::post('/file', DailyCheckController::class . '@fileDailyCheck');