<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Ooredoo\CriticalCasesP1\Http\Controllers\CriticalCasesP1Controller;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Route::get('/endpoint', function (Request $request) {
//     //
// });


Route::get('/shops', CriticalCasesP1Controller::class . '@index');
Route::get('/shops/{id}', CriticalCasesP1Controller::class . '@shopDetail');
Route::post('/start', CriticalCasesP1Controller::class . '@startDailyCheck');
Route::post('/file', CriticalCasesP1Controller::class . '@fileDailyCheck');
Route::post('/save-item', CriticalCasesP1Controller::class . '@saveDailyCheckItem');
Route::post('/item-create-issue', CriticalCasesP1Controller::class . '@createDailyCheckIssue');
Route::post('/file', CriticalCasesP1Controller::class . '@fileDailyCheck');
