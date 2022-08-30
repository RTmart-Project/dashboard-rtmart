<?php

namespace App\Http\Controllers;

use App\Services\SettlementService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SettlementController extends Controller
{
    private $settlementService;

    public function __construct(SettlementService $settlementService)
    {
        $this->settlementService = $settlementService;
    }

    public function index()
    {
        return view('distribution.settlement.index');
    }

    public function getDataSettlement(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $distributor = $request->input('distributor');
        $filterBy = $request->input('filterBy');

        $startDate = new DateTime($fromDate) ?? new DateTime();
        $endDate = new DateTime($toDate) ?? new DateTime();
        $startDateFormat = $startDate->format('Y-m-d');
        $endDateFormat = $endDate->format('Y-m-d');

        $sql = $this->settlementService->dataSettlement();

        if ($filterBy == "CreatedDate" || $filterBy == "") {
            $sql->whereDate('tmdo.CreatedDate', '>=', $startDateFormat)
                ->whereDate('tmdo.CreatedDate', '<=', $endDateFormat);
        } elseif ($filterBy == "FinishDate") {
            $sql->whereDate('tmdo.FinishDate', '>=', $startDateFormat)
                ->whereDate('tmdo.FinishDate', '<=', $endDateFormat);
        }

        if (!empty($distributor)) {
            $sql->whereIn('tx_merchant_order.DistributorID', $distributor);
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sql->where('ms_distributor.Depo', '=', $depoUser);
        }

        $data = $sql;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('StockOrderID', function ($data) {
                    $link = '<a href="/distribution/restock/detail/' . $data->StockOrderID . '" target="_blank">' . $data->StockOrderID . '</a>';
                    return $link;
                })
                ->editColumn('FinishDate', function ($data) {
                    $date = date('d-M-Y H:i', strtotime($data->FinishDate));
                    return $date;
                })
                ->editColumn('CreatedDate', function ($data) {
                    $date = date('d-M-Y H:i', strtotime($data->CreatedDate));
                    return $date;
                })
                ->editColumn('PaymentDate', function ($data) {
                    if ($data->PaymentDate == null) {
                        $paymentDate = "-";
                    } else {
                        $paymentDate = date('d-M-Y', strtotime($data->PaymentDate));
                    }
                    return $paymentDate;
                })
                ->editColumn('StatusSettlementName', function ($data) {
                    if ($data->StatusSettlementID == 1 || $data->StatusSettlementID == null) {
                        $badge = '<span class="badge badge-info">' . $data->StatusSettlementName . '</span>';
                    } elseif ($data->StatusSettlementID == 2) {
                        $badge = '<span class="badge badge-warning">' . $data->StatusSettlementName . '</span>';
                    } elseif ($data->StatusSettlementID == 3) {
                        $badge = '<span class="badge badge-success">' . $data->StatusSettlementName . '</span>';
                    } else {
                        $badge = '<span class="badge badge-danger">' . $data->StatusSettlementName . '</span>';
                    }
                    return $badge;
                })
                ->rawColumns(['StockOrderID', 'StatusSettlementName'])
                ->make();
        }
    }
}
