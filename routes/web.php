<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('admin/parssconfig', [ParssconfigController::class, 'index'])->name('package.skeleton.index');
    Route::get('parssconfig', [ParssconfigController::class, 'index'])->name('package.skeleton.tab.index');
});
