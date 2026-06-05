<?php

use Illuminate\Support\Facades\Route;
use Modules\HelloWorld\Http\Controllers\HelloWorldController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('helloworlds', HelloWorldController::class)->names('helloworld');
});
