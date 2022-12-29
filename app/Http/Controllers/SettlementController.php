<?php

namespace App\Http\Controllers;

use App\Services\SettlementService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SettlementController extends Controller
{
    protected $settlementService;
    protected $saveImageUrl;
    protected $baseImageUrl;
    protected $dateNow;

    public function __construct(SettlementService $settlementService)
    {
        $this->settlementService = $settlementService;
        $this->saveImageUrl = config('app.save_image_url');
        $this->baseImageUrl = config('app.base_image_url');
        $this->dateNow = date('Y-m-d H:i:s');
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
        $depoUser = Auth::user()->Depo;

        $sql = $this->settlementService->dataSettlement();

        if ($filterBy == "CreatedDate") {
            $sql->whereDate('tmdo.CreatedDate', '>=', $fromDate)
                ->whereDate('tmdo.CreatedDate', '<=', $toDate);
        } elseif ($filterBy == "FinishDate") {
            $sql->whereDate('tmdo.FinishDate', '>=', $fromDate)
                ->whereDate('tmdo.FinishDate', '<=', $toDate);
        }

        if (!empty($distributor)) {
            $sql->whereIn('tx_merchant_order.DistributorID', $distributor);
        }

        if ($depoUser != "ALL" && $depoUser == "REG1" && $depoUser == "REG2") {
            $sql->where('ms_distributor.Depo', $depoUser);
        }
        if ($depoUser == "REG1") {
            $sql->whereIn('ms_distributor.Depo', ['SMG', 'YYK']);
        }
        if ($depoUser == "REG2") {
            $sql->whereIn('ms_distributor.Depo', ['CRS', 'CKG', 'BDG']);
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
                ->editColumn('SettlementDate', function ($data) {
                    if ($data->SettlementDate == null) {
                        $settlementDate = "-";
                    } else {
                        $settlementDate = date('d-M-Y', strtotime($data->SettlementDate));
                    }
                    return $settlementDate;
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
                ->editColumn('PaymentSlip', function ($data) {
                    if ($data->PaymentSlip != null) {
                        $paymentSlip = '<a class="lihat-bukti" target="_blank" 
                                href="' . $this->baseImageUrl . 'settlement_slip_payment/' . $data->PaymentSlip . '"
                                data-store-name="' . $data->StoreName . '" data-do-id="' . $data->DeliveryOrderID . '">
                                    Lihat Bukti
                                </a>';
                    } else {
                        $paymentSlip = "-";
                    }

                    return $paymentSlip;
                })
                ->addColumn('Action', function ($data) {
                    if (
                        (Auth::user()->RoleID == "AD" || Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI") &&
                        ($data->StatusSettlementID == 1 || $data->StatusSettlementID == 2 || $data->StatusSettlementID == null)
                    ) {
                        $action = '<a class="btn btn-xs btn-warning btn-settlement my-1" 
                                    data-do-id="' . $data->DeliveryOrderID . '" data-store-name="' . $data->StoreName . '"
                                    data-payment-date="' . $data->PaymentDate . '" data-nominal="' . $data->PaymentNominal . '"
                                    data-status-settlement="' . $data->StatusSettlementID . '" data-must-settle="' . $data->TotalSettlement . '"
                                    data-created-date="' . $data->CreatedDate . '"
                                    data-payment-slip="' . $data->PaymentSlip . '" data-config="' . $this->baseImageUrl . 'settlement_slip_payment/' . '">
                                    Settle
                                </a>';
                    } else {
                        $action = '';
                    }
                    $invoice = '<a class="btn btn-xs btn-info my-1 mr-1" target="_blank" href="/restock/deliveryOrder/invoice/' . $data->DeliveryOrderID . '">Delivery Invoice</a>';

                    return $invoice . $action;
                })
                ->addColumn('Confirmation', function ($data) {
                    if ((Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI") && $data->StatusSettlementID == 2) {
                        $confirm = '<a class="btn btn-xs btn-success btn-approve" 
                                        data-do-id="' . $data->DeliveryOrderID . '" data-store-name="' . $data->StoreName . '">
                                        Terima
                                    </a>
                                    <a class="btn btn-xs btn-danger btn-reject"
                                        data-do-id="' . $data->DeliveryOrderID . '" data-store-name="' . $data->StoreName . '">
                                        Tolak
                                    </a>';
                    } else {
                        $confirm = '';
                    }
                    return $confirm;
                })
                ->rawColumns(['StockOrderID', 'StatusSettlementName', 'PaymentSlip', 'Action', 'Confirmation'])
                ->make();
        }
    }

    public function summarySettlement(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $distributor = $request->input('distributor');
        $filterBy = $request->input('filterBy');

        $startDate = new DateTime($fromDate) ?? new DateTime();
        $endDate = new DateTime($toDate) ?? new DateTime();
        $startDateFormat = $startDate->format('Y-m-d');
        $endDateFormat = $endDate->format('Y-m-d');

        $data = $this->settlementService->summaryDataSettlemnet($fromDate, $toDate, $distributor, $filterBy);

        return $data;
    }

    public function updateSettlement($deliveryOrderID, Request $request)
    {
        $request->validate([
            'payment_date' => 'required',
            'nominal' => 'required'
        ]);

        $deliveryOrder = DB::table('tx_merchant_delivery_order')->where('DeliveryOrderID', $deliveryOrderID)->select('StatusSettlementID', 'PaymentSlip')->first();

        $user = Auth::user()->Name . "-" . Auth::user()->RoleID . "-" . Auth::user()->Depo;
        $paymentDate = $request->input('payment_date');
        $paymentNominal = $request->input('nominal');
        if ($request->hasFile('payment_slip')) {
            $imageName = date('YmdHis') . '_' . $deliveryOrderID . '.' . $request->file('payment_slip')->extension();
            $request->file('payment_slip')->move($this->saveImageUrl . 'settlement_slip_payment/', $imageName);
        } else {
            $imageName = $deliveryOrder->PaymentSlip;
        }

        if ($deliveryOrder->StatusSettlementID === 1 || $deliveryOrder->StatusSettlementID === NULL) {
            $data = [
                'StatusSettlementID' => 2,
                'PaymentDate' => $paymentDate,
                'PaymentNominal' => $paymentNominal,
                'PaymentSlip' => $imageName
            ];
        } else {
            $data = [
                'StatusSettlementID' => 2,
                'PaymentDate' => $request->input('payment_date'),
                'PaymentNominal' => $request->input('nominal')
            ];
        }

        $dataLog = [
            'DeliveryOrderID' => $deliveryOrderID,
            'StatusSettlementID' => 2,
            'PaymentDate' => $paymentDate,
            'PaymentNominal' => $paymentNominal,
            'PaymentSlip' => $imageName,
            'ActionBy' => $user,
            'CreatedDate' => $this->dateNow,
            'Type' => 'SETTLEMENT'
        ];

        try {
            $this->settlementService->updateDataSettlement($deliveryOrderID, $data, $dataLog);
            return redirect()->route('distribution.settlement')->with('success', 'Data Setoran berhasil disimpan');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.settlement')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function confirmSettlement($deliveryOrderID, $status)
    {
        $user = Auth::user()->Name . "-" . Auth::user()->RoleID . "-" . Auth::user()->Depo;
        $deliveryOrder = DB::table('tx_merchant_delivery_order')
            ->where('DeliveryOrderID', $deliveryOrderID)
            ->select('PaymentDate', 'PaymentNominal', 'PaymentSlip')->first();

        if ($status == "approve") {
            $statusSettlementID = 3;
            $isPaid = 1;
        } else {
            $statusSettlementID = 4;
            $isPaid = 0;
        }

        $data = [
            'StatusSettlementID' => $statusSettlementID,
            'IsPaid' => $isPaid
        ];

        $dataLog = [
            'DeliveryOrderID' => $deliveryOrderID,
            'StatusSettlementID' => $statusSettlementID,
            'PaymentDate' => $deliveryOrder->PaymentDate,
            'PaymentNominal' => $deliveryOrder->PaymentNominal,
            'PaymentSlip' => $deliveryOrder->PaymentSlip,
            'ActionBy' => $user,
            'CreatedDate' => $this->dateNow,
            'Type' => 'SETTLEMENT'
        ];

        try {
            $this->settlementService->confirmDataSettlement($deliveryOrderID, $data, $dataLog);
            return redirect()->route('distribution.settlement')->with('success', 'Data Setoran berhasil dikonfirmasi');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.settlement')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }
}
