<?php

use Illuminate\Support\Facades\Route;

Route::namespace('EvansKim\\GnuMigration\\Controller')
    ->middleware(['web'])
    ->name("gnu.")
    ->prefix('bbs')->group(function(){
    Route::post("/login.php", "LoginController@login");
    Route::post("/logout.php", "LoginController@logout");
    Route::post("/password_lost2.php", "ForgotPasswordController@sendResetLinkEmail")->name("password.email");
    Route::post("/register_form_update.php", "RegisterController@register");
});