<?php

use Illuminate\Support\Facades\Route;
use ProcessMaker\Package\Parssconfig\Http\Controllers\ParssconfigController;
use processmaker\parssconfig\Http\Controllers\ParserConfigController;

Route::group(['middleware' => ['auth:api', 'bindings']], function () {
    Route::get('admin/parssconfig/fetch', [ParssconfigController::class, 'fetch'])->name('package.skeleton.fetch');
    Route::apiResource('admin/parssconfig', ParssconfigController::class);
});
Route::group([],function (){
    Route::get('/ParserConfig ',[ParserConfigController::class,'index']);
    Route::get('/parserFetch/{screen_id} ',[ParserConfigController::class,'fetch']);
    Route::get('ParserConfig/search/{value}',[ParserConfigController::class,'search']);
    Route::get('ParserConfig/all',[ParserConfigController::class,'all']);
    Route::post('ParserConfig/store',[ParserConfigController::class,'store']);
});
