<?php


$controller = '\Helori\LaravelMedias\Controllers\MediasController';

Route::post('/api/media', ['uses' => $controller.'@create']);
Route::get('/api/media/{id?}', ['uses' => $controller.'@read']);
Route::put('/api/media/{id}', ['uses' => $controller.'@update']);
Route::delete('/api/media/{id}', ['uses' => $controller.'@delete']);
Route::get('/api/media/{id}/download', ['uses' => $controller.'@download']);
