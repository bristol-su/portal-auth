<?php

use Illuminate\Support\Facades\Route;

/**
 * Register the routes needed for authorization.
 *
 * @return void
 */
    Route::group(['middleware' => ['web', 'portal-auth']], function () {
        Route::get('/authorize', [\Laravel\Passport\Http\Controllers\AuthorizationController::class, 'authorize'])->name('passport.authorizations.authorize');
        Route::post('/authorize', [\Laravel\Passport\Http\Controllers\ApproveAuthorizationController::class, 'approve'])->name('passport.authorizations.approve');
        Route::delete('/authorize', [\Laravel\Passport\Http\Controllers\DenyAuthorizationController::class, 'deny'])->name('passport.authorizations.deny');
    });

/**
 * Register the routes for retrieving and issuing access tokens.
 *
 * @return void
 */
    Route::middleware('throttle')->post('/token', [\Laravel\Passport\Http\Controllers\AccessTokenController::class, 'issueToken'])->name('passport.token');

    Route::group(['middleware' => ['web', 'portal-auth']], function () {
        Route::get('/tokens', [\Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController::class, 'forUser'])->name('passport.tokens.index');
        Route::delete('/tokens/{token_id}', [\Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController::class, 'destroy'])->name('passport.tokens.destroy');
    });

/**
 * Register the routes needed for refreshing transient tokens.
 *
 * @return void
 */
    Route::middleware(['web', 'portal-auth'])->post('/token/refresh', [\Laravel\Passport\Http\Controllers\TransientTokenController::class, 'refresh'])->name('passport.token.refresh');
