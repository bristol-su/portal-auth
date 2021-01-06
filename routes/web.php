<?php


use BristolSU\Auth\Http\Controllers\Auth\LoginController;
use BristolSU\Auth\Http\Controllers\Auth\ConfirmPasswordController;
use BristolSU\Auth\Http\Controllers\Auth\RegisterController;
use BristolSU\Auth\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('portal-guest')->group(function() {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);


});

Route::middleware(['portal-auth', 'portal-not-verified'])->group(function() {
    Route::get('verify', [VerifyEmailController::class, 'showVerifyPage'])->name('verify.notice');
    Route::middleware('link')->get('verify/authorize', [VerifyEmailController::class, 'verify'])->name('verify');
    Route::middleware('portal-throttle:3')->post('verify/resend', [VerifyEmailController::class, 'resend'])->name('verify.resend');
});

Route::middleware(['portal-auth', 'portal-verified'])->group(function() {
    Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmationPage'])->name('password.confirmation.notice');
    Route::middleware('portal-throttle:5,1')->post('password/confirm', [ConfirmPasswordController::class, 'confirm'])->name('password.confirmation');
});



//Route::post('logout', [LoginController::class, 'logout'])->name('logout');
//
//// Email Verification Routes...
//Route::middleware('auth')->group(function() {
//    Route::get('email/verify', [\App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
//    Route::post('email/resend', [\App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');
//});
//Route::middleware('link')->get('email/verify/authorize', [\App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');

//
// Password Reset Routes...
// Show the forgot password form
//Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
//Route::post('password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
//Route::get('password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
//Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
//
//Route::get('password/confirm', 'Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
//Route::post('password/confirm', 'Auth\ConfirmPasswordController@confirm');
