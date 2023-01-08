<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PpobController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Banner\BannerSliderController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MerchantMembershipController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RTSalesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockPromoController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\VoucherController;

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

    Route::group(['prefix' => 'price-submission', 'middleware' => ['checkRoleUser:IT,BM,CEO,FI,SM']], function () {
        Route::get('/', [DistributionController::class, 'priceSubmission'])->name('priceSubmission');
        Route::get('/get/{statusPriceSubmission}', [DistributionController::class, 'getPriceSubmission'])->name('getPriceSubmission');
        Route::get('/detail/{priceSubmissionID}', [DistributionController::class, 'detailPriceSubmission'])->name('detailPriceSubmission');
        Route::post('/confirm/{priceSubmissionID}/{status}', [DistributionController::class, 'confirmPriceSubmission'])->name('confirmPriceSubmission');
    });

    Route::group(['prefix' => 'summary', 'middleware' => ['checkRoleUser:IT,FI,BM,CEO,HL,SM,SV']], function () {
        Route::get('/finance', [SummaryController::class, 'summary'])->name('summary.summary');
        Route::post('/get', [SummaryController::class, 'getSummary'])->name('summary.dataSummary');
        Route::get('/report', [SummaryController::class, 'summaryReport'])->name('summary.report');
        Route::post('/report/data', [SummaryController::class, 'summaryReportData'])->name('summary.reportData');
        Route::get('/reportDetail/{type}', [SummaryController::class, 'reportDetail'])->name('summary.detail');
        Route::get('/margin', [SummaryController::class, 'margin'])->name('summary.margin');
        Route::post('/margin/data', [SummaryController::class, 'marginData'])->name('summary.marginData');

        Route::group(['prefix' => 'merchant'], function () {
            Route::get('/', [SummaryController::class, 'summaryMerchant'])->name('summary.merchant');
            Route::post('/data', [SummaryController::class, 'summaryMerchantData'])->name('summary.merchantData');
        });
    });

    // Distribution
    Route::group(['prefix' => 'distribution'], function () {
        Route::group(['prefix' => 'validation', 'middlewate' => ['checkRoleUser:IT,AH,SM']], function () {
            Route::get('/', [DistributionController::class, 'validationRestock'])->name('distribution.validationRestock');
            Route::get('/get', [DistributionController::class, 'getValidationRestock'])->name('distribution.getValidationRestock');
            Route::get('/detail/{stockOrderID}', [DistributionController::class, 'validationDetail'])->name('distribution.validationDetail');
            Route::post('/update/{stockOrderID}', [DistributionController::class, 'updateValidationRestock'])->name('distribution.updateValidationRestock');
        });
        Route::group(['prefix' => 'restock', 'middleware' => ['checkRoleUser:IT,AD,RBTAD,BM,CEO,FI,AH,DMO,HL,SM']], function () {
            Route::get('/', [DistributionController::class, 'restock'])->name('distribution.restock');
            Route::post('/get/allRestockAndDO', [DistributionController::class, 'getAllRestockAndDO'])->name('distribution.getAllRestockAndDO');
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
        Route::get('/restock/price-submission/create/{stockOrderID}', [DistributionController::class, 'createPriceSubmission'])->middleware('checkRoleUser:IT,BM,CEO,SM')->name('distribution.createPriceSubmission');
        Route::post('/restock/price-submission/store/{stockOrderID}', [DistributionController::class, 'storePriceSubmission'])->middleware('checkRoleUser:IT,BM,CEO,SM')->name('distribution.storePriceSubmission');

        Route::group(['prefix' => 'bill', 'middleware' => ['checkRoleUser:IT,AD,RBTAD,BM,CEO,FI,AH,HL,DMO,DRV,SM']], function () {
            Route::get('/', [DistributionController::class, 'billPayLater'])->name('distribution.billPayLater');
            Route::get('/get', [DistributionController::class, 'getBillPayLater'])->name('distribution.getBillPayLater');
            Route::post('/update/{deliveryOrderID}', [DistributionController::class, 'updateBillPayLater'])->name('distribution.updateBillPayLater');
        });
        Route::group(['prefix' => 'settlement', 'middlewate' => ['checkRoleUser:IT,AD,BM,CEO,FI,HL,SM']], function () {
            Route::get('/', [SettlementController::class, 'index'])->name('distribution.settlement');
            Route::post('/data', [SettlementController::class, 'getDataSettlement'])->name('distribution.getSettlement');
            Route::post('/summary', [SettlementController::class, 'summarySettlement'])->name('distribution.summarySettlement');
            Route::post('/update/{deliveryOrderID}', [SettlementController::class, 'updateSettlement'])->name('distribution.updateSettlement');
            Route::get('/confirm/{deliveryOrderID}/{status}', [SettlementController::class, 'confirmSettlement'])->name('distribution.confirmSettlement');
        });
        Route::group(['prefix' => 'product', 'middleware' => ['checkRoleUser:IT,AD,RBTAD,BM,CEO,FI,AH,DMO,HL,SM,SV']], function () {
            Route::get('/', [DistributionController::class, 'product'])->name('distribution.product');
            Route::get('/get', [DistributionController::class, 'getProduct'])->name('distribution.getProduct');
            Route::group(['middleware' => ['checkRoleUser:IT,FI,AH,BM,CEO,RBTAD,AD']], function () {
                Route::get('/add', [DistributionController::class, 'addProduct'])->name('distribution.addProduct');
                Route::get('/ajax/get/{distributorId}', [DistributionController::class, 'ajaxGetProduct'])->name('distribution.ajaxGetProduct');
                Route::post('/insert', [DistributionController::class, 'insertProduct'])->name('distribution.insertProduct');
                Route::post('/update/{distributorId}/{productId}/{gradeId}', [DistributionController::class, 'updateProduct'])->name('distribution.updateProduct');
                Route::get('/delete/{distributorId}/{productId}/{gradeId}', [DistributionController::class, 'deleteProduct'])->name('distribution.deleteProduct');
            });
        });
        Route::group(['prefix' => 'merchant', 'middleware' => ['checkRoleUser:IT,AD,RBTAD,BM,CEO,FI,AH,DMO,SM']], function () {
            Route::get('/', [DistributionController::class, 'merchant'])->name('distribution.merchant');
            Route::get('/get', [DistributionController::class, 'getMerchant'])->name('distribution.getMerchant');
            Route::post('/grade/update/{merchantId}', [DistributionController::class, 'updateGrade'])->name('distribution.updateGrade');
            Route::get('/specialprice/{merchantId}', [DistributionController::class, 'specialPrice'])->name('distribution.specialPrice');
            Route::get('/specialprice/{merchantId}/get', [DistributionController::class, 'getSpecialPrice'])->name('distribution.getSpecialPrice');
            Route::group(['middleware' => ['checkRoleUser:IT,FI,BM,CEO,RBTAD']], function () {
                Route::post('/specialprice/insertOrUpdate', [DistributionController::class, 'insertOrUpdateSpecialPrice'])->name('distribution.insertOrUpdateSpecialPrice');
                Route::post('/specialprice/delete', [DistributionController::class, 'deleteSpecialPrice'])->name('distribution.deleteSpecialPrice');
                Route::post('/specialprice/reset', [DistributionController::class, 'resetSpecialPrice'])->name('distribution.resetSpecialPrice');
            });
        });
    });

    Route::group(['prefix' => 'delivery', 'middleware' => ['checkRoleUser:IT,AD,BM,CEO,FI,AH,RBTAD,HL,DRV,SM']], function () {
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
            Route::get('/all-product/get/{status}', [DeliveryController::class, 'getExpeditionAllProduct'])->name('delivery.getExpeditionAllProduct');
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
        Route::group(['prefix' => 'opname', 'middleware' => ['checkRoleUser:IT,FI,BM,CEO,INVTR,HL']], function () {
            Route::get('/', [StockController::class, 'opname'])->name('stock.opname');
            Route::get('/get', [StockController::class, 'getOpname'])->name('stock.getOpname');
            Route::get('/create', [StockController::class, 'createOpname'])->name('stock.createOpname');
            Route::get('/getProductExcluded/{distributorID}', [StockController::class, 'getProductExcluded'])->name('stock.getProductExcluded');
            Route::get('/sumOldProduct/{distributorID}/{investorID}/{productID}/{label}', [StockController::class, 'sumOldProduct'])->name('stock.sumOldProduct');
            Route::get('/getDetailFromInbound/{inboundID}', [StockController::class, 'getDetailFromInbound'])->name('stock.getDetailFromInbound');
            Route::post('/store', [StockController::class, 'storeOpname'])->name('stock.storeOpname');
            Route::get('/detail/{stockOpnameID}', [StockController::class, 'detailOpname'])->name('stock.detailOpname');
        });

        Route::group(['prefix' => 'plan-purchase', 'middleware' => ['checkRoleUser:IT,FI,BM,CEO,HL']], function () {
            Route::get('/', [StockController::class, 'purchasePlan'])->name('stock.purchasePlan');
            Route::get('/detail/{purchasePlanID}', [StockController::class, 'purchasePlanDetail'])->name('stock.purchasePlanDetail');
            Route::get('/create', [StockController::class, 'createPurchasePlan'])->name('stock.createPurchasePlan');
            Route::post('/store', [StockController::class, 'storePurchasePlan'])->name('stock.storePurchasePlan');
            Route::get('/edit/{purchasePlanID}', [StockController::class, 'editPurchasePlan'])->name('stock.editPurchasePlan');
            Route::post('update/{purchasePlanID}', [StockController::class, 'updatePurchasePlan'])->name('stock.updatePurchasePlan');
            Route::get('/confirm/{purchasePlanID}/{status}', [StockController::class, 'confirmPurchasePlan'])->name('stock.confirmPurchasePlan');
        });

        Route::group(['prefix' => 'purchase', 'middleware' => ['checkRoleUser:IT,FI,BM,CEO,HL,INVTR']], function () {
            Route::get('/', [StockController::class, 'purchase'])->name('stock.purchase');
            Route::get('/get', [StockController::class, 'getPurchase'])->name('stock.getPurchase');
            Route::get('/all-product/get', [StockController::class, 'getPurchaseAllProduct'])->name('stock.getPurchaseAllProduct');
            Route::get('/create', [StockController::class, 'createPurchase'])->name('stock.createPurchase');
            Route::post('/store', [StockController::class, 'storePurchase'])->name('stock.storePurchase');
            Route::get('/by-purchase-plan/{purchasePlanID}', [StockController::class, 'getPurchaseByPurchasePlan'])->name('stock.getPurchaseByPurchasePlan');
            Route::get('/edit/{purchaseID}', [StockController::class, 'editPurchase'])->name('stock.editPurchase');
            Route::post('/update/{purchaseID}', [StockController::class, 'updatePurchase'])->name('stock.updatePurchase');
            Route::get('/detail/{purchaseID}', [StockController::class, 'detailPurchase'])->name('stock.detailPurchase');
            Route::get('/confirmation/{status}/{purchaseID}', [StockController::class, 'confirmPurchase'])->name('stock.confirmPurchase');
            Route::post('/confirmProduct/{status}/{purchaseDetailID}', [StockController::class, 'confirmProductPurchase'])->name('stock.confirmProductPurchase');
            Route::get('/edit/invoice/{purchaseID}', [StockController::class, 'editInvoice'])->name('stock.editInvoicePurchase');
            Route::post('/update/invoice/{purchaseID}', [StockController::class, 'updateInvoice'])->name('stock.updateInvoicePurchase');
        });

        Route::group(['prefix' => 'list', 'middleware' => ['checkRoleUser:IT,FI,AD,BM,CEO,INVTR,HL,SM,SV']], function () {
            Route::get('/', [StockController::class, 'listStock'])->name('stock.listStock');
            Route::get('/get', [StockController::class, 'getListStock'])->name('stock.getListStock');
            Route::get('/detail/{distributorID}/{investorID}/{productID}/{label}', [StockController::class, 'detailStock'])->name('stock.detailStock');
        });

        Route::group(['prefix' => 'mutation', 'middleware' => ['checkRoleUser:IT,FI,AD,BM,CEO,INVTR,HL']], function () {
            Route::get('/', [StockController::class, 'mutationStock'])->name('stock.mutation');
            Route::get('/get', [StockController::class, 'getMutationStock'])->name('stock.getMutation');
            Route::get('/all-product/get', [StockController::class, 'getMutationStockAllProduct'])->name('stock.getMutationAllProduct');
            Route::get('/detail/{mutationID}', [StockController::class, 'detailMutation'])->name('stock.detailMutation');
            Route::get('/create', [StockController::class, 'createMutation'])->name('stock.createMutation');
            Route::get('/getExcludeDistributorID/{distributorID}', [StockController::class, 'getExcludeDistributorID'])->name('stock.getExcludeDistributorID');
            Route::get('/getProductByPurchaseID/{purchaseID}/{distributorID}', [StockController::class, 'getProductByPurchaseID'])->name('stock.getProductByPurchaseID');
            Route::post('/store', [StockController::class, 'storeMutation'])->name('stock.storeMutation');
        });
    });

    Route::group(['prefix' => 'stock-promo'], function () {
        Route::group(['prefix' => 'inbound', 'middleware' => ['checkRoleUser:IT,FI,BM,CEO,AD,HL']], function () {
            Route::get('/', [StockPromoController::class, 'stockPromoInbound'])->name('stockPromo.inbound');
            Route::get('/detail/{inboundID}', [StockPromoController::class, 'stockPromoInboundDetail'])->name('stockPromo.inboundDetail');
            Route::get('/create-by-purchase', [StockPromoController::class, 'stockPromoInboundCreateByPurchase'])->name('stockPromo.createByPurchase');
            Route::post('/store-by-purchase', [StockPromoController::class, 'stockPromoInboundStoreByPurchase'])->name('stockPromo.storeByPurchase');
        });
    });

    Route::group(['prefix' => 'rtsales', 'middleware' => ['checkRoleUser:IT,FI,BM,CEO,DMO,SM,SV']], function () {
        Route::get('/summary', [RTSalesController::class, 'summary'])->name('rtsales.summary');

        Route::group(['prefix' => 'callplan'], function () {
            Route::post('/', [RTSalesController::class, 'callplan'])->name('rtsales.callPlan');
            Route::get('/index', [RTSalesController::class, 'callplanIndex'])->name('rtsales.callPlanIndex');
        });

        Route::group(['prefix' => 'callreport'], function () {
            Route::get('/', [RTSalesController::class, 'callReport'])->name('rtsales.callReport');
            Route::get('/get', [RTSalesController::class, 'getCallReport'])->name('rtsales.getCallReport');
        });

        Route::group(['prefix' => 'surveyreport'], function () {
            Route::get('/', [RTSalesController::class, 'surveyReport'])->name('rtsales.surveyReport');
            Route::get('/get', [RTSalesController::class, 'getSurveyReport'])->name('rtsales.getSurveyReport');
            Route::get('/update-valid/{visitSurveyID}/{isValid}', [RTSalesController::class, 'updateIsValid'])->name('rtsales.updateIsValid');
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

    Route::group(['prefix' => 'master/product/list', 'middleware' => ['checkRoleUser:IT,BM,CEO,FI,AH,DMO,RBTAD']], function () {
        // Product List
        Route::get('/', [ProductController::class, 'list'])->name('product.list');
        Route::get('/get', [ProductController::class, 'getLists'])->name('product.getLists');
        Route::get('/add', [ProductController::class, 'addList'])->name('product.addList');
        Route::post('/insert', [ProductController::class, 'insertList'])->name('product.insertList');
        Route::get('/edit/{product}', [ProductController::class, 'editList'])->name('product.editList');
        Route::post('/update/{product}', [ProductController::class, 'updateList'])->name('product.updateList');
    });

    Route::group(['prefix' => 'master/product/category', 'middleware' => ['checkRoleUser:IT,BM,CEO,FI,DMO']], function () {
        // Product Category
        Route::get('/', [ProductController::class, 'category'])->name('product.category');
        Route::get('/get', [ProductController::class, 'getCategories'])->name('product.getCategories');
        Route::get('/add', [ProductController::class, 'addCategory'])->name('product.addCategory');
        Route::post('/insert', [ProductController::class, 'insertCategory'])->name('product.insertCategory');
        Route::get('/edit/{category}', [ProductController::class, 'editCategory'])->name('product.editCategory');
        Route::post('/update/{category}', [ProductController::class, 'updateCategory'])->name('product.updateCategory');
    });

    Route::group(['prefix' => 'master/product/uom', 'middleware' => ['checkRoleUser:IT,BM,CEO,FI,DMO']], function () {
        // Product UOM
        Route::get('/', [ProductController::class, 'uom'])->name('product.uom');
        Route::get('/get', [ProductController::class, 'getUoms'])->name('product.getUoms');
        Route::get('/add', [ProductController::class, 'addUom'])->name('product.addUom');
        Route::post('/insert', [ProductController::class, 'insertUom'])->name('product.insertUom');
        Route::get('/edit/{uom}', [ProductController::class, 'editUom'])->name('product.editUom');
        Route::post('/update/{uom}', [ProductController::class, 'updateUom'])->name('product.updateUom');
    });

    Route::group(['prefix' => 'master/product/type', 'middleware' => ['checkRoleUser:IT,BM,CEO,FI,DMO']], function () {
        // Product Type
        Route::get('/', [ProductController::class, 'type'])->name('product.type');
        Route::get('/get', [ProductController::class, 'getTypes'])->name('product.getTypes');
        Route::get('/add', [ProductController::class, 'addType'])->name('product.addType');
        Route::post('/insert', [ProductController::class, 'insertType'])->name('product.insertType');
        Route::get('/edit/{type}', [ProductController::class, 'editType'])->name('product.editType');
        Route::post('/update/{type}', [ProductController::class, 'updateType'])->name('product.updateType');
    });

    Route::group(['prefix' => 'master/product/brand', 'middleware' => ['checkRoleUser:IT,BM,CEO,FI,DMO']], function () {
        // Product Brand
        Route::get('/', [ProductController::class, 'brand'])->name('product.brand');
        Route::get('/get', [ProductController::class, 'getBrands'])->name('product.getBrands');
        Route::get('/add', [ProductController::class, 'addBrand'])->name('product.addBrand');
        Route::post('/insert', [ProductController::class, 'insertBrand'])->name('product.insertBrand');
        Route::get('/edit/{brand}', [ProductController::class, 'editBrand'])->name('product.editBrand');
        Route::post('/update/{brand}', [ProductController::class, 'updateBrand'])->name('product.updateBrand');
    });

    Route::group(['prefix', 'middleware' => ['checkRoleUser:IT,BM,CEO,FI,AH,DMO']], function () {
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

    Route::group(['middleware' => ['checkRoleUser:IT,BM,CEO,FI,AH,HR,AD,HL,DMO,RBTAD,SM']], function () {
        Route::post('/merchant/restock/get', [MerchantController::class, 'getRestocks'])->name('merchant.getRestocks');
        Route::post('/merchant/restock/product/get', [MerchantController::class, 'getRestockProduct'])->name('merchant.getRestockProduct');
    });

    // Distributor
    Route::group(['middleware' => ['checkRoleUser:IT,BM,CEO,FI,AH,HR,DMO,RBTAD,HL,SM,SV,AD']], function () {
        Route::get('/distributor/account', [DistributorController::class, 'account'])->name('distributor.account');
        Route::get('/distributor/account/get', [DistributorController::class, 'getAccounts'])->withoutMiddleware('checkRoleUser:IT,BM,CEO,FI,AH,HR,DMO,RBTAD,SM')->name('distributor.getAccounts');
        Route::get('/distributor/account/add', [DistributorController::class, 'addDistributor'])->name('distributor.addDistributor');
        Route::post('/distributor/account/insert', [DistributorController::class, 'insertDistributor'])->name('distributor.insertDistributor');
        Route::get('/distributor/account/edit/{distributorId}', [DistributorController::class, 'editAccount'])->name('distributor.editAccount');
        Route::post('/distributor/account/update/{distributorId}', [DistributorController::class, 'updateAccount'])->name('distributor.updateAccount');
        Route::get('/distributor/account/product/{distributorId}', [DistributorController::class, 'productDetails'])->name('distributor.productDetails');
        Route::get('/distributor/account/product/get/{distributorId}', [DistributorController::class, 'getProductDetails'])->name('distributor.getProductDetails');
        Route::get('/distributor/account/product/edit/{distributorId}/{productId}/{gradeId}', [DistributorController::class, 'editProduct'])->name('distributor.editProduct');
        Route::post('/distributor/account/product/update/{distributorId}/{productId}/{gradeId}', [DistributorController::class, 'updateProduct'])->name('distributor.updateProduct');
        Route::get('/distributor/account/product/delete/{distributorId}/{productId}/{gradeId}', [DistributorController::class, 'deleteProduct'])->name('distributor.deleteProduct');
    });

    // Merchant
    Route::group(['middleware' => ['checkRoleUser:IT,BM,CEO,FI,AH,HR,DMO,HL,RBTAD,SM,AD']], function () {
        Route::get('/merchant/account', [MerchantController::class, 'account'])->name('merchant.account');
        Route::get('/merchant/account/get', [MerchantController::class, 'getAccounts'])->name('merchant.getAccounts');
        Route::post('/merchant/account/update-block/{merchantID}', [MerchantController::class, 'updateBlock'])->name('merchant.updateBlock');
        Route::get('/merchant/account/grade/get/{distributorId}', [MerchantController::class, 'getGrade'])->withoutMiddleware('checkRoleUser:IT,BM,CEO,FI,AH,HR,DMO,RBTAD')->name('merchant.getGrade');
        Route::get('/merchant/account/edit/{merchantId}', [MerchantController::class, 'editAccount'])->name('merchant.editAccount');
        Route::post('/merchant/account/update/{merchantId}', [MerchantController::class, 'updateAccount'])->name('merchant.updateAccount');
        Route::get('/merchant/account/product/{merchantId}', [MerchantController::class, 'product'])->withoutMiddleware('checkRoleUser:IT,BM,CEO,FI,AH,HR,DMO,RBTAD')->name('merchant.product');
        Route::get('/merchant/account/product/get/{merchantId}', [MerchantController::class, 'getProducts'])->withoutMiddleware('checkRoleUser:IT,BM,CEO,FI,AH,HR,DMO,RBTAD')->name('merchant.getProducts');
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
        Route::get('/merchant/assessment/edit/{assessmentID}', [MerchantController::class, 'editAssessment'])->name('merchant.editAssessment');
        Route::post('/merchant/assessment/update/{assessmentID}', [MerchantController::class, 'updateAssessment'])->name('merchant.updateAssessment');
        Route::get('/merchant/assessment/checked/{assessmentID}', [MerchantController::class, 'checkedAssessment'])->name('merchant.checkedAssessment');
        Route::get('/merchant/assessment/unchecked/{assessmentID}', [MerchantController::class, 'uncheckedAssessment'])->name('merchant.uncheckedAssessment');
        Route::get('/merchant/assessment/downloadKTP', [MerchantController::class, 'downloadKTP'])->name('merchant.downloadKTP');
    });
    Route::group(['middleware' => ['checkRoleUser:IT,BM,CEO,FI,AH,HR']], function () {
        Route::get('/merchant/powermerchant', [MerchantController::class, 'powerMerchant'])->name('merchant.powermerchant');
        Route::get('/merchant/powermerchant/get', [MerchantController::class, 'getPowerMerchant'])->name('merchant.getPowerMerchant');
        Route::post('/merchant/powermerchant/insert', [MerchantController::class, 'insertPowerMerchant'])->name('merchant.insertPowerMerchant');
        Route::get('/merchant/powermerchant/delete/{merchantId}', [MerchantController::class, 'deletePowerMerchant'])->name('merchant.deletePowerMerchant');
        Route::get('/merchant/otp', [MerchantController::class, 'otp'])->name('merchant.otp');
        Route::get('/merchant/otp/get', [MerchantController::class, 'getOtps'])->name('merchant.getOtps');

        Route::group(['prefix' => 'merchant/membership'], function () {
            Route::get('/', [MerchantMembershipController::class, 'index'])->name('merchant.membership');
            Route::post('/data', [MerchantMembershipController::class, 'data'])->name('merchant.membershipData');
            Route::get('/photo/{merchantID}', [MerchantMembershipController::class, 'photo'])->name('merchant.membershipPhoto');
            Route::post('/confirm/{merchantID}/{status}', [MerchantMembershipController::class, 'confirm'])->name('merchant.membershipConfirm');
            Route::post('/updateCrowdo/{merchantID}', [MerchantMembershipController::class, 'updateCrowdo'])->name('merchant.membershipUpdateCrowdo');
        });
    });

    // Customer
    Route::group(['middleware' => ['checkRoleUser:IT,BM,CEO,FI,AH,HR,DMO,RBTAD']], function () {
        Route::get('/customer/account', [CustomerController::class, 'account'])->name('customer.account');
        Route::get('/customer/account/get', [CustomerController::class, 'getAccounts'])->name('customer.getAccounts');
        Route::get('/customer/transaction', [CustomerController::class, 'transaction'])->name('customer.transaction');
        Route::get('/customer/transaction/get', [CustomerController::class, 'getTransactions'])->name('customer.getTransactions');
        Route::get('/customer/transaction/product/get', [CustomerController::class, 'getTransactionProduct'])->name('customer.getTransactionProduct');
        Route::get('/customer/transaction/detail/{orderId}', [CustomerController::class, 'transactionDetails'])->name('customer.transactionDetails');
        Route::get('/customer/transaction/detail/get/{orderId}', [CustomerController::class, 'getTransactionDetails'])->name('customer.getTransactionDetails');
    });
    Route::group(['middleware' => ['checkRoleUser:IT,BM,CEO,FI,AH,HR']], function () {
        Route::get('/customer/otp', [CustomerController::class, 'otp'])->name('customer.otp');
        Route::get('/customer/otp/get', [CustomerController::class, 'getOtps'])->name('customer.getOtps');
    });

    // Banner
    Route::group(['prefix' => 'banner', 'middleware' => ['checkRoleUser:IT']], function () {
        Route::group(['prefix' => 'slider'], function () {
            Route::get('/', [BannerSliderController::class, 'index'])->name('banner.slider');
            Route::post('/data', [BannerSliderController::class, 'data'])->name('banner.sliderData');
            Route::get('/create', [BannerSliderController::class, 'create'])->name('banner.sliderCreate');
            Route::get('/edit/{promoId}', [BannerSliderController::class, 'edit'])->name('banner.sliderEdit');
            Route::put('/update/{promoId}', [BannerSliderController::class, 'update'])->name('banner.sliderUpdate');
            Route::get('/listTargetID/{target}', [BannerSliderController::class, 'listTargetID'])->name('banner.listTargetID');
            Route::post('/store', [BannerSliderController::class, 'store'])->name('banner.sliderStore');
            Route::get('/delete/{promoId}', [BannerSliderController::class, 'destroy',])->name('banner.deletePromo');
        });
    });

    // RT Courier
    Route::group(['middleware' => ['checkRoleUser:IT'], 'prefix' => 'rtcourier'], function () {
        Route::prefix('courier')->group(function () {
            Route::get('/', [CourierController::class, 'courierList'])->name('courier.courierList');
            Route::get('/get', [CourierController::class, 'getCourierList'])->name('courier.getCourierList');
            Route::get('/nonactive/{courierCode}', [CourierController::class, 'nonactiveCourier'])->name('courier.nonactiveCourier');
        });
        Route::prefix('order')->group(function () {
            Route::get('/', [CourierController::class, 'courierOrder'])->name('courier.order');
            Route::get('/get/{courierStatus}', [CourierController::class, 'getCourierOrderByStatus'])->name('courier.getCourierOrderByStatus');
        });
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

    // Monthly Report
    Route::group(['middleware' => ['checkRoleUser:IT,BM,CEO']], function () {
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
        Route::get('/setting/users/log/{userID}', [AuthController::class, 'userLogDetail'])->name('setting.userLogDetail');
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

    // Investor by id
    Route::get('/investor/{id}', [Controller::class, 'getInvestorByID'])->name('getInvestorByID');

    // Product by id
    Route::get('/product/{productId}', [ProductController::class, 'getProductById'])->name('getProductById');

    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// Login
Route::group(['middleware' => ['guest']], function () {
    Route::get('/', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/', [AuthController::class, 'validateLogin'])->name('auth.validateLogin');

    Route::get('/rtrabat', [AuthController::class, 'loginRabat'])->name('auth.login.rabat');
    Route::post('/rtrabat', [AuthController::class, 'validateLoginRabat'])->name('auth.validateLoginRabat');
});

Route::get('/restock/invoice/{stockOrderId}', [InvoiceController::class, 'invoiceSO'])->name('restock.invoice');
Route::get('/restock/deliveryOrder/invoice/{deliveryOrderId}', [InvoiceController::class, 'invoiceDO'])->name('restockDeliveryOrder.invoice');
Route::get('/restock/invoice/completed/{stockOrderId}', [InvoiceController::class, 'invoiceDOselesai'])->name('deliveryOrderSelesai.invoice');
