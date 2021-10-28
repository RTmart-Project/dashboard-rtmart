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
use Illuminate\Support\Facades\Auth;

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

    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI,AH']], function () {
        // Product List
        Route::get('/master/product/list', [ProductController::class, 'list'])->name('product.list');
        Route::get('/master/product/list/get', [ProductController::class, 'getLists'])->name('product.getLists');
        Route::get('/master/product/list/add', [ProductController::class, 'addList'])->name('product.addList');
        Route::post('/master/product/list/insert', [ProductController::class, 'insertList'])->name('product.insertList');
        Route::get('/master/product/list/edit/{product}', [ProductController::class, 'editList'])->name('product.editList');
        Route::post('/master/product/list/update/{product}', [ProductController::class, 'updateList'])->name('product.updateList');
    });

    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI']], function () {
        // Product Category
        Route::get('/master/product/category', [ProductController::class, 'category'])->name('product.category');
        Route::get('/master/product/category/get', [ProductController::class, 'getCategories'])->name('product.getCategories');
        Route::get('/master/product/category/add', [ProductController::class, 'addCategory'])->name('product.addCategory');
        Route::post('/master/product/category/insert', [ProductController::class, 'insertCategory'])->name('product.insertCategory');
        Route::get('/master/product/category/edit/{category}', [ProductController::class, 'editCategory'])->name('product.editCategory');
        Route::post('/master/product/category/update/{category}', [ProductController::class, 'updateCategory'])->name('product.updateCategory');
    });

    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI']], function () {
        // Product UOM
        Route::get('/master/product/uom', [ProductController::class, 'uom'])->name('product.uom');
        Route::get('/master/product/uom/get', [ProductController::class, 'getUoms'])->name('product.getUoms');
        Route::get('/master/product/uom/add', [ProductController::class, 'addUom'])->name('product.addUom');
        Route::post('/master/product/uom/insert', [ProductController::class, 'insertUom'])->name('product.insertUom');
        Route::get('/master/product/uom/edit/{uom}', [ProductController::class, 'editUom'])->name('product.editUom');
        Route::post('/master/product/uom/update/{uom}', [ProductController::class, 'updateUom'])->name('product.updateUom');
    });

    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI']], function () {
        // Product Type
        Route::get('/master/product/type', [ProductController::class, 'type'])->name('product.type');
        Route::get('/master/product/type/get', [ProductController::class, 'getTypes'])->name('product.getTypes');
        Route::get('/master/product/type/add', [ProductController::class, 'addType'])->name('product.addType');
        Route::post('/master/product/type/insert', [ProductController::class, 'insertType'])->name('product.insertType');
        Route::get('/master/product/type/edit/{type}', [ProductController::class, 'editType'])->name('product.editType');
        Route::post('/master/product/type/update/{type}', [ProductController::class, 'updateType'])->name('product.updateType');
    });

    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI']], function () {
        // Product Brand
        Route::get('/master/product/brand', [ProductController::class, 'brand'])->name('product.brand');
        Route::get('/master/product/brand/get', [ProductController::class, 'getBrands'])->name('product.getBrands');
        Route::get('/master/product/brand/add', [ProductController::class, 'addBrand'])->name('product.addBrand');
        Route::post('/master/product/brand/insert', [ProductController::class, 'insertBrand'])->name('product.insertBrand');
        Route::get('/master/product/brand/edit/{brand}', [ProductController::class, 'editBrand'])->name('product.editBrand');
        Route::post('/master/product/brand/update/{brand}', [ProductController::class, 'updateBrand'])->name('product.updateBrand');
    });

    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI,AH']], function () {
        // PPOB
        Route::get('/ppob/topup', [PpobController::class, 'topup'])->name('ppob.topup');
        Route::get('/ppob/topup/get', [PpobController::class, 'getTopups'])->name('ppob.getTopups');
        Route::get('/ppob/topup/get/{topupStatus}', [PpobController::class, 'getTopupByStatus'])->name('ppob.getTopupByStatus');
        Route::post('/ppob/topup/confirm/{topupId}', [PpobController::class, 'confirmTopup'])->name('ppob.confirmTopup');
        Route::get('/ppob/topup/cancel/{topupId}', [PpobController::class, 'cancelTopup'])->name('ppob.cancelTopup');
        Route::get('/ppob/transaction', [PpobController::class, 'transaction'])->name('ppob.transaction');
        Route::get('/ppob/transaction/get', [PpobController::class, 'getTransactions'])->name('ppob.getTransactions');
        Route::get('/ppob/merchant/get', [PpobController::class, 'getActiveMerchant'])->name('ppob.activeMerchant');
    });

    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI,AH,HR']], function () {
        // Distributor
        Route::get('/distributor/account', [DistributorController::class, 'account'])->name('distributor.account');
        Route::get('/distributor/account/get', [DistributorController::class, 'getAccounts'])->name('distributor.getAccounts');
        Route::get('/distributor/account/edit/{distributorId}', [DistributorController::class, 'editAccount'])->name('distributor.editAccount');
        Route::post('/distributor/account/update/{distributorId}', [DistributorController::class, 'updateAccount'])->name('distributor.updateAccount');
        Route::get('/distributor/account/product/{distributorId}', [DistributorController::class, 'productDetails'])->name('distributor.productDetails');
        Route::get('/distributor/account/product/get/{distributorId}', [DistributorController::class, 'getProductDetails'])->name('distributor.getProductDetails');
        Route::get('/distributor/account/product/edit/{distributorId}/{productId}/{gradeId}', [DistributorController::class, 'editProduct'])->name('distributor.editProduct');
        Route::post('/distributor/account/product/update/{distributorId}/{productId}/{gradeId}', [DistributorController::class, 'updateProduct'])->name('distributor.updateProduct');
        Route::get('/distributor/account/product/delete/{distributorId}/{productId}/{gradeId}', [DistributorController::class, 'deleteProduct'])->name('distributor.deleteProduct');

        // Merchant
        Route::get('/merchant/account', [MerchantController::class, 'account'])->name('merchant.account');
        Route::get('/merchant/account/get', [MerchantController::class, 'getAccounts'])->name('merchant.getAccounts');
        Route::get('/merchant/account/edit/{merchantId}', [MerchantController::class, 'editAccount'])->name('merchant.editAccount');
        Route::post('/merchant/account/update/{merchantId}', [MerchantController::class, 'updateAccount'])->name('merchant.updateAccount');
        Route::get('/merchant/account/product/{merchantId}', [MerchantController::class, 'product'])->name('merchant.product');
        Route::get('/merchant/account/product/get/{merchantId}', [MerchantController::class, 'getProducts'])->name('merchant.getProducts');
        Route::get('/merchant/account/product/edit/{merchantId}/{productId}', [MerchantController::class, 'editProduct'])->name('merchant.editProduct');
        Route::post('/merchant/account/product/update/{merchantId}/{productId}', [MerchantController::class, 'updateProduct'])->name('merchant.updateProduct');
        Route::get('/merchant/account/product/delete/{merchantId}/{productId}', [MerchantController::class, 'deleteProduct'])->name('merchant.deleteProduct');
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
    });

    Route::group(['middleware' => ['checkRoleUser:IT']], function () {
        // User
        Route::get('/setting/users', [AuthController::class, 'users'])->name('setting.users');
        Route::get('/setting/users/get', [AuthController::class, 'getUsers'])->name('setting.getUsers');
        Route::get('/setting/users/new', [AuthController::class, 'newUser'])->name('setting.newUser');
        Route::post('/setting/users/create', [AuthController::class, 'createNewUser'])->name('setting.createNewUser');
        Route::get('/setting/users/edit/{user}', [AuthController::class, 'editUser'])->name('setting.editUser');
        Route::post('/setting/users/update/{user}', [AuthController::class, 'updateUser'])->name('setting.updateUser');
        Route::get('/setting/users/reset-password/{user}', [AuthController::class, 'resetPassword'])->name('setting.resetPassword');

        // Role
        Route::get('/setting/role', [AuthController::class, 'role'])->name('setting.role');
        Route::get('/setting/role/get', [AuthController::class, 'getRoles'])->name('setting.getRoles');
        Route::get('/setting/role/new', [AuthController::class, 'newRole'])->name('setting.newRole');
        Route::post('/setting/role/create', [AuthController::class, 'createRole'])->name('setting.createRole');
        Route::get('/setting/role/edit/{role}', [AuthController::class, 'editRole'])->name('setting.editRole');
        Route::post('/setting/role/update/{role}', [AuthController::class, 'updateRole'])->name('setting.updateRole');
    });

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
