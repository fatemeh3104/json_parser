<?php

use Illuminate\Support\Facades\Route;
use ProcessMaker\Http\Controllers\Api\ScreenController;
use ProcessMaker\Http\Controllers\TaskController;
use ProcessMaker\Package\Parssconfig\Http\Controllers\ParssconfigController;
use ProcessMaker\Package\Parssconfig\Http\Middleware\ValidationItems;
use processmaker\parssconfig\Http\Controllers\ParserConfigController;

Route::group([],function (){
    Route::get('/ParserConfig ',[ParserConfigController::class,'index']);
    Route::get('/parserFetch/{screen_id} ',[ParserConfigController::class,'fetch']);
    Route::get('ParserConfig/search/{value}',[ParserConfigController::class,'search']);
    Route::get('ParserConfig/all',[ParserConfigController::class,'all']);
    Route::post('ParserConfig/store',[ParserConfigController::class,'store']);
    Route::get('ParserConfig/ShowConditionalHide',[ParserConfigController::class,'ShowConditionalHide']);
});
Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update')->middleware('ValidationItems');
Route::put('screens/{screen}', [ScreenController::class, 'update'])->name('screens.update')->middleware(['ValidationUpdate']);

