<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SourceController;
use App\Http\Controllers\Api\SourceTemplateController;




Route::prefix('sources')->group(function () {
    Route::controller(SourceController::class)->group(function () {
        Route::get('/get-existing-sources', 'index');
        Route::get('/get-sources', 'getAvailableSources');
        Route::post('/search/{name}', 'search')->whereAlphaNumeric('name');
        Route::middleware('Validation:SourceValidation')->group(function () {
            Route::post('/test-connection/{id}', 'testConnection');
            Route::post('/create-source/{id}', 'createSource');
        });
    });
    Route::controller(SourceTemplateController::class)->group(function () {
        Route::get('/get-requirements-dynamic/{id}', 'getConnectorRequirements')->whereAlphaNumeric('id');
        Route::get('/get-requirements-static/{id}', 'getTemplateFromDatabase')->whereAlphaNumeric('id');
    });
});
