<?php

Route::namespace('EvansKim\\GnuMigration\\Controller')
    ->middleware(['web'])
    ->prefix('bbs')->group(function(){
    Route::post("/login.php", "LoginController@login");
    Route::post("/logout.php", "LoginController@logout");
});