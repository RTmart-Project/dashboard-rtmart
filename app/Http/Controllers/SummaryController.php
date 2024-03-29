<?php

namespace App\Http\Controllers;

use stdClass;
use Illuminate\Http\Request;
use App\Services\SummaryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SummaryController extends Controller
{
    public function summary()
    {
        return view('summary.finance.index');
    }

    public function getSummary(SummaryService $summaryService, Request $request)
    {
        $distributorID = $request->distributorID;
        $filterStartDate = $request->startDate;
        $filterEndDate = $request->endDate;

        $day = date('d');
        $month = date('m');
        $year = date('Y');

        if ($filterStartDate != null && $filterEndDate != null) {
            $startDate = $filterStartDate;
            $endDate = $filterEndDate;
        } else {
            $startDate = $year . '-' . $month . '-01';
            $endDate = $year . '-' . $month . '-' . $day;
        }

        if ($distributorID == "tanggal") {
            $output = DB::select("
                select DateSummary from (
                    select @maxDate - interval (a.a+(10*b.a)+(100*c.a)+(1000*d.a)) day DateSummary from
                    (select 0 as a union all select 1 union all select 2 union all select 3
                    union all select 4 union all select 5 union all select 6 union all
                    select 7 union all select 8 union all select 9) a, /*10 day range*/
                    (select 0 as a union all select 1 union all select 2 union all select 3
                    union all select 4 union all select 5 union all select 6 union all
                    select 7 union all select 8 union all select 9) b, /*100 day range*/
                    (select 0 as a union all select 1 union all select 2 union all select 3
                    union all select 4 union all select 5 union all select 6 union all
                    select 7 union all select 8 union all select 9) c, /*1000 day range*/
                    (select 0 as a union all select 1 union all select 2 union all select 3
                    union all select 4 union all select 5 union all select 6 union all
                    select 7 union all select 8 union all select 9) d, /*10000 day range*/
                    (select @minDate := '$startDate', @maxDate := '$endDate') e
                ) f
                where DateSummary between @minDate and @maxDate
                order by DateSummary
            ");
        } else if ($distributorID == "grandTotal") {
            $output = $summaryService->summaryGrandTotal($startDate, $endDate);
        } else {
            $output = $summaryService->getSummary($startDate, $endDate, $distributorID);
        }

        if ($request->ajax()) {
            return $output;
        }

        return view('summary.finance.index');
    }

    public function summaryReport()
    {
        $depoUser = Auth::user()->Depo;
        $regionalUser = Auth::user()->Regional;

        $distributors = DB::table('ms_distributor')
            ->where('IsActive', 1)
            ->select('DistributorID', 'DistributorName');

        if ($depoUser != "ALL") {
            $distributors->where('Depo', $depoUser);
        }
        if ($regionalUser != NULL && $depoUser == "ALL") {
            $distributors->where('Regional', $regionalUser);
        }

        $distributors = $distributors->get();

        $sales = DB::table('ms_sales')
            ->where('IsActive', 1)
            ->select('SalesCode', 'SalesName')
            ->orderBy('Team', 'ASC');

        if ($depoUser != "ALL") {
            $sales->where('Team', $depoUser);
        }

        if ($regionalUser == "REGIONAL1" && $depoUser == "ALL") {
            // $sales->whereRaw("Team IN ('SMG', 'YYK', 'UNR')");
            $sales->whereIn('Team', ['SMG', 'UNR', 'YYK']);
        }

        if ($regionalUser == "REGIONAL2" && $depoUser == "ALL") {
            // $sales->whereRaw("Team IN ('CRS', 'CKG', 'BDG')");
            $sales->whereIn('Team', ['CRS', 'CKG', 'BDG']);
        }

        $sales = $sales->get();

        $typePO = DB::table('tx_merchant_order')->distinct('Type')->select('Type')->get();

        $partners = DB::table('ms_partner')->where('IsActive', 1)->get();

        return view('summary.report.index', [
            'distributors' => $distributors,
            'sales' => $sales,
            'typePO' => $typePO,
            'partners' => $partners
        ]);
    }

    public function summaryReportData(Request $request, SummaryService $summaryService)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $distributorID = $request->distributorID;
        $salesCode = $request->salesCode;
        $typePO = $request->typePO;
        $partner = $request->partner;

        $data = $summaryService->summaryReport($startDate, $endDate, $distributorID, $salesCode, $typePO, $partner);

        return $data;
    }

    public function reportDetail(Request $request, SummaryService $summaryService, $type)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $distributorID = $request->input('distributorID');
        $salesCode = $request->input('salesCode');
        $typePO = $request->input('typePO');
        $partner = $request->input('partner');

        if ($typePO != null) {
            $filterPO = explode(",", $typePO);
        } else {
            $filterPO = $typePO;
        }

        if ($distributorID != null) {
            $filterDistributor = explode(",", $distributorID);
        } else {
            $filterDistributor = $distributorID;
        }

        if ($salesCode != null) {
            $filterSales = explode(",", $salesCode);
        } else {
            $filterSales = $salesCode;
        }

        if ($partner != null) {
            $filterPartner = explode(",", $partner);
        } else {
            $filterPartner = $partner;
        }

        $data = $summaryService->summaryReport($startDate, $endDate, $filterDistributor, $filterSales, $filterPO, $filterPartner);

        $dataTotalValuePO = $summaryService->totalValuePO($type, $startDate, $endDate, $distributorID, $salesCode, $typePO, $partner);
        $dataCountPO = $summaryService->countPO($type, $startDate, $endDate, $distributorID, $salesCode, $typePO);
        $dataCountMerchantPO = $summaryService->countMerchantPO($type, $startDate, $endDate, $distributorID, $salesCode, $typePO);

        $dataTotalValueDO = $summaryService->totalValueDO($startDate, $endDate, $distributorID, $salesCode, $typePO, $partner);
        $dataCountDO = $summaryService->countDO($startDate, $endDate, $distributorID, $salesCode, $typePO);
        $dataCountMerchantDO = $summaryService->countMerchantDO($startDate, $endDate, $distributorID, $salesCode, $typePO);
        $dataFilter = $summaryService->dataFilter($startDate, $endDate, $distributorID, $salesCode, $typePO, $partner);
        
        if ($request->ajax()) {
            if ($type == "totalValuePO" || $type == "totalValuePOallStatus" || $type == "totalValuePOcancelled") {
                return DataTables::of($dataTotalValuePO)
                    ->editColumn('CreatedDate', function ($dataTotalValuePO) {
                        return date('d M Y H:i', strtotime($dataTotalValuePO->CreatedDate));
                    })
                    ->editColumn('StockOrderID', function ($data) {
                        return '<a href="/distribution/restock/detail/' . $data->StockOrderID . '" target="_blank">' . $data->StockOrderID . '</a>';
                    })
                    ->editColumn('StatusOrder', function ($dataTotalValuePO) {
                        $pesananBaru = "S009";
                        $dikonfirmasi = "S010";
                        $dalamProses = "S023";
                        $dikirim = "S012";
                        $selesai = "S018";
                        $dibatalkan = "S011";

                        if ($dataTotalValuePO->StatusOrderID == $pesananBaru) {
                            $statusOrder = '<span class="badge badge-secondary">' . $dataTotalValuePO->StatusOrder . '</span>';
                        } elseif ($dataTotalValuePO->StatusOrderID == $dikonfirmasi) {
                            $statusOrder = '<span class="badge badge-primary">' . $dataTotalValuePO->StatusOrder . '</span>';
                        } elseif ($dataTotalValuePO->StatusOrderID == $dalamProses) {
                            $statusOrder = '<span class="badge badge-warning">' . $dataTotalValuePO->StatusOrder . '</span>';
                        } elseif ($dataTotalValuePO->StatusOrderID == $dikirim) {
                            $statusOrder = '<span class="badge badge-info">' . $dataTotalValuePO->StatusOrder . '</span>';
                        } elseif ($dataTotalValuePO->StatusOrderID == $selesai) {
                            $statusOrder = '<span class="badge badge-success">' . $dataTotalValuePO->StatusOrder . '</span>';
                        } elseif ($dataTotalValuePO->StatusOrderID == $dibatalkan) {
                            $statusOrder = '<span class="badge badge-danger">' . $dataTotalValuePO->StatusOrder . '</span>';
                        } else {
                            $statusOrder = 'Status tidak ditemukan';
                        }

                        return $statusOrder;
                    })
                    ->editColumn('PurchasePrice', function ($data) {
                        if ($data->PurchasePrice != null) {
                            $purchasePrice = $data->PurchasePrice;
                        } else {
                            $purchasePrice = $data->PurchasePriceProduct;
                        }
                        return $purchasePrice;
                    })
                    ->addColumn('ValuePurchase', function ($data) {
                        if ($data->PurchasePrice != null) {
                            $purchasePrice = $data->PurchasePrice;
                        } else {
                            $purchasePrice = $data->PurchasePriceProduct;
                        }
                        $valuePurchase = $data->PromisedQuantity * $purchasePrice;
                        return $valuePurchase;
                    })
                    ->addColumn('ValueMargin', function ($data) {
                        if ($data->PurchasePrice != null) {
                            $purchasePrice = $data->PurchasePrice;
                        } else {
                            $purchasePrice = $data->PurchasePriceProduct;
                        }
                        $valueMargin = $data->SubTotalProduct - ($data->PromisedQuantity * $purchasePrice);
                        return $valueMargin;
                    })
                    ->addColumn('Margin', function ($data) {
                        if ($data->PurchasePrice != null) {
                            $purchasePrice = $data->PurchasePrice;
                        } else {
                            $purchasePrice = $data->PurchasePriceProduct;
                        }
                        $margin =  ($data->SubTotalProduct - ($data->PromisedQuantity * $purchasePrice)) / $data->SubTotalProduct * 100;
                        return round($margin, 2) . '%';
                    })
                    ->rawColumns(['StatusOrder', 'StockOrderID'])
                    ->make(true);
            } elseif ($type == "countPO") {
                return DataTables::of($dataCountPO)
                    ->editColumn('CreatedDate', function ($dataCountPO) {
                        return date('d M Y H:i', strtotime($dataCountPO->CreatedDate));
                    })
                    ->editColumn('StockOrderID', function ($data) {
                        return '<a href="/distribution/restock/detail/' . $data->StockOrderID . '" target="_blank">' . $data->StockOrderID . '</a>';
                    })
                    ->rawColumns(['StockOrderID'])
                    ->make(true);
            } elseif ($type == "countMerchantPO") {
                return DataTables::of($dataCountMerchantPO)
                    ->editColumn('MerchantID', function ($data) {
                        return '<a href="/merchant/account/product/' . $data->MerchantID . '" target="_blank">' . $data->MerchantID . '</a>';
                    })
                    ->rawColumns(['MerchantID'])
                    ->make(true);
            } elseif ($type == "totalValueDO") {
                return DataTables::of($dataTotalValueDO)
                    ->editColumn('DatePO', function ($dataTotalValueDO) {
                        // return date('d M Y H:i', strtotime($dataTotalValuePO->DatePO));
                        return date('d M Y H:i', strtotime($dataTotalValueDO->DatePO));
                    })
                    ->editColumn('CreatedDate', function ($dataTotalValueDO) {
                        return date('d M Y H:i', strtotime($dataTotalValueDO->CreatedDate));
                    })
                    ->editColumn('StockOrderID', function ($data) {
                        return '<a href="/distribution/restock/detail/' . $data->StockOrderID . '" target="_blank">' . $data->StockOrderID . '</a>';
                    })
                    ->editColumn('MerchantExpeditionID', function ($data) {
                        return '<a href="/delivery/history/detail/' . $data->MerchantExpeditionID . '" target="_blank">' . $data->MerchantExpeditionID . '</a>';
                    })
                    ->editColumn('StatusOrder', function ($dataTotalValueDO) {
                        if ($dataTotalValueDO->StatusOrder == "Dalam Pengiriman") {
                            $statusOrder = '<span class="badge badge-warning">' . $dataTotalValueDO->StatusOrder . '</span>';
                        } elseif ($dataTotalValueDO->StatusOrder == "Selesai") {
                            $statusOrder = '<span class="badge badge-success">' . $dataTotalValueDO->StatusOrder . '</span>';
                        } else {
                            $statusOrder = '<span class="badge badge-info">' . $dataTotalValueDO->StatusOrder . '</span>';
                        }

                        return $statusOrder;
                    })
                    ->addColumn('StatusPayLater', function ($data) {
                        if ($data->PaymentMethodID === 14) {
                            if ($data->IsPaid === 1) {
                                $statusPayLater = 'Lunas';
                            } else {
                                $statusPayLater = 'Belum Lunas';
                            }
                        } else {
                            $statusPayLater = '-';
                        }
                        return $statusPayLater;
                    })
                    ->addColumn('ValuePurchase', function ($data) {
                        $valuePurchase = $data->Qty * $data->PurchasePrice;
                        return $valuePurchase;
                    })
                    ->addColumn('ValueMargin', function ($data) {
                        $valueMargin = $data->ValueProduct - ($data->Qty * $data->PurchasePrice);
                        return $valueMargin;
                    })
                    ->addColumn('Margin', function ($data) {
                        $margin =  ($data->ValueProduct - ($data->Qty * $data->PurchasePrice)) / $data->ValueProduct * 100;
                        return round($margin, 2) . '%';
                    })
                    ->addColumn('GrandTotal', function ($dataTotalValueDO) {
                        return $dataTotalValueDO->SubTotal - $dataTotalValueDO->Discount + $dataTotalValueDO->ServiceCharge + $dataTotalValueDO->DeliveryFee;
                    })
                    ->rawColumns(['StatusOrder', 'StockOrderID', 'MerchantExpeditionID', 'Sales'])
                    ->make(true);
            } elseif ($type == "countDO") {
                return DataTables::of($dataCountDO)
                    ->editColumn('CreatedDate', function ($dataCountDO) {
                        return date('d M Y H:i', strtotime($dataCountDO->CreatedDate));
                    })
                    ->editColumn('StockOrderID', function ($data) {
                        return '<a href="/distribution/restock/detail/' . $data->StockOrderID . '" target="_blank">' . $data->StockOrderID . '</a>';
                    })
                    ->editColumn('MerchantExpeditionID', function ($data) {
                        return '<a href="/delivery/history/detail/' . $data->MerchantExpeditionID . '" target="_blank">' . $data->MerchantExpeditionID . '</a>';
                    })
                    ->editColumn('StatusOrder', function ($dataCountDO) {
                        if ($dataCountDO->StatusOrder == "Dalam Pengiriman") {
                            $statusOrder = '<span class="badge badge-warning">' . $dataCountDO->StatusOrder . '</span>';
                        } elseif ($dataCountDO->StatusOrder == "Selesai") {
                            $statusOrder = '<span class="badge badge-success">' . $dataCountDO->StatusOrder . '</span>';
                        } else {
                            $statusOrder = '<span class="badge badge-info">' . $dataCountDO->StatusOrder . '</span>';
                        }
                        return $statusOrder;
                    })
                    ->addColumn('GrandTotal', function ($dataCountDO) {
                        return $dataCountDO->SubTotal - $dataCountDO->Discount + $dataCountDO->ServiceCharge + $dataCountDO->DeliveryFee;
                    })
                    ->rawColumns(['StatusOrder', 'StockOrderID', 'MerchantExpeditionID'])
                    ->make(true);
            } elseif ($type == "countMerchantDO") {
                return DataTables::of($dataCountMerchantDO)
                    ->editColumn('MerchantID', function ($data) {
                        return '<a href="/merchant/account/product/' . $data->MerchantID . '" target="_blank">' . $data->MerchantID . '</a>';
                    })
                    ->rawColumns(['MerchantID'])
                    ->make(true);
            }
        }

        if ($type == "totalValuePO" || $type == "totalValuePOallStatus" || $type == "totalValuePOcancelled") {
            return view('summary.report.detail.po.total-value', [
                'data' => $data->PO,
                'dataFilter' => $dataFilter,
                'type' => $type
            ]);
        } elseif ($type == "countPO") {
            return view('summary.report.detail.po.count-po', [
                'data' => (clone $dataCountPO)->count(),
                'dataFilter' => $dataFilter
            ]);
        } elseif ($type == "countMerchantPO") {
            return view('summary.report.detail.po.count-merchant', [
                'data' => (clone $dataCountMerchantPO)->count(),
                'dataFilter' => $dataFilter
            ]);
        } elseif ($type == "totalValueDO") {
            return view('summary.report.detail.do.total-value', [
                'data' => $data->DO->TotalValueDO,
                'dataFilter' => $dataFilter
            ]);
        } elseif ($type == "countDO") {
            return view('summary.report.detail.do.count-do', [
                'data' => (clone $dataCountDO)->count(),
                'dataFilter' => $dataFilter
            ]);
        } elseif ($type == "countMerchantDO") {
            return view('summary.report.detail.do.count-merchant', [
                'data' => (clone $dataCountMerchantDO)->get()->count(),
                'dataFilter' => $dataFilter
            ]);
        }
    }

    public function margin()
    {
        return view('summary.margin.index');
    }

    public function marginData(SummaryService $summaryService, Request $request)
    {
        $filterStartDate = $request->input('fromDate');
        $filterEndDate = $request->input('toDate');
        $filterTypePO = $request->input('typePO');
        if ($filterTypePO === null) {
            $filterTypePO = ["REGULER"];
        }

        if ($filterStartDate != null && $filterEndDate != null) {
            $startDate = $filterStartDate;
            $endDate = $filterEndDate;
        } else {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-d');
        }

        $data = $summaryService->dataSummaryMargin($startDate, $endDate, $filterTypePO);

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('GrossMargin', function ($data) {
                    return $data->Sales - $data->COGS;
                })
                ->addColumn('NettMargin', function ($data) {
                    return $data->Sales - $data->COGS - $data->Discount;
                })
                ->addColumn('PercentMargin', function ($data) {
                    if ($data->Sales - $data->Discount === 0) {
                        $percentMargin = 0;
                    } else {
                        $percentMargin = round(($data->Sales - $data->COGS - $data->Discount) / ($data->Sales - $data->Discount) * 100, 2) . '%';
                    }

                    return $percentMargin;
                })
                ->rawColumns(['DistributorName'])
                ->make();
        }
    }

    public function summaryMerchant()
    {
        $depoUser = Auth::user()->Depo;
        $regionalUser = Auth::user()->Regional;

        $distributors = DB::table('ms_distributor')
            ->where('IsActive', 1)
            ->select('DistributorID', 'DistributorName');

        if ($depoUser != "ALL") {
            $distributors->where('Depo', $depoUser);
        }
        if ($regionalUser != NULL && $depoUser == "ALL") {
            $distributors->where('Regional', $regionalUser);
        }

        $distributors = $distributors->get();

        $sales = DB::table('ms_sales')
            ->where('IsActive', 1)
            ->select('SalesCode', 'SalesName')
            ->orderBy('Team', 'ASC');

        if ($depoUser != "ALL") {
            $sales->where('Team', $depoUser);
        }
        if ($regionalUser == "REGIONAL1" && $depoUser == "ALL") {
            $sales->whereIn('Team', ['SMG', 'YYK', 'UNR']);
        }
        if ($regionalUser == "REGIONAL2" && $depoUser == "ALL") {
            $sales->whereIn('Team', ['CRS', 'CKG', 'BDG']);
        }

        $sales = $sales->get();

        return view('summary.merchant.index', [
            'distributors' => $distributors,
            'sales' => $sales
        ]);
    }

    public function summaryMerchantData(Request $request, SummaryService $summaryService)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $filterBy = $request->input('filterBy');
        $distributorID = $request->input('distributorID');
        $salesCode = $request->input('salesCode');
        $marginStatus = $request->input('marginStatus');
        $regionalUser = Auth::user()->Regional;
        $depoUser = Auth::user()->Depo;

        if (($regionalUser == "REGIONAL1" && $depoUser == "ALL") && !$distributorID) {
            $distributorID = ["D-2004-000002", "D-2212-000001"];
        }
        if (($regionalUser == "REGIONAL2" && $depoUser == "ALL") && !$distributorID) {
            $distributorID = ["D-2004-000006", "D-2004-000005", "D-2004-000001"];
        }

        $sqlMain = $summaryService->dataSummaryMerchant($startDate, $endDate, $filterBy, $distributorID, $salesCode, $marginStatus);

        $data = $sqlMain;

        if ($request->ajax()) {
            return Datatables::of($data)
                ->addColumn('Sales', function ($data) {
                    if ($data->SalesCode) {
                        $sales = $data->SalesCode . ' ' . $data->SalesName;
                    } else {
                        $sales = '-';
                    }
                    return $sales;
                })
                ->addColumn('MarginStatus', function ($data) {
                    if ($data->PercentNettMargin > 8) {
                        $status = '<span class="badge badge-success"><i class="fas fa-arrow-up"></i> High</span>';
                    } else if ($data->PercentNettMargin < 5) {
                        $status = '<span class="badge badge-danger"><i class="fas fa-arrow-down"></i> Below</span>';
                    } else {
                        $status = '<span class="badge badge-secondary"><i class="fas fa-minus"></i> Standart</span>';
                    }
                    return $status;
                })
                ->rawColumns(['MarginStatus'])
                ->make(true);
        }
    }
}
