<?php

use App\Http\Controllers\Api\SourceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('sources')->group(function () {
    Route::controller(SourceController::class)->group(function () {
        Route::get('/existing', 'index');
        Route::get('/search/{name}', 'search');
        Route::get('/get-requirements/{id}', 'getConnectorRequirements');
    });

});


Route::post('/createavailablesource',[SourceController::class,'createAvailableSource'])->middleware('Validation:Source');
