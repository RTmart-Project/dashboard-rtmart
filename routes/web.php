<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PpobController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
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

    // Product List
    Route::get('/master/product/list', [ProductController::class, 'list'])->name('product.list');
    Route::get('/master/product/list/get', [ProductController::class, 'getLists'])->name('product.getLists');

    // Product Category
    Route::get('/master/product/category', [ProductController::class, 'category'])->name('product.category');
    Route::get('/master/product/category/get', [ProductController::class, 'getCategories'])->name('product.getCategories');
    Route::get('/master/product/category/add', [ProductController::class, 'addCategory'])->name('product.addCategory');
    Route::post('/master/product/category/insert', [ProductController::class, 'insertCategory'])->name('product.insertCategory');

    // Product UOM
    Route::get('/master/product/uom', [ProductController::class, 'uom'])->name('product.uom');
    Route::get('/master/product/uom/get', [ProductController::class, 'getUoms'])->name('product.getUoms');
    Route::get('/master/product/uom/add', [ProductController::class, 'addUom'])->name('product.addUom');
    Route::post('/master/product/uom/insert', [ProductController::class, 'insertUom'])->name('product.insertUom');

    // Product Type
    Route::get('/master/product/type', [ProductController::class, 'type'])->name('product.type');
    Route::get('/master/product/type/get', [ProductController::class, 'getTypes'])->name('product.getTypes');
    Route::get('/master/product/type/add', [ProductController::class, 'addType'])->name('product.addType');
    Route::post('/master/product/type/insert', [ProductController::class, 'insertType'])->name('product.insertType');

    // Product Brand
    Route::get('/master/product/brand', [ProductController::class, 'brand'])->name('product.brand');
    Route::get('/master/product/brand/get', [ProductController::class, 'getBrands'])->name('product.getBrands');
    Route::get('/master/product/brand/add', [ProductController::class, 'addBrand'])->name('product.addBrand');
    Route::post('/master/product/brand/insert', [ProductController::class, 'insertBrand'])->name('product.insertBrand');

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
