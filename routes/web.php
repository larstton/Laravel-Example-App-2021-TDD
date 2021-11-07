<?php

use Illuminate\Support\Facades\Route;

/*
 * These route takes care of generating the urls which point to the email verification success
 * and failure page in the frontend code. There is no functionality in the api other than
 * generation of the url.
 */
Route::get('/register/verification?verified=true&email={email}&utm_source={utm_source}&utm_medium={utm_medium}&utm_campaign={utm_campaign}&utm_content={utm_content}', fn () => true)
    ->name('register.email.verified.success');
Route::get('/register/verification?verified=false', fn () => true)
    ->name('register.email.verified.fail');

Route::get('/password/reset/{email}/{token}', fn () => true)->name('password.reset');

/*
 * Use this to redirect the user to the frontend login page to authenticate with the api.
 */
Route::get('/auth/{email?}', fn () => true)->name('login');

/*
 * Directs to frontend page for new team member to complete joining process. Will be a signed url.
 */
Route::get('/join-team/{email}/{id}', fn () => true)->name('team.join');

Route::get('/', fn () => true)->name('homepage');
Route::get('/hosts/create', fn () => true)->name('host.create');
Route::get('/hosts/{host}', fn () => true)->name('host.show');
Route::get('/frontmen', fn () => true)->name('frontmen');

Route::name('status-pages.')->domain(config('app.status_page_url'))->group(function () {
    Route::get('/{token}', fn () => true)->name('show');
});
