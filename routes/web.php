<?php


use Illuminate\Routing\Route;

//Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
//Route::post('login', [LoginController::class, 'login']);
//Route::post('logout', [LoginController::class, 'logout'])->name('logout');
//
//// Registration Routes...
//Route::get('register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
//Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
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