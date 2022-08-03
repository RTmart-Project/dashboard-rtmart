<?php

namespace App\Http\Controllers;

use App\Services\SummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function summaryReportData(Request $request)
    {
        // $startDate = $request->startDate;
        // $endDate = $request->endDate;
        // $distributorID = $request->distributorID;
        // $salesCode = $request->salesCode;
        $startDate = "2022-07-01";
        $endDate = "2022-08-03";
        $distributorID = null;
        $salesCode = null;

        $sqlMainPO = DB::table('tx_merchant_order as tmo')
            ->select('tmo.StockOrderID', 'tmo.CreatedDate', 'tmo.MerchantID', 'tmo.DistributorID', 'tmo.SalesCode', 'tmo.NettPrice', 'tmo.StatusOrderID')
            ->whereRaw("DATE(tmo.CreatedDate) >= '$startDate'")
            ->whereRaw("DATE(tmo.CreatedDate) <= '$endDate'")
            ->whereRaw("tmo.StatusOrderID IN ('S009', 'S010', 'S023')");

        if ($distributorID != null) {
            $distributorIn = "'" . implode("', '", $distributorID) . "'";
            $sqlMainPO->whereRaw("tmo.DistributorID IN ($distributorIn)");
        }

        if ($salesCode != null) {
            $salesCodeIn = "'" . implode("', '", $salesCode) . "'";
            $sqlMainPO->whereRaw("tmo.SalesCode IN ($salesCodeIn)");
        }

        $sqlMarginPO = (clone $sqlMainPO)
            ->join('tx_merchant_order_detail as tmod', 'tmod.StockOrderID', 'tmo.StockOrderID')
            ->select('tmo.StockOrderID', 'tmo.DistributorID', 'tmod.ProductID', 'tmod.PromisedQuantity', 'tmod.Nett');

        dd($sqlMarginPO->get());

        $sqlMainPO = $sqlMainPO->toSql();

        $sqlPO = DB::table(DB::raw("($sqlMainPO) as SummaryPO"))
            ->selectRaw("
                ( 
                    SELECT SUM(SummaryPO.NettPrice)
                ) as TotalValuePO,
                (
                    SELECT COUNT(SummaryPO.StockOrderID)
                ) as CountTotalPO,
                (
                    SELECT COUNT(DISTINCT SummaryPO.MerchantID)
                ) as CountMerchantPO
            ");

        $data = $sqlPO->first();

        dd($data);

        // $data = [
        //     'a' => $startDate,
        //     'b' => $endDate,
        //     'c' => $distributorID,
        //     'd' => $salesCode
        // ];
        return $data;
    }
}
