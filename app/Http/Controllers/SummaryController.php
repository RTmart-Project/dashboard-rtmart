<?php

namespace App\Http\Controllers;

use App\Services\SummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;
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
        $distributors = DB::table('ms_distributor')
            ->where('IsActive', '=', 1)
            ->whereNotNull('Email')
            ->select('DistributorID', 'DistributorName')
            ->get();

        $sales = DB::table('ms_sales')
            ->where('IsActive', 1)
            ->select('SalesCode', 'SalesName')->get();

        return view('summary.report.index', [
            'distributors' => $distributors,
            'sales' => $sales
        ]);
    }

    public function summaryReportData(Request $request, SummaryService $summaryService)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $distributorID = $request->distributorID;
        $salesCode = $request->salesCode;

        $data = $summaryService->summaryReport($startDate, $endDate, $distributorID, $salesCode);

        return $data;
    }

    public function reportDetail(Request $request, SummaryService $summaryService, $type)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $distributorID = $request->input('distributorID');
        $salesCode = $request->input('salesCode');

        $dataTotalValuePO = $summaryService->totalValuePO($startDate, $endDate, $distributorID, $salesCode);
        $dataCountPO = $summaryService->countPO($startDate, $endDate, $distributorID, $salesCode);
        $dataCountMerchantPO = $summaryService->countMerchantPO($startDate, $endDate, $distributorID, $salesCode);

        $dataTotalValueDO = $summaryService->totalValueDO($startDate, $endDate, $distributorID, $salesCode);
        $dataCountDO = $summaryService->countDO($startDate, $endDate, $distributorID, $salesCode);
        $dataCountMerchantDO = $summaryService->countMerchantDO($startDate, $endDate, $distributorID, $salesCode);

        $dataFilter = $summaryService->dataFilter($startDate, $endDate, $distributorID, $salesCode);

        if ($request->ajax()) {
            if ($type == "totalValuePO") {
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
                    ->addColumn('GrandTotal', function ($dataTotalValueDO) {
                        return $dataTotalValueDO->SubTotal - $dataTotalValueDO->Discount + $dataTotalValueDO->ServiceCharge + $dataTotalValueDO->DeliveryFee;
                    })
                    ->rawColumns(['StatusOrder', 'StockOrderID', 'MerchantExpeditionID'])
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

        if ($type == "totalValuePO") {
            return view('summary.report.detail.po.total-value', [
                'data' => (clone $dataTotalValuePO)->distinct('tmo.StockOrderID')->select('tmo.StockOrderID', 'tmo.NettPrice')->get()->toArray(),
                'dataFilter' => $dataFilter
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
                'data' => (clone $dataTotalValueDO)->groupBy('tmdo.DeliveryOrderID')->get()->toArray(),
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
}
