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

    Route::get("/board.php", 'Board\\GnuBoardPostController@index');

    Route::get("/board.php/{any}", function(){
        return view("gnu::board-index", ['user'=>auth()->user()]);
    })->where('any', '.*');
});

Route::namespace('EvansKim\\GnuMigration\\Controller\\Board')
    ->middleware(['web'])
    ->name("gnu.api.")
    ->prefix('api/g4')->group(function(){

        Route::get("/board/{board}", 'GnuBoardController@show');
        Route::get("/board/{board}/post", "GnuBoardPostController@index");
        Route::post("/board/{board}/post", "GnuBoardPostController@store");
        Route::get("/board/{board}/post/{post_id}", "GnuBoardPostController@show");
        Route::put("/board/{board}/post/{post_id}", "GnuBoardPostController@update");
        Route::delete("/board/{board}/post/{post_id}", "GnuBoardPostController@destroy");

        Route::get("/board/{board_id}/post/{post_id}/comment", "GnuBoardPostCommentController@index");
        Route::post("/board/{board_id}/post/{post_id}/comment", "GnuBoardPostCommentController@store");
        Route::delete("/board/{board_id}/post/{post_id}/comment/{comment_id}", "GnuBoardPostCommentController@destroy");

    });

