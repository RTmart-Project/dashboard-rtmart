<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PpobController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth']], function () {
    // Home
    Route::get('/home', [HomeController::class, 'home'])->name('home');

    // PPOB
    Route::get('/ppob/topup', [PpobController::class, 'topup'])->name('ppob');
    Route::get('/ppob/topup/get', [PpobController::class, 'getTopups'])->name('ppob.getTopups');
    Route::get('/ppob/topup/get/{topupStatus}', [PpobController::class, 'getTopupByStatus'])->name('ppob.getTopupByStatus');
    Route::post('/ppob/topup/confirm/{topupId}', [PpobController::class, 'confirmTopup'])->name('ppob.confirmTopup');
    Route::get('/ppob/topup/cancel/{topupId}', [PpobController::class, 'cancelTopup'])->name('ppob.cancelTopup');
    Route::get('/ppob/transaction', [PpobController::class, 'transaction'])->name('transaction');
    Route::get('/ppob/transaction/get', [PpobController::class, 'getTransactions'])->name('ppob.getTransactions');
    Route::get('/ppob/merchant/get', [PpobController::class, 'getActiveMerchant'])->name('ppob.activeMerchant');

    // Merchant
    Route::get('/merchant/account', [MerchantController::class, 'account'])->name('merchant.account');
    Route::get('/merchant/account/get', [MerchantController::class, 'getAccounts'])->name('merchant.getAccounts');
    Route::get('/merchant/otp', [MerchantController::class, 'otp'])->name('merchant.otp');
    Route::get('/merchant/otp/get', [MerchantController::class, 'getOtps'])->name('merchant.getOtps');
    Route::get('/merchant/restock', [MerchantController::class, 'restock'])->name('merchant.restock');
    Route::get('/merchant/restock/get', [MerchantController::class, 'getRestocks'])->name('merchant.getRestocks');
    Route::get('/merchant/restock/detail/{stockOrderId}', [MerchantController::class, 'restockDetails'])->name('merchant.restockDetails');
    Route::get('/merchant/restock/detail/get/{stockOrderId}', [MerchantController::class, 'getRestockDetails'])->name('merchant.getRestockDetails');

    // Customer
    Route::get('/customer/account', [CustomerController::class, 'account'])->name('customer.account');
    Route::get('/customer/account/get', [CustomerController::class, 'getAccounts'])->name('customer.getAccounts');
    Route::get('/customer/otp', [CustomerController::class, 'otp'])->name('customer.otp');
    Route::get('/customer/otp/get', [CustomerController::class, 'getOtps'])->name('customer.getOtps');
    Route::get('/customer/transaction', [CustomerController::class, 'transaction'])->name('customer.transaction');
    Route::get('/customer/transaction/get', [CustomerController::class, 'getTransactions'])->name('customer.getTransactions');
    Route::get('/customer/transaction/detail/{orderId}', [CustomerController::class, 'transactionDetails'])->name('customer.transactionDetails');
    Route::get('/customer/transaction/detail/get/{orderId}', [CustomerController::class, 'getTransactionDetails'])->name('customer.getTransactionDetails');

    // Check Role
    Route::group(['middleware' => ['checkRoleUser:IT']], function () {
        // Setting
        Route::get('/setting/users', [AuthController::class, 'users'])->name('setting.users');
        Route::get('/setting/users/get', [AuthController::class, 'getUsers'])->name('setting.getUsers');
        Route::get('/setting/users/new', [AuthController::class, 'newUser'])->name('setting.newUser');
        Route::post('/setting/users/create', [AuthController::class, 'createNewUser'])->name('setting.createNewUser');
        Route::get('/setting/users/edit/{user}', [AuthController::class, 'editUser'])->name('setting.editUser');
        Route::post('/setting/users/update/{user}', [AuthController::class, 'updateUser'])->name('setting.updateUser');
        Route::get('/setting/users/reset-password/{user}', [AuthController::class, 'resetPassword'])->name('setting.resetPassword');
    });

    // Distributor
    Route::get('/distributor/account/get', [DistributorController::class, 'getAccounts'])->name('distributor.getAccounts');

    // Payment Method
    Route::get('/payment/method/get', [PaymentMethodController::class, 'getPaymentMethods'])->name('payment.getPaymentMethods');

    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::group(['middleware' => ['guest']], function () {
    // Login
    Route::get('/', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/', [AuthController::class, 'validateLogin'])->name('auth.validateLogin');
});
