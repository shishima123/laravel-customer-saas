<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\User\CustomerController as UserCustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::group(['middleware' => ['auth', 'activated']], function () {
    Route::get('/customer/first-login', [UserCustomerController::class, 'firstLoginGet'])->name('first-login-get');
    Route::post('/customer/first-login', [UserCustomerController::class, 'firstLoginPost'])->name('first-login-post');

    Route::controller(SystemController::class)
        ->name('systems.')
        ->group(function () {
            Route::get('/admin/account', 'adminAccountGet')->name('admin-account-get');
            Route::post('/admin/account/{user}', 'changePasswordPost')->name('admin-change-password-post');
        });

    Route::group(['middleware' => ['changed.password']], function () {
        Route::controller(UserCustomerController::class)
            ->name('customers.')
            ->prefix('customers')
            ->group(function () {
            Route::get('/setup-information', 'setupInformationGet')->name('setup-information');
            Route::put('/setup-information', 'setupInformationPut')->name('setup-information-put');
        });

        Route::group(['middleware' => ['changed.info']], function () {
            Route::get('/', function () {
                return redirect()->route('customers.index');
            });

            Route::controller(CustomerController::class)
                ->name('customers.')
                ->prefix('customers')
                ->group(function () {
                    Route::post('/update-status/{user}', 'updateStatus')->name('update-status');
                    Route::get('/{customer}/account', 'changePasswordGet')->name('account');
                    Route::post('/{customer}/account', 'changePasswordPost');
                    Route::get('/{customer}/payment-history', 'paymentHistory')
                        ->name('payment-history');
                    Route::get('/unsubscription', 'unsubscriptionPlanGet')
                        ->name('unsubscription-plan-get');
                    Route::get('/profile', 'profile')->name('profile');
                    Route::get('/{customer}/invoice/{invoice}/detail', 'getInvoiceDetail')
                        ->name('invoice.detail');
                    Route::get('/{customer}/invoice/{invoice}/download', 'invoiceDownload')
                        ->name('invoice.download');
                });
            Route::resource('customers', CustomerController::class)->except('create', 'destroy');

            Route::controller(PaymentController::class)
                ->name('payments.')
                ->prefix('payments')
                ->group(function () {
                    Route::get('/resume', 'resumeWithNewCardForm')->name('resume-with-new-card');
                    Route::post('/{customer}/cancel/', 'cancel')->name('cancel');
                    Route::post('/{customer}/resume/', 'resume')->name('resume');
                });
            Route::resource('payments', PaymentController::class)->except('show', 'edit', 'update');
        });
    });
});

Route::get('/language/{lang}', [SystemController::class, 'changeLanguage'])->name('language');
