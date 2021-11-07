<?php

use App\Http\Klick\Controllers\Auth\PleskTeamUserVerificationSuccess;
use App\Http\Klick\Controllers\Auth\VerificationController;
use App\Http\Klick\Controllers\CancelRemindersForEventController;
use App\Http\Klick\Controllers\ExtendTrialController;
use App\Http\Klick\Controllers\MarketingController;
use App\Http\Klick\Controllers\MuteCommentsForRecipientController;
use App\Http\Klick\Controllers\MuteRecipientController;
use App\Http\Klick\Controllers\Recipient\RecipientUnsubscribeDailySummaryController;
use App\Http\Klick\Controllers\Recipient\RecipientVerificationController;
use App\Http\Klick\Controllers\TeamMember\TeamMemberInviteVerificationController;
use Illuminate\Support\Facades\Route;

/*
 * NOTE: All routes in here need to be signed using Laravel's signed url generator as there
 * is no authentication setup. There is middleware configured for all routes on klick to
 * check the signature is valid, and will prevent url manipulation on these public urls.
 */
Route::middleware('signed')->group(function () {
    Route::get('/register/verify/{user}/{id}/{hash}', VerificationController::class)
        ->name('register.email.verify');

    Route::get('/recipient/verify/{recipient}/{token}', RecipientVerificationController::class)
        ->name('recipient.email.verify');

    Route::get('/team-member/verify/{teamMember}/{token}', TeamMemberInviteVerificationController::class)
        ->name('team-member.email.verify');

    Route::get('/verification-success/plesk/{user}', PleskTeamUserVerificationSuccess::class)
        ->name('register.email.success.plesk');

    Route::post('/marketing/unsubscribe/{user}/{team}', [MarketingController::class, 'update'])
        ->name('unsubscribe.update');

    Route::get('/extend-trial/{user}', ExtendTrialController::class)->name('extend-trial');

    Route::post('/mute/recipient/{recipient}/{event}', [MuteRecipientController::class, 'update'])
        ->name('mute-recipient.update');

    Route::get('/mute/comments/{recipient}', [MuteCommentsForRecipientController::class, 'edit'])
        ->name('mute-comments.edit');
    Route::post('/mute/comments/{recipient}', [MuteCommentsForRecipientController::class, 'update'])
        ->name('mute-comments.update');
});

/**
 * The following urls are not signed, but only link to forms, which ultimately post to signed
 * endpoints defined above, or in themselves have very limited scope.
 */
Route::get('/marketing/unsubscribe/{user}/{team}', [MarketingController::class, 'edit'])
    ->name('unsubscribe.edit');

Route::get('/mute/recipient/{recipient}/{event}', [MuteRecipientController::class, 'edit'])
    ->name('mute-recipient.edit');

Route::get('/reminder/mute/{recipient}/{event}', CancelRemindersForEventController::class)
    ->name('reminder-mute');

Route::get('/summary/unsubscribe/{recipient}', RecipientUnsubscribeDailySummaryController::class)
    ->name('recipient.unsubscribe');
