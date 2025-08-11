<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // event(new  \App\Events\MessageSent('Hello dari route /'));
    /*\App\Events\MessageSent::dispatch("Hello dari router / ");*/
    return view('welcome');
});


Route::post('/send-message', function (\Illuminate\Http\Request $request) {
    \App\Events\MessageSent::dispatch($request->message);
})->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
