<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/** Home Route */
Route::get('/', function(){
    return redirect()->route('admin.login');
});

Route::get('cache-clear', function(){
    Artisan::call('cache:clear');
    return "Cache cleared successfully.";
});

Route::get('route-clear', function(){
    Artisan::call('route:clear');
    return "Route cache cleared successfully.";
});

Route::get('optimize-clear', function(){
    Artisan::call('optimize:clear');
    return "Opimize cleared successfully.";
});

Route::get('config-clear', function(){
    Artisan::call('config:clear');
    return "Configuration cleared successfully.";
});
