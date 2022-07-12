<?php

namespace App\Http\Controllers;

use App\Services\SummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function summary()
    {
        return view('summary.index');
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

        return view('summary.index');
    }
}
