<?php


use BristolSU\Auth\Http\Controllers\Auth\ForgotPasswordController;
use BristolSU\Auth\Http\Controllers\Auth\LoginController;
use BristolSU\Auth\Http\Controllers\Auth\ConfirmPasswordController;
use BristolSU\Auth\Http\Controllers\Auth\LogoutController;
use BristolSU\Auth\Http\Controllers\Auth\RegisterController;
use BristolSU\Auth\Http\Controllers\Auth\ResetPasswordController;
use BristolSU\Auth\Http\Controllers\Auth\VerifyEmailController;
use BristolSU\Auth\Social\Http\Controllers\SocialLoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('portal-guest')->group(function() {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.action');

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register'])->name('register.action');

    Route::middleware('socialite')->group(function() {
        Route::get('/login/social/{driver}', [SocialLoginController::class, 'redirect'])->name('social.login');
        Route::get('/login/social/{driver}/callback', [SocialLoginController::class, 'callback'])->name('social.callback');
    });

    Route::get('/password/forgot', [ForgotPasswordController::class, 'showForm'])->name('password.forgot');
    Route::middleware('portal-throttle:3,1')
        ->post('/password/forgot', [ForgotPasswordController::class, 'sendResetLink'])->name('password.forgot.action');

    Route::middleware(['link', 'portal-throttle:3,1'])->group(function() {
        Route::get('/password/reset', [ResetPasswordController::class, 'showForm'])->name('password.reset');
        Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset.action');
    });

});

// Without verification
Route::middleware([\BristolSU\Support\Authentication\Middleware\IsAuthenticated::class])->group(function() {

    Route::name('logout')->post('logout', [LogoutController::class, 'logout']);


    Route::middleware(['portal-not-verified'])->group(function() {
        Route::get('verify', [VerifyEmailController::class, 'showVerifyPage'])->name('verify.notice');
        Route::middleware('link')->get('verify/authorize', [VerifyEmailController::class, 'verify'])->name('verify');
        Route::middleware('portal-throttle:3')->post('verify/resend', [VerifyEmailController::class, 'resend'])->name('verify.resend');
    });
});

Route::middleware(['portal-auth'])->group(function() {
    Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmationPage'])->name('password.confirmation.notice');
    Route::middleware('portal-throttle:5,1')->post('password/confirm', [ConfirmPasswordController::class, 'confirm'])->name('password.confirmation');
});
