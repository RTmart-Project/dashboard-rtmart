<?php

namespace App\Http\Controllers;

use App\Services\CourierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CourierController extends Controller
{
    private $courierService;
    public function __construct(CourierService $courierService)
    {
        $this->courierService = $courierService;
    }

    public function courierList()
    {
        return view('rtcourier.courierList.index');
    }

    public function getCourierList(Request $request)
    {
        $data = $this->courierService->getCouriers();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('IsActive', function ($data) {
                    if ($data->IsActive == 1) {
                        $isActive = "<span class='badge badge-success'>Ya</span>";
                    } else {
                        $isActive = "<span class='badge badge-danger'>Tidak</span>";
                    }
                    return $isActive;
                })
                ->editColumn('CreatedDate', function ($data) {
                    return date('d M Y, H:i', strtotime($data->CreatedDate));
                })
                ->addColumn('Action', function ($data) {
                    $btn =
                        // <a class="btn btn-xs btn-warning">Ubah</a>
                        '<a class="btn btn-xs btn-danger nonactive-courier" href="#" data-courier-name="' . $data->CourierName . '" data-courier-code="' . $data->CourierCode . '">Nonaktifkan</a>';
                    return $btn;
                })
                ->rawColumns(['IsActive', 'Action'])
                ->make(true);
        }
    }

    public function nonactiveCourier($courierCode)
    {
        $nonActiveCourier = $this->courierService->nonActiveCourier($courierCode);
        if ($nonActiveCourier) {
            return redirect()->route('courier.courierList')->with('success', 'Data Kurir berhasil dinonaktifkan');
        } else {
            return redirect()->route('courier.courierList')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function courierOrder()
    {
        return view('rtcourier.order.index');
    }

    public function getCourierOrderByStatus($courierStatus, Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $depoUser = Auth::user()->Depo;

        $sqlCourierOrder = $this->courierService->orderByStatus($courierStatus);

        if ($fromDate != '' && $toDate != '') {
            $sqlCourierOrder->whereDate('tx_product_order.CreatedDate', '>=', $fromDate)
                ->whereDate('tx_product_order.CreatedDate', '<=', $toDate);
        }
        if ($depoUser != "ALL") {
            $sqlCourierOrder->where('ms_distributor.Depo', $depoUser);
        }

        $data = $sqlCourierOrder;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('StatusOrder', function ($data) {
                    $pesananBaru = "S013";
                    $dikonfirmasi = "S014";
                    $dalamProses = "S019";
                    $dikirim = "S015";
                    $selesai = "S016";
                    $dibatalkan = "S017";

                    if ($data->StatusOrderID == $pesananBaru) {
                        $statusOrder = '<span class="badge badge-secondary">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dikonfirmasi) {
                        $statusOrder = '<span class="badge badge-primary">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dalamProses) {
                        $statusOrder = '<span class="badge badge-warning">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dikirim) {
                        $statusOrder = '<span class="badge badge-info">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $selesai) {
                        $statusOrder = '<span class="badge badge-success">' . $data->StatusOrder . '</span>';
                    } elseif ($data->StatusOrderID == $dibatalkan) {
                        $statusOrder = '<span class="badge badge-danger">' . $data->StatusOrder . '</span>';
                    } else {
                        $statusOrder = 'Status tidak ditemukan';
                    }

                    return $statusOrder;
                })
                ->rawColumns(['StatusOrder'])
                ->make(true);
        }
    }
}
