<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PpobController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RTSalesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\VoucherController;
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

    Route::group(['prefix' => 'distribution', 'middleware' => ['checkRoleUser:IT,AD,RBTAD,BM,FI,AH,DMO']], function () {
        // Distribution
        Route::group(['prefix' => 'restock'], function () {
            Route::get('/', [DistributionController::class, 'restock'])->name('distribution.restock');
            Route::get('/get/allRestockAndDO', [DistributionController::class, 'getAllRestockAndDO'])->name('distribution.getAllRestockAndDO');
            Route::get('/get/{statusOrder}', [DistributionController::class, 'getRestockByStatus'])->name('distribution.getRestockByStatus');
            Route::get('/detail/{stockOrderID}', [DistributionController::class, 'restockDetail'])->name('distribution.restockDetail');
            Route::post('/update/{stockOrderID}/{status}', [DistributionController::class, 'updateStatusRestock'])->name('distribution.updateStatusRestock');
            Route::get('/update/deliveryOrder/{deliveryOrderId}', [DistributionController::class, 'updateDeliveryOrder'])->name('distribution.updateDeliveryOrder');
            Route::post('/create/deliveryOrder/{stockOrderID}/{depoChannel}', [DistributionController::class, 'createDeliveryOrder'])->name('distribution.createDeliveryOrder');
            Route::get('/update/qty/{deliveryOrderId}', [DistributionController::class, 'updateQtyDO'])->name('distribution.updateQtyDO');
            Route::post('/cancel/deliveryOrder/{deliveryOrderId}', [DistributionController::class, 'cancelDeliveryOrder'])->name('distribution.cancelDeliveryOrder');
            Route::post('/reject/request/{deliveryOrderId}', [DistributionController::class, 'rejectRequestDO'])->name('distribution.rejectRequestDO');
            Route::post('/confirm/request/{deliveryOrderId}/{depoChannel}', [DistributionController::class, 'confirmRequestDO'])->name('distribution.confirmRequestDO');
        });
        Route::group(['prefix' => 'bill'], function () {
            Route::get('/', [DistributionController::class, 'billPayLater'])->name('distribution.billPayLater');
            Route::get('/get', [DistributionController::class, 'getBillPayLater'])->name('distribution.getBillPayLater');
            Route::post('/update/{deliveryOrderID}', [DistributionController::class, 'updateBillPayLater'])->name('distribution.updateBillPayLater');
        });
        Route::group(['prefix' => 'product'], function () {
            Route::get('/', [DistributionController::class, 'product'])->name('distribution.product');
            Route::get('/get', [DistributionController::class, 'getProduct'])->name('distribution.getProduct');
            Route::group(['middleware' => ['checkRoleUser:IT,FI,AH,BM,RBTAD']], function () {
                Route::get('/add', [DistributionController::class, 'addProduct'])->name('distribution.addProduct');
                Route::get('/ajax/get/{distributorId}', [DistributionController::class, 'ajaxGetProduct'])->name('distribution.ajaxGetProduct');
                Route::post('/insert', [DistributionController::class, 'insertProduct'])->name('distribution.insertProduct');
                Route::post('/update/{distributorId}/{productId}/{gradeId}', [DistributionController::class, 'updateProduct'])->name('distribution.updateProduct');
                Route::get('/delete/{distributorId}/{productId}/{gradeId}', [DistributionController::class, 'deleteProduct'])->name('distribution.deleteProduct');
            });
        });
        Route::group(['prefix' => 'merchant'], function () {
            Route::get('/', [DistributionController::class, 'merchant'])->name('distribution.merchant');
            Route::get('/get', [DistributionController::class, 'getMerchant'])->name('distribution.getMerchant');
            Route::post('/grade/update/{merchantId}', [DistributionController::class, 'updateGrade'])->name('distribution.updateGrade');
            Route::get('/specialprice/{merchantId}', [DistributionController::class, 'specialPrice'])->name('distribution.specialPrice');
            Route::get('/specialprice/{merchantId}/get', [DistributionController::class, 'getSpecialPrice'])->name('distribution.getSpecialPrice');
            Route::group(['middleware' => ['checkRoleUser:IT,FI,BM,RBTAD']], function () {
                Route::post('/specialprice/insertOrUpdate', [DistributionController::class, 'insertOrUpdateSpecialPrice'])->name('distribution.insertOrUpdateSpecialPrice');
                Route::post('/specialprice/delete', [DistributionController::class, 'deleteSpecialPrice'])->name('distribution.deleteSpecialPrice');
                Route::post('/specialprice/reset', [DistributionController::class, 'resetSpecialPrice'])->name('distribution.resetSpecialPrice');
            });
        });
    });

    Route::group(['prefix' => 'delivery', 'middleware' => ['checkRoleUser:IT,AD,BM,FI,AH,RBTAD']], function () {
        Route::group(['prefix' => 'request'], function () {
            Route::get('/', [DeliveryController::class, 'request'])->name('delivery.request');
            Route::post('/get', [DeliveryController::class, 'getRequest'])->name('delivery.getRequest');
            Route::post('/getDeliveryOrderByID', [DeliveryController::class, 'getDeliveryOrderByID'])->name('delivery.getDeliveryOrderByID');
            Route::post('/createExpedition', [DeliveryController::class, 'createExpedition'])->name('delivery.createExpedition');
            Route::get('/sumStockProduct/{productID}/{distributorID}/{investorID}/{label}', [DeliveryController::class, 'sumStockProduct'])->name('delivery.sumStockProduct');
        });

        Route::group(['prefix' => 'on-going'], function () {
            Route::get('/', [DeliveryController::class, 'expedition'])->name('delivery.expedition');
            Route::get('/get/{status}', [DeliveryController::class, 'getExpedition'])->name('delivery.getExpedition');
            Route::get('/detail/{expeditionID}', [DeliveryController::class, 'detailExpedition'])->name('delivery.detailExpedition');
            Route::get('/confirmExpedition/{status}/{expeditionID}', [DeliveryController::class, 'confirmExpedition'])->name('delivery.confirmExpedition');
            Route::post('/confirmProduct/{status}/{expeditionDetailID}', [DeliveryController::class, 'confirmProduct'])->name('delivery.confirmProduct');
            Route::get('/resendHaistar/{deliveryOrderID}', [DeliveryController::class, 'resendHaistar'])->name('delivery.resendHaistar');
            Route::get('/requestCancelHaistar/{deliveryOrderID}/{expeditionID}', [DeliveryController::class, 'requestCancelHaistar'])->name('delivery.requestCancelHaistar');
        });

        Route::group(['prefix' => 'history'], function () {
            Route::get('/', [DeliveryController::class, 'history'])->name('delivery.history');
            Route::get('/detail/{expeditionID}', [DeliveryController::class, 'detailHistory'])->name('delivery.detailHistory');
        });
    });

    Route::group(['prefix' => 'monthly-report', 'middleware' => ['checkRoleUser:IT,FI']], function () {
        Route::get('/', [MonthlyReportController::class, 'index'])->name('monthlyReport');
        Route::post('/', [MonthlyReportController::class, 'index'])->name('monthlyReport.post');
    });

    Route::prefix('stock')->group(function () {
        Route::group(['prefix' => 'opname', 'middleware' => ['checkRoleUser:IT,FI,BM,INVTR']], function () {
            Route::get('/', [StockController::class, 'opname'])->name('stock.opname');
            Route::get('/get', [StockController::class, 'getOpname'])->name('stock.getOpname');
            Route::get('/create', [StockController::class, 'createOpname'])->name('stock.createOpname');
            Route::get('/sumOldProduct/{distributorID}/{investorID}/{productID}/{label}', [StockController::class, 'sumOldProduct'])->name('stock.sumOldProduct');
            Route::post('/store', [StockController::class, 'storeOpname'])->name('stock.storeOpname');
            Route::get('/detail/{stockOpnameID}', [StockController::class, 'detailOpname'])->name('stock.detailOpname');
        });

        Route::group(['prefix' => 'purchase', 'middleware' => ['checkRoleUser:IT,FI,BM,INVTR']], function () {
            Route::get('/', [StockController::class, 'purchase'])->name('stock.purchase');
            Route::get('/get', [StockController::class, 'getPurchase'])->name('stock.getPurchase');
            Route::get('/create', [StockController::class, 'createPurchase'])->name('stock.createPurchase');
            Route::post('/store', [StockController::class, 'storePurchase'])->name('stock.storePurchase');
            Route::get('/edit/{purchaseID}', [StockController::class, 'editPurchase'])->name('stock.editPurchase');
            Route::post('/update/{purchaseID}', [StockController::class, 'updatePurchase'])->name('stock.updatePurchase');
            Route::get('/detail/{purchaseID}', [StockController::class, 'detailPurchase'])->name('stock.detailPurchase');
            Route::get('/confirmation/{status}/{purchaseID}', [StockController::class, 'confirmPurchase'])->name('stock.confirmPurchase');
            Route::get('/edit/invoice/{purchaseID}', [StockController::class, 'editInvoice'])->name('stock.editInvoicePurchase');
            Route::post('/update/invoice/{purchaseID}', [StockController::class, 'updateInvoice'])->name('stock.updateInvoicePurchase');
        });

        Route::group(['prefix' => 'list', 'middleware' => ['checkRoleUser:IT,FI,AD,BM,INVTR']], function () {
            Route::get('/', [StockController::class, 'listStock'])->name('stock.listStock');
            Route::get('/get', [StockController::class, 'getListStock'])->name('stock.getListStock');
            Route::get('/detail/{distributorID}/{investorID}/{productID}/{label}', [StockController::class, 'detailStock'])->name('stock.detailStock');
        });
    });

    Route::group(['prefix' => 'rtsales', 'middleware' => ['checkRoleUser:IT,FI,BM,DMO']], function () {
        Route::get('/summary', [RTSalesController::class, 'summary'])->name('rtsales.summary');

        Route::group(['prefix' => 'callreport'], function () {
            Route::get('/', [RTSalesController::class, 'callReport'])->name('rtsales.callReport');
            Route::get('/get', [RTSalesController::class, 'getCallReport'])->name('rtsales.getCallReport');
        });

        Route::group(['prefix' => 'surveyreport'], function () {
            Route::get('/', [RTSalesController::class, 'surveyReport'])->name('rtsales.surveyReport');
            Route::get('/get', [RTSalesController::class, 'getSurveyReport'])->name('rtsales.getSurveyReport');
        });

        Route::group(['prefix' => 'saleslist'], function () {
            Route::get('/', [RTSalesController::class, 'saleslist'])->name('rtsales.saleslist');
            Route::get('/get', [RTSalesController::class, 'getDataSales'])->name('rtsales.getSaleslist');
            Route::get('/add', [RTSalesController::class, 'addSales'])->name('rtsales.addSales');
            Route::post('/insert', [RTSalesController::class, 'insertSales'])->name('rtsales.insertSales');
            Route::get('/edit/{salesCode}', [RTSalesController::class, 'editSales'])->name('rtsales.editSales');
            Route::post('/update/{salesCode}', [RTSalesController::class, 'updateSales'])->name('rtsales.updateSales');
            Route::get('/delete/{salesCode}', [RTSalesController::class, 'deleteSales'])->name('rtsales.deleteSales');
        });

        Route::group(['prefix' => 'store'], function () {
            Route::get('/', [RTSalesController::class, 'storeList'])->name('rtsales.storeList');
            Route::get('/get', [RTSalesController::class, 'getStoreList'])->name('rtsales.getStoreList');
            Route::get('/create', [RTSalesController::class, 'createStore'])->name('rtsales.createStore');
            Route::post('/store', [RTSalesController::class, 'storeStore'])->name('rtsales.storeStore');
            Route::get('/edit/{storeID}', [RTSalesController::class, 'editStore'])->name('rtsales.editStore');
            Route::post('/update/{storeID}', [RTSalesController::class, 'updateStore'])->name('rtsales.updateStore');
            Route::get('/delete/{storeID}', [RTSalesController::class, 'deleteStore'])->name('rtsales.deleteStore');
        });
    });

    Route::group(['prefix' => 'master/product/list', 'middleware' => ['checkRoleUser:IT,BM,FI,AH,DMO,RBTAD']], function () {
        // Product List
        Route::get('/', [ProductController::class, 'list'])->name('product.list');
        Route::get('/get', [ProductController::class, 'getLists'])->name('product.getLists');
        Route::get('/add', [ProductController::class, 'addList'])->name('product.addList');
        Route::post('/insert', [ProductController::class, 'insertList'])->name('product.insertList');
        Route::get('/edit/{product}', [ProductController::class, 'editList'])->name('product.editList');
        Route::post('/update/{product}', [ProductController::class, 'updateList'])->name('product.updateList');
    });

    Route::group(['prefix' => 'master/product/category', 'middleware' => ['checkRoleUser:IT,BM,FI,DMO']], function () {
        // Product Category
        Route::get('/', [ProductController::class, 'category'])->name('product.category');
        Route::get('/get', [ProductController::class, 'getCategories'])->name('product.getCategories');
        Route::get('/add', [ProductController::class, 'addCategory'])->name('product.addCategory');
        Route::post('/insert', [ProductController::class, 'insertCategory'])->name('product.insertCategory');
        Route::get('/edit/{category}', [ProductController::class, 'editCategory'])->name('product.editCategory');
        Route::post('/update/{category}', [ProductController::class, 'updateCategory'])->name('product.updateCategory');
    });

    Route::group(['prefix' => 'master/product/uom', 'middleware' => ['checkRoleUser:IT,BM,FI,DMO']], function () {
        // Product UOM
        Route::get('/', [ProductController::class, 'uom'])->name('product.uom');
        Route::get('/get', [ProductController::class, 'getUoms'])->name('product.getUoms');
        Route::get('/add', [ProductController::class, 'addUom'])->name('product.addUom');
        Route::post('/insert', [ProductController::class, 'insertUom'])->name('product.insertUom');
        Route::get('/edit/{uom}', [ProductController::class, 'editUom'])->name('product.editUom');
        Route::post('/update/{uom}', [ProductController::class, 'updateUom'])->name('product.updateUom');
    });

    Route::group(['prefix' => 'master/product/type', 'middleware' => ['checkRoleUser:IT,BM,FI,DMO']], function () {
        // Product Type
        Route::get('/', [ProductController::class, 'type'])->name('product.type');
        Route::get('/get', [ProductController::class, 'getTypes'])->name('product.getTypes');
        Route::get('/add', [ProductController::class, 'addType'])->name('product.addType');
        Route::post('/insert', [ProductController::class, 'insertType'])->name('product.insertType');
        Route::get('/edit/{type}', [ProductController::class, 'editType'])->name('product.editType');
        Route::post('/update/{type}', [ProductController::class, 'updateType'])->name('product.updateType');
    });

    Route::group(['prefix' => 'master/product/brand', 'middleware' => ['checkRoleUser:IT,BM,FI,DMO']], function () {
        // Product Brand
        Route::get('/', [ProductController::class, 'brand'])->name('product.brand');
        Route::get('/get', [ProductController::class, 'getBrands'])->name('product.getBrands');
        Route::get('/add', [ProductController::class, 'addBrand'])->name('product.addBrand');
        Route::post('/insert', [ProductController::class, 'insertBrand'])->name('product.insertBrand');
        Route::get('/edit/{brand}', [ProductController::class, 'editBrand'])->name('product.editBrand');
        Route::post('/update/{brand}', [ProductController::class, 'updateBrand'])->name('product.updateBrand');
    });

    Route::group(['prefix', 'middleware' => ['checkRoleUser:IT,BM,FI,AH,DMO']], function () {
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


    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI,AH,HR,AD,DMO,RBTAD']], function () {
        Route::get('/merchant/restock/get', [MerchantController::class, 'getRestocks'])->name('merchant.getRestocks');
        Route::post('/merchant/restock/product/get', [MerchantController::class, 'getRestockProduct'])->name('merchant.getRestockProduct');
    });

    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI,AH,HR,DMO,RBTAD']], function () {
        // Distributor
        Route::get('/distributor/account', [DistributorController::class, 'account'])->name('distributor.account');
        Route::get('/distributor/account/get', [DistributorController::class, 'getAccounts'])->withoutMiddleware('checkRoleUser:IT,BM,FI,AH,HR,DMO,RBTAD')->name('distributor.getAccounts');
        Route::get('/distributor/account/edit/{distributorId}', [DistributorController::class, 'editAccount'])->name('distributor.editAccount');
        Route::post('/distributor/account/update/{distributorId}', [DistributorController::class, 'updateAccount'])->name('distributor.updateAccount');
        Route::get('/distributor/account/product/{distributorId}', [DistributorController::class, 'productDetails'])->name('distributor.productDetails');
        Route::get('/distributor/account/product/get/{distributorId}', [DistributorController::class, 'getProductDetails'])->name('distributor.getProductDetails');
        Route::get('/distributor/account/product/edit/{distributorId}/{productId}/{gradeId}', [DistributorController::class, 'editProduct'])->name('distributor.editProduct');
        Route::post('/distributor/account/product/update/{distributorId}/{productId}/{gradeId}', [DistributorController::class, 'updateProduct'])->name('distributor.updateProduct');
        Route::get('/distributor/account/product/delete/{distributorId}/{productId}/{gradeId}', [DistributorController::class, 'deleteProduct'])->name('distributor.deleteProduct');
    });

    // Merchant
    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI,AH,HR,DMO,RBTAD']], function () {
        Route::get('/merchant/account', [MerchantController::class, 'account'])->name('merchant.account');
        Route::get('/merchant/account/get', [MerchantController::class, 'getAccounts'])->name('merchant.getAccounts');
        Route::get('/merchant/account/grade/get/{distributorId}', [MerchantController::class, 'getGrade'])->withoutMiddleware('checkRoleUser:IT,BM,FI,AH,HR,DMO,RBTAD')->name('merchant.getGrade');
        Route::get('/merchant/account/edit/{merchantId}', [MerchantController::class, 'editAccount'])->name('merchant.editAccount');
        Route::post('/merchant/account/update/{merchantId}', [MerchantController::class, 'updateAccount'])->name('merchant.updateAccount');
        Route::get('/merchant/account/product/{merchantId}', [MerchantController::class, 'product'])->withoutMiddleware('checkRoleUser:IT,BM,FI,AH,HR,DMO,RBTAD')->name('merchant.product');
        Route::get('/merchant/account/product/get/{merchantId}', [MerchantController::class, 'getProducts'])->withoutMiddleware('checkRoleUser:IT,BM,FI,AH,HR,DMO,RBTAD')->name('merchant.getProducts');
        Route::get('/merchant/account/product/edit/{merchantId}/{productId}', [MerchantController::class, 'editProduct'])->name('merchant.editProduct');
        Route::post('/merchant/account/product/update/{merchantId}/{productId}', [MerchantController::class, 'updateProduct'])->name('merchant.updateProduct');
        Route::get('/merchant/account/product/delete/{merchantId}/{productId}', [MerchantController::class, 'deleteProduct'])->name('merchant.deleteProduct');
        Route::get('/merchant/account/operationalhour/edit/{merchantId}', [MerchantController::class, 'editOperationalHour'])->name('merchant.editOperationalHour');
        Route::post('/merchant/account/operationalhour/update/{merchantId}', [MerchantController::class, 'updateOperationalHour'])->name('merchant.updateOperationalHour');
        Route::get('/merchant/account/assessment/{merchantId}', [MerchantController::class, 'merchantAssessment'])->name('merchant.account.assessment');
        Route::get('/merchant/account/resetAssessment/{merchantAssessmentId}', [MerchantController::class, 'resetMerchantAssessment'])->name('merchant.resetAssessment');
        Route::get('/merchant/restock', [MerchantController::class, 'restock'])->name('merchant.restock');
        Route::get('/merchant/restock/detail/{stockOrderId}', [MerchantController::class, 'restockDetails'])->name('merchant.restockDetails');
        Route::get('/merchant/invoice/{stockOrderId}', [MerchantController::class, 'invoice'])->name('merchant.invoice');

        Route::get('/merchant/assessment', [MerchantController::class, 'assessment'])->name('merchant.assessment');
        Route::get('/merchant/assessment/get', [MerchantController::class, 'getAssessments'])->name('merchant.getAssessments');
        Route::get('/merchant/assessment/create', [MerchantController::class, 'createAssessment'])->name('merchant.createAssessment');
        Route::post('/merchant/assessment/store', [MerchantController::class, 'storeAssessment'])->name('merchant.storeAssessment');
    });
    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI,AH,HR']], function () {
        Route::get('/merchant/powermerchant', [MerchantController::class, 'powerMerchant'])->name('merchant.powermerchant');
        Route::get('/merchant/powermerchant/get', [MerchantController::class, 'getPowerMerchant'])->name('merchant.getPowerMerchant');
        Route::post('/merchant/powermerchant/insert', [MerchantController::class, 'insertPowerMerchant'])->name('merchant.insertPowerMerchant');
        Route::get('/merchant/powermerchant/delete/{merchantId}', [MerchantController::class, 'deletePowerMerchant'])->name('merchant.deletePowerMerchant');
        Route::get('/merchant/otp', [MerchantController::class, 'otp'])->name('merchant.otp');
        Route::get('/merchant/otp/get', [MerchantController::class, 'getOtps'])->name('merchant.getOtps');
    });

    // Customer
    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI,AH,HR,DMO,RBTAD']], function () {
        Route::get('/customer/account', [CustomerController::class, 'account'])->name('customer.account');
        Route::get('/customer/account/get', [CustomerController::class, 'getAccounts'])->name('customer.getAccounts');
        Route::get('/customer/transaction', [CustomerController::class, 'transaction'])->name('customer.transaction');
        Route::get('/customer/transaction/get', [CustomerController::class, 'getTransactions'])->name('customer.getTransactions');
        Route::get('/customer/transaction/product/get', [CustomerController::class, 'getTransactionProduct'])->name('customer.getTransactionProduct');
        Route::get('/customer/transaction/detail/{orderId}', [CustomerController::class, 'transactionDetails'])->name('customer.transactionDetails');
        Route::get('/customer/transaction/detail/get/{orderId}', [CustomerController::class, 'getTransactionDetails'])->name('customer.getTransactionDetails');
    });
    Route::group(['middleware' => ['checkRoleUser:IT,BM,FI,AH,HR']], function () {
        Route::get('/customer/otp', [CustomerController::class, 'otp'])->name('customer.otp');
        Route::get('/customer/otp/get', [CustomerController::class, 'getOtps'])->name('customer.getOtps');
    });

    Route::group(['middleware' => ['checkRoleUser:IT,RBTAD']], function () {
        // Voucher
        Route::get('/voucher/list', [VoucherController::class, 'list'])->name('voucher.list');
        Route::get('/voucher/list/get', [VoucherController::class, 'getList'])->name('voucher.getList');
        Route::get('/voucher/list/detail/{voucherCode}', [VoucherController::class, 'detail'])->name('voucher.detail');
        Route::get('/voucher/list/add', [VoucherController::class, 'addList'])->name('voucher.addList');
        Route::post('/voucher/list/insert', [VoucherController::class, 'insertList'])->name('voucher.insertList');
        Route::get('/voucher/list/edit/{voucherCode}', [VoucherController::class, 'editList'])->name('voucher.editList');
        Route::post('/voucher/list/update/{voucherCodeDB}', [VoucherController::class, 'updateList'])->name('voucher.updateList');
        Route::get('/voucher/log', [VoucherController::class, 'log'])->name('voucher.log');
        Route::get('/voucher/log/get', [VoucherController::class, 'getLog'])->name('voucher.getLog');
    });

    Route::group(['middleware' => ['checkRoleUser:IT']], function () {
        // Monthly Report
        Route::group(['prefix' => 'setting/monthly-report'], function () {
            Route::get('/', [MonthlyReportController::class, 'setting'])->name('setting.monthlyReport');
            Route::post('/getOneData', [MonthlyReportController::class, 'getOneData'])->name('monthlyReport.getOneData');
            Route::post('/store', [MonthlyReportController::class, 'store'])->name('monthlyReport.store');
            Route::post('/update', [MonthlyReportController::class, 'update'])->name('monthlyReport.update');
            Route::get('/delete/{area}/{periode}', [MonthlyReportController::class, 'delete'])->name('monthlyReport.delete');
        });

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

        // Module
        Route::group(['prefix' => 'setting/module'], function () {
            // Fairbanc
            Route::group(['prefix' => 'fairbanc'], function () {
                Route::get('/', [SettingController::class, 'fairbanc'])->name('setting.fairbanc');
                Route::get('/get', [SettingController::class, 'getFairbanc'])->name('setting.getFairbanc');
                Route::post('/insert', [SettingController::class, 'insertFairbanc'])->name('setting.insertFairbanc');
                Route::get('/delete/{merchantID}', [SettingController::class, 'deleteFairbanc'])->name('setting.deleteFairbanc');
            });

            // Haistar
            Route::group(['prefix' => 'haistar'], function () {
                Route::get('/', [SettingController::class, 'haistar'])->name('setting.haistar');
                Route::get('/get', [SettingController::class, 'getHaistar'])->name('setting.getHaistar');
                Route::post('/insert', [SettingController::class, 'insertHaistar'])->name('setting.insertHaistar');
                Route::get('/delete/{distributorID}', [SettingController::class, 'deleteHaistar'])->name('setting.deleteHaistar');
            });
        });
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

    Route::get('/rtrabat', [AuthController::class, 'loginRabat'])->name('auth.login.rabat');
    Route::post('/rtrabat', [AuthController::class, 'validateLoginRabat'])->name('auth.validateLoginRabat');
});

Route::get('/restock/invoice/{stockOrderId}', [InvoiceController::class, 'invoiceSO'])->name('restock.invoice');
Route::get('/restock/deliveryOrder/invoice/{deliveryOrderId}', [InvoiceController::class, 'invoiceDO'])->name('restockDeliveryOrder.invoice');
