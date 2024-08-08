<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SourceController;
use App\Http\Controllers\Api\SourceTemplateController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('sources')->group(function () {
    Route::controller(SourceController::class)->group(function () {
        Route::get('/get-existing-sources', 'index');
        Route::get('/get-sources', 'getAvailableSources');
        Route::get('/search/{name}', 'search')->whereAlphaNumeric('name');
        Route::post('/test-connection/{id}', 'testConnection')->middleware('Validation:Database');
        Route::post('/create-source/{id}', 'createSource')->middleware('Validation:Database');
    });
    Route::controller(SourceTemplateController::class)->group(function () {
        Route::get('/get-requirements-dynamic/{id}', 'getConnectorRequirements')->whereAlphaNumeric('id');
        Route::get('/get-requirements-static/{id}','getTemplateFromDatabase')->whereAlphaNumeric('id');
});
});
