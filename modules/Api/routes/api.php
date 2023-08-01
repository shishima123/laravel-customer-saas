<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Api\App\Http\Controllers\Auth\LoginController;
use Modules\Api\App\Http\Controllers\CustomerController;
use Modules\Api\App\Http\Controllers\PaymentController;
use Modules\Api\App\Http\Controllers\SystemController;

Route::middleware('api')->prefix('api')->name('api.')
    ->group(function () {
        Route::group(['middleware' => ['auth:sanctum', 'activated']], function () {
            Route::get('/user', function (Request $request) {
                return $request->user();
            });

            Route::controller(SystemController::class)
                ->name('systems.')
                ->group(function () {
                    Route::post('/admin/account/change-password', 'changePasswordPost')->name('admin-change-password-post');
                });

            Route::post('/customer/first-login', [CustomerController::class, 'firstLoginPost'])->name('first-login-post');
            Route::group(['middleware' => ['changed.password', 'changed.info']], function () {
                Route::controller(CustomerController::class)
                    ->name('customers.')
                    ->prefix('customers')
                    ->group(function () {
                        Route::put('/setup-information', 'setupInformationPut')->name('setup-information-put')
                            ->withoutMiddleware('changed.info');
                        Route::post('/update-status/{user}', 'updateStatus')->name('update-status');
                        Route::post('/{customer}/account', 'changePasswordPost');
                        Route::get('/{customer}/payment-history', 'paymentHistory')
                            ->name('payment-history');
                        Route::get('/{customer}/invoice/{invoice}/detail', 'getInvoiceDetail')
                            ->name('invoice.detail');
                        Route::get('/{customer}/invoice/{invoice}/download', 'invoiceDownload')
                            ->name('invoice.download');
                    });
                Route::apiResource('customers', CustomerController::class)->except('destroy');

                Route::controller(PaymentController::class)
                    ->name('payments.')
                    ->prefix('payments')
                    ->group(function () {
                        Route::post('/get-setup-intent', 'getSetupIntent')->name('get-setup-intent');
                        Route::post('/{customer}/cancel/', 'cancel')->name('cancel');
                        Route::post('/{customer}/resume/', 'resume')->name('resume');
                    });
                Route::apiResource('payments', PaymentController::class)->except('index', 'update', 'show');
            });
        });

        // Auth Route
        Route::post('/register', [RegisterController::class, 'register'])->name('register');
        Route::controller(LoginController::class)->group(function () {
            Route::post('/login', 'login')->name('login');
            Route::post('/logout', 'logout')->name('logout')
                ->middleware('auth:sanctum');
        });
        Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.email');
    });
