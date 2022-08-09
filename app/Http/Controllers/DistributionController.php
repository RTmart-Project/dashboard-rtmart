<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Services\DeliveryOrderService;
use App\Services\HaistarService;
use App\Services\MerchantService;
use App\Services\PayLaterService;
use App\Services\RestockService;
use App\Services\TxLogService;
use Illuminate\Support\Str;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use PHPUnit\TextUI\Help;
use stdClass;
use Yajra\DataTables\Facades\DataTables;

class DistributionController extends Controller
{
    protected $saveImageUrl;
    protected $baseImageUrl;

    public function __construct()
    {
        $this->saveImageUrl = config('app.save_image_url');
        $this->baseImageUrl = config('app.base_image_url');
    }

    public function validationRestock()
    {
        return view('distribution.validation.index');
    }

    public function getValidationRestock(Request $request, RestockService $restockService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlGetValidation = $restockService->getRestokValidation();
        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlGetValidation->where('ms_distributor.Depo', '=', $depoUser);
        }

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlGetValidation->whereDate('tmo.CreatedDate', '>=', $fromDate)
                ->whereDate('tmo.CreatedDate', '<=', $toDate);
        }

        $data = $sqlGetValidation;

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('PhoneNumber', function ($data) {
                    return '<a href="https://wa.me/' . $data->PhoneNumber . '" target="_blank">' . $data->PhoneNumber . '</a>';
                })
                ->filterColumn('Sales', function ($query, $keyword) {
                    $sql = "CONCAT(tmo.SalesCode,' - ',ms_sales.SalesName)  like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->editColumn('Validation', function ($data) {
                    if ($data->IsValid === 1) {
                        $classBadge = 'success';
                    } elseif ($data->IsValid === 0) {
                        $classBadge = 'danger';
                    } elseif ($data->IsValid === NULL) {
                        $classBadge = 'info';
                    }
                    $validation = '<span class="badge badge-' . $classBadge . '">' . $data->Validation . '</span>';
                    return $validation;
                })
                ->editColumn('ValidationNotes', function ($data) {
                    if ($data->ValidationNotes == null) {
                        $validationNotes = "-";
                    } else {
                        $validationNotes = $data->ValidationNotes;
                    }
                    return $validationNotes;
                })
                ->addColumn('Action', function ($data) {
                    // $btn = '<a class="btn btn-sm btn-warning update-validitas" 
                    //             data-stock-order-id="' . $data->StockOrderID . '"
                    //             data-is-valid="' . $data->IsValid . '"
                    //             data-validation-notes="' . $data->ValidationNotes . '">
                    //             Update Validitas
                    //         </a>';
                    $btn = '<a class="btn btn-xs btn-info" href="/distribution/validation/detail/' . $data->StockOrderID . '">Detail</a>';
                    return $btn;
                })
                ->filterColumn('Validation', function ($query, $keyword) {
                    $sql = "CASE
                                WHEN tmo.IsValid = 1 THEN 'Sudah Valid'
                                WHEN tmo.IsValid = 0 THEN 'Tidak Valid'
                                ELSE 'Belum Divalidasi'
                            END like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['PhoneNumber', 'Validation', 'Action'])
                ->make(true);
        }
    }

    public function validationDetail($stockOrderID)
    {
        $sql = DB::table('tx_merchant_order AS tmo')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tmo.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', 'tmo.DistributorID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', 'tmo.PaymentMethodID')
            ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'tmo.SalesCode')
            ->where('tmo.StockOrderID', $stockOrderID)
            ->selectRaw("
                tmo.StockOrderID,
                tmo.CreatedDate,
                ms_payment_method.PaymentMethodName,
                ms_distributor.DistributorName,
                tmo.TotalPrice,
                tmo.NettPrice,
                tmo.DeliveryFee,
                tmo.DiscountVoucher + tmo.DiscountPrice AS Discount,
                tmo.ServiceChargeNett,
                CONCAT(tmo.SalesCode, ' - ', ms_sales.SalesName) AS Sales,
                tmo.IsValid,
                CASE
                    WHEN tmo.IsValid = 1 THEN 'Sudah Valid'
                    WHEN tmo.IsValid = 0 THEN 'Tidak Valid'
                    ELSE 'Belum Divalidasi'
                END AS Validation,
                tmo.ValidationNotes,
                tmo.MerchantID,
                ms_merchant_account.StoreName,
                ms_merchant_account.OwnerFullName,
                ms_merchant_account.PhoneNumber,
                ms_merchant_account.Latitude,
                ms_merchant_account.Longitude,
                ms_merchant_account.StoreAddress,
                ms_merchant_account.StoreImage,
                ms_merchant_account.StoreAddressNote,
                ms_merchant_account.CreatedDate AS RegisterDate,
                ms_merchant_account.LastPing,
                (
                    SELECT CONCAT(IFNULL(COUNT(StockOrderID), 0), ' order - (Rp ', IFNULL(FORMAT(SUM(NettPrice), 0), 0), ')')
                    FROM tx_merchant_order
                    WHERE MerchantID = tmo.MerchantID
                    AND StatusOrderID = 'S018'
                ) AS OrderSelesai,
                (
                    SELECT CONCAT(IFNULL(COUNT(StockOrderID), 0), ' order - (Rp ', IFNULL(FORMAT(SUM(NettPrice), 0), 0), ')')
                    FROM tx_merchant_order
                    WHERE MerchantID = tmo.MerchantID
                    AND StatusOrderID = 'S011'
                ) AS OrderBatal,
                (
                    SELECT GROUP_CONCAT(DISTINCT ms_payment_method.PaymentMethodName SEPARATOR ', ')
                    FROM tx_merchant_order
                    JOIN ms_payment_method ON ms_payment_method.PaymentMethodID = tx_merchant_order.PaymentMethodID
                    WHERE MerchantID = tmo.MerchantID
                ) AS OrderPaymentMethod
            ")
            ->first();

        $sql->OrderDetail = DB::table('tx_merchant_order_detail')
            ->join('ms_product', 'ms_product.ProductID', 'tx_merchant_order_detail.ProductID')
            ->where('tx_merchant_order_detail.StockOrderID', $stockOrderID)
            ->select('ms_product.ProductName', 'ms_product.ProductImage', 'tx_merchant_order_detail.PromisedQuantity', 'tx_merchant_order_detail.Nett', DB::raw("tx_merchant_order_detail.PromisedQuantity * tx_merchant_order_detail.Nett AS TotalPriceProduct"))
            ->get()->toArray();

        // dd($sql);

        return view('distribution.validation.detail', [
            'data' => $sql
        ]);
    }

    public function updateValidationRestock(Request $request, $stockOrderID, RestockService $restockService)
    {
        $isValid = $request->input('is_valid');
        $validationNotes = $request->input('validation_notes');
        $update = $restockService->updateRestockValidation($stockOrderID, $isValid, $validationNotes);
        if ($update) {
            return redirect()->route('distribution.validationRestock')->with('success', 'Data PO berhasil divalidasi');
        } else {
            return redirect()->route('distribution.validationRestock')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function restock()
    {
        return view('distribution.restock.index');
    }

    public function getRestockByStatus(Request $request, $statusOrder)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $fromShipmentDate = $request->input('fromShipmentDate');
        $toShipmentDate = $request->input('toShipmentDate');
        $paymentMethodId = $request->input('paymentMethodId');

        $sqlGetRestock = DB::table('tx_merchant_order')
            ->leftJoin('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', 'tx_merchant_order.MerchantID')
            ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', 'ms_distributor_merchant_grade.GradeID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->leftJoin('ms_sales', 'ms_sales.SalesCode', '=', 'tx_merchant_order.SalesCode')
            ->where('ms_merchant_account.IsTesting', 0)
            ->where('tx_merchant_order.StatusOrderID', '=', $statusOrder)
            ->select('tx_merchant_order.StockOrderID', 'tx_merchant_order.CreatedDate', 'ms_distributor.DistributorName', 'tx_merchant_order.ShipmentDate', 'tx_merchant_order.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.Partner', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'tx_merchant_order.CancelReasonNote', 'tx_merchant_order.StatusOrderID', 'tx_merchant_order.TotalPrice', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.DiscountVoucher', 'tx_merchant_order.NettPrice', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.DeliveryFee', 'ms_payment_method.PaymentMethodName', 'tx_merchant_order.SalesCode as ReferralCode', 'ms_sales.SalesName', 'ms_distributor_grade.Grade');

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlGetRestock->where('ms_distributor.Depo', '=', $depoUser);
        }

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlGetRestock->whereDate('tx_merchant_order.CreatedDate', '>=', $fromDate)
                ->whereDate('tx_merchant_order.CreatedDate', '<=', $toDate);
        }
        if ($fromShipmentDate != '' && $toShipmentDate != '') {
            $sqlGetRestock->whereDate('tx_merchant_order.ShipmentDate', '>=', $fromShipmentDate)
                ->whereDate('tx_merchant_order.ShipmentDate', '<=', $toShipmentDate);
        }

        if ($paymentMethodId != null) {
            $sqlGetRestock->where('tx_merchant_order.PaymentMethodID', '=', $paymentMethodId);
        }

        // Get data response
        $data = $sqlGetRestock;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('CreatedDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('Grade', function ($data) {
                    if ($data->Grade != null) {
                        $grade = $data->Grade;
                    } else {
                        $grade = 'Retail';
                    }
                    return $grade;
                })
                ->addColumn('Sales', function ($data) {
                    return $data->ReferralCode . ' ' . $data->SalesName;
                })
                ->editColumn('TotalTrx', function ($data) {
                    return $data->TotalPrice - $data->DiscountPrice - $data->DiscountVoucher + $data->ServiceChargeNett + $data->DeliveryFee;
                })
                ->editColumn('Partner', function ($data) {
                    if ($data->Partner != null) {
                        $partner = '<a class="badge badge-info">' . $data->Partner . '</a>';
                    } else {
                        $partner = '';
                    }
                    return $partner;
                })
                ->editColumn('ShipmentDate', function ($data) {
                    return date('d M Y', strtotime($data->ShipmentDate));
                })
                ->addColumn('Invoice', function ($data) {
                    if ($data->StatusOrderID == "S012" || $data->StatusOrderID == "S018") {
                        $textBtn = "Invoice";
                    } else {
                        $textBtn = "Proforma";
                    }
                    $stockOrderId = '<a href="/restock/invoice/' . $data->StockOrderID . '" target="_blank" class="btn btn-sm btn-info">' . $textBtn . '</a>';
                    return $stockOrderId;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a class="btn btn-sm btn-secondary" href="/distribution/restock/detail/' . $data->StockOrderID . '">Lihat</a>';
                    return $actionBtn;
                })
                ->filterColumn('tx_merchant_order.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tx_merchant_order.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->filterColumn('tx_merchant_order.ShipmentDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tx_merchant_order.ShipmentDate,'%d-%b-%Y') like ?", ["%$keyword%"]);
                })
                ->filterColumn('TotalTrx', function ($query, $keyword) {
                    $query->whereRaw("tx_merchant_order.TotalPrice - tx_merchant_order.DiscountPrice - tx_merchant_order.DiscountVoucher + tx_merchant_order.ServiceChargeNett + tx_merchant_order.DeliveryFee like ?", ["%$keyword%"]);
                })
                ->filterColumn('Sales', function ($query, $keyword) {
                    $sql = "CONCAT(ms_merchant_account.ReferralCode,' - ',ms_sales.SalesName)  like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['Invoice', 'Partner', 'Action'])
                ->make(true);
        }
    }

    public function getAllRestockAndDO(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $filterBy = $request->input('filterBy');

        $startDate = new DateTime($fromDate) ?? new DateTime();
        $endDate = new DateTime($toDate) ?? new DateTime();
        $startDateFormat = $startDate->format('Y-m-d');
        $endDateFormat = $endDate->format('Y-m-d');

        $sqlAllRestockAndDO = DB::table('tx_merchant_order')
            ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', 'tx_merchant_order.MerchantID')
            ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', 'ms_distributor_merchant_grade.GradeID')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'tx_merchant_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
            ->leftJoin('ms_status_order', 'ms_status_order.StatusOrderID', 'tx_merchant_order.StatusOrderID')
            ->leftJoin('tx_merchant_delivery_order as tmdo', function ($join) {
                $join->on('tmdo.StockOrderID', 'tx_merchant_order.StockOrderID');
                // $join->where('tmdo.StatusDO', '!=', 'S028');
            })
            ->leftJoin('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderID', 'tmdo.DeliveryOrderID')
            ->leftJoin('tx_merchant_expedition_detail', function ($join) {
                $join->on('tx_merchant_expedition_detail.DeliveryOrderDetailID', 'tx_merchant_delivery_order_detail.DeliveryOrderDetailID');
                $join->whereRaw("(tx_merchant_expedition_detail.StatusExpeditionDetail = 'S030' OR tx_merchant_expedition_detail.StatusExpeditionDetail = 'S031')");
            })
            ->leftJoin('ms_product', 'ms_product.ProductID', 'tx_merchant_delivery_order_detail.ProductID')
            ->leftJoin('ms_user', 'ms_user.UserID', 'tmdo.DriverID')
            ->leftJoin('ms_vehicle', 'ms_vehicle.VehicleID', 'tmdo.VehicleID')
            ->leftJoin('ms_sales', 'ms_sales.SalesCode', '=', 'tx_merchant_order.SalesCode')
            ->leftJoin('tx_merchant_order_detail', function ($join) {
                $join->on('tx_merchant_order_detail.StockOrderID', 'tmdo.StockOrderID');
                $join->on('tx_merchant_order_detail.ProductID', 'tx_merchant_delivery_order_detail.ProductID');
            })
            ->where('ms_merchant_account.IsTesting', 0)
            ->select(
                'tx_merchant_order.StockOrderID',
                'tx_merchant_order.CreatedDate',
                'ms_distributor.DistributorName',
                'tx_merchant_order.MerchantID',
                'ms_merchant_account.StoreName',
                'ms_distributor_grade.Grade',
                'ms_merchant_account.OwnerFullName',
                'ms_merchant_account.PhoneNumber',
                'ms_merchant_account.Partner',
                'tx_merchant_order.StatusOrderID',
                'ms_status_order.StatusOrder',
                'tmdo.DeliveryOrderID',
                'tmdo.Discount',
                'tmdo.ServiceCharge',
                'tmdo.DeliveryFee',
                'ms_product.ProductName',
                'tx_merchant_delivery_order_detail.Qty',
                'tx_merchant_order_detail.PromisedQuantity',
                'tx_merchant_delivery_order_detail.Price',
                'tx_merchant_expedition_detail.ReceiptImage',
                DB::raw("tx_merchant_order.TotalPrice - tx_merchant_order.DiscountPrice - tx_merchant_order.DiscountVoucher + tx_merchant_order.ServiceChargeNett + tx_merchant_order.DeliveryFee AS TotalTrx"),
                DB::raw("tmdo.CreatedDate as TanggalDO"),
                DB::raw("tx_merchant_order_detail.PromisedQuantity * tx_merchant_order_detail.Nett AS TotalPricePO"),
                DB::raw("tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price AS TotalPriceDO"),
                DB::raw("CASE WHEN tmdo.StatusDO = 'S024' THEN 'Dalam Pengiriman' 
                        WHEN tmdo.StatusDO = 'S025' THEN 'Selesai' 
                        WHEN tmdo.StatusDO = 'S026' THEN 'Dibatalkan' 
                        WHEN tmdo.StatusDO = 'S027' THEN 'Permintaan Batal' 
                        WHEN tmdo.StatusDO = 'S028' THEN 'Menunggu Konfirmasi' 
                        ELSE '' END AS StatusDO"),
                DB::raw("
                (
                    SELECT CONCAT('DO ke-', COUNT(*)) FROM tx_merchant_delivery_order
                    WHERE tx_merchant_delivery_order.CreatedDate <= tmdo.CreatedDate
                    AND tx_merchant_delivery_order.StockOrderID = tmdo.StockOrderID
                ) AS UrutanDO
                "),
                DB::raw("(
                    SELECT ms_stock_product_log.PurchasePrice 
                    FROM ms_stock_product_log 
                    WHERE MerchantExpeditionDetailID = tx_merchant_expedition_detail.MerchantExpeditionDetailID 
                    LIMIT 1) AS PurchasePrice
                "),
                'ms_user.Name',
                'ms_vehicle.VehicleName',
                'tmdo.VehicleLicensePlate',
                'tx_merchant_order.SalesCode as ReferralCode',
                'ms_sales.SalesName'
            );

        if ($filterBy == "PO" || $filterBy == "") {
            $sqlAllRestockAndDO->whereDate('tx_merchant_order.CreatedDate', '>=', $startDateFormat)
                ->whereDate('tx_merchant_order.CreatedDate', '<=', $endDateFormat);
        } elseif ($filterBy == "DO") {
            $sqlAllRestockAndDO->whereDate('tmdo.CreatedDate', '>=', $startDateFormat)
                ->whereDate('tmdo.CreatedDate', '<=', $endDateFormat);
        }


        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlAllRestockAndDO->where('ms_distributor.Depo', '=', $depoUser);
        }

        // Get data response
        $data = $sqlAllRestockAndDO;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('CreatedDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->CreatedDate));
                })
                ->addColumn('MarginReal', function ($data) {
                    if ($data->PurchasePrice == null) {
                        $marginReal = "";
                    } else {
                        if ($data->Qty == null) {
                            $marginReal = "";
                        } else {
                            $marginReal = (($data->Price - $data->PurchasePrice) * $data->Qty) - $data->Discount;
                        }
                    }
                    return $marginReal;
                })
                ->addColumn('MarginRealPercentage', function ($data) {
                    $marginReal = (($data->Price - $data->PurchasePrice) * $data->Qty) - $data->Discount;
                    if ($data->PurchasePrice == null) {
                        $marginRealPercentage = "";
                    } else {
                        if ($data->TotalPriceDO == 0) {
                            $marginRealPercentage = "";
                        } else {
                            $marginRealPercentage = round($marginReal / $data->TotalPriceDO * 100, 2);
                        }
                    }

                    return $marginRealPercentage;
                })
                ->editColumn('Grade', function ($data) {
                    if ($data->Grade != null) {
                        $grade = $data->Grade;
                    } else {
                        $grade = 'Retail';
                    }
                    return $grade;
                })
                ->addColumn('Sales', function ($data) {
                    return $data->ReferralCode . ' ' . $data->SalesName;
                })
                ->editColumn('Partner', function ($data) {
                    if ($data->Partner != null) {
                        $partner = '<a class="badge badge-info">' . $data->Partner . '</a>';
                    } else {
                        $partner = '';
                    }
                    return $partner;
                })
                ->editColumn('StatusOrder', function ($data) {
                    $pesananBaru = "S009";
                    $dikonfirmasi = "S010";
                    $dalamProses = "S023";
                    $dikirim = "S012";
                    $selesai = "S018";
                    $dibatalkan = "S011";

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
                ->editColumn('TanggalDO', function ($data) {
                    if ($data->TanggalDO) {
                        $tanggalDO = date('d M Y H:i', strtotime($data->TanggalDO));
                    } else {
                        $tanggalDO = "";
                    }

                    return $tanggalDO;
                })
                ->editColumn('StatusDO', function ($data) {
                    if ($data->StatusDO == "Dalam Pengiriman") {
                        $statusOrder = '<span class="badge badge-warning">' . $data->StatusDO . '</span>';
                    } elseif ($data->StatusDO == "Selesai") {
                        $statusOrder = '<span class="badge badge-success">' . $data->StatusDO . '</span>';
                    } else {
                        $statusOrder = '<span class="badge badge-info">' . $data->StatusDO . '</span>';
                    }

                    return $statusOrder;
                })
                ->editColumn('ReceiptImage', function ($data) {
                    if ($data->ReceiptImage == null) {
                        $receiptImage = "";
                    } else {
                        $receiptImage = '<a target="_blank" href="' . $this->baseImageUrl . 'receipt_image_expedition/' . $data->ReceiptImage . '">Lihat Bukti</a>';
                    }
                    return $receiptImage;
                })
                ->filterColumn('tx_merchant_order.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tx_merchant_order.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->filterColumn('TotalTrx', function ($query, $keyword) {
                    $query->whereRaw("tx_merchant_order.TotalPrice - tx_merchant_order.DiscountPrice - tx_merchant_order.DiscountVoucher + tx_merchant_order.ServiceChargeNett + tx_merchant_order.DeliveryFee like ?", ["%$keyword%"]);
                })
                ->filterColumn('TanggalDO', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tmdo.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->filterColumn('StatusDO', function ($query, $keyword) {
                    $query->whereRaw("ms_status_order.StatusOrder like ?", ["%$keyword%"]);
                })
                // ->filterColumn('TotalPrice', function ($query, $keyword) {
                //     $query->whereRaw("tx_merchant_delivery_order_detail.Qty * tx_merchant_delivery_order_detail.Price like ?", ["%$keyword%"]);
                // })
                ->filterColumn('Sales', function ($query, $keyword) {
                    $sql = "CONCAT(ms_merchant_account.ReferralCode,' - ',ms_sales.SalesName)  like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['Partner', 'StatusOrder', 'StatusDO', 'ReceiptImage'])
                ->make(true);
        }
    }

    public function restockDetail($stockOrderID, HaistarService $haistarService)
    {
        $merchantOrder = DB::table('tx_merchant_order')
            ->leftJoin('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->leftJoin('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_order.StatusOrderID')
            ->leftJoin('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', 'tx_merchant_order.DistributorID')
            ->where('tx_merchant_order.StockOrderID', '=', $stockOrderID)
            ->select('ms_merchant_account.StoreImage', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'tx_merchant_order.MerchantID', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'ms_merchant_account.StoreAddressNote', 'ms_merchant_account.Latitude', 'ms_merchant_account.Longitude', 'tx_merchant_order.StockOrderID', 'tx_merchant_order.StatusOrderID', 'tx_merchant_order.PaymentMethodID', 'tx_merchant_order.TotalPrice', 'tx_merchant_order.NettPrice', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.DiscountVoucher', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.DeliveryFee', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.ShipmentDate', 'tx_merchant_order.MerchantNote', 'tx_merchant_order.DistributorNote', 'tx_merchant_order.Rating', 'tx_merchant_order.Feedback', 'tx_merchant_order.CancelReasonNote', 'ms_status_order.StatusOrder', 'ms_payment_method.PaymentMethodName', 'ms_distributor.IsHaistar')
            ->first();

        $merchantOrderDetail = DB::table('tx_merchant_order_detail')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_order_detail.ProductID')
            ->where('tx_merchant_order_detail.StockOrderID', '=', $stockOrderID)
            ->select('tx_merchant_order_detail.ProductID', 'ms_product.ProductName', 'ms_product.ProductImage', 'tx_merchant_order_detail.Quantity', 'tx_merchant_order_detail.PromisedQuantity', 'tx_merchant_order_detail.Price', 'tx_merchant_order_detail.Discount', 'tx_merchant_order_detail.Nett')
            ->get();

        $deliveryOrder = DB::table('tx_merchant_delivery_order AS do')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'do.StatusDO')
            ->leftJoin('ms_user AS driver', 'driver.UserID', 'do.DriverID')
            ->leftJoin('ms_user AS helper', 'helper.UserID', 'do.HelperID')
            ->leftJoin('ms_vehicle', 'ms_vehicle.VehicleID', 'do.VehicleID')
            ->where('do.StockOrderID', '=', $stockOrderID)
            ->where('do.StatusDO', '!=', 'S026')
            ->select('do.*', 'ms_status_order.StatusOrder', 'driver.Name', 'helper.Name AS HelperName', 'ms_vehicle.VehicleName')
            ->get();

        foreach ($deliveryOrder as $key => $value) {
            $dateDlmPengiriman = DB::table('tx_merchant_delivery_order_log')
                ->where('DeliveryOrderID', $value->DeliveryOrderID)
                ->where('StatusDO', 'S024')
                ->selectRaw("MAX(ProcessTime) AS DateKirim")->first();
            $value->DateKirim = $dateDlmPengiriman->DateKirim;

            $deliveryOrderDetail = DB::table('tx_merchant_delivery_order_detail')
                ->leftJoin('ms_status_order', 'ms_status_order.StatusOrderID', 'tx_merchant_delivery_order_detail.StatusExpedition')
                ->join('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_delivery_order_detail.ProductID')
                ->join('tx_merchant_delivery_order', 'tx_merchant_delivery_order.DeliveryOrderID', '=', 'tx_merchant_delivery_order_detail.DeliveryOrderID')
                ->where('tx_merchant_delivery_order_detail.DeliveryOrderID', '=', $value->DeliveryOrderID)
                ->where('tx_merchant_delivery_order_detail.StatusExpedition', '!=', 'S037')
                ->select('tx_merchant_delivery_order_detail.ProductID', 'tx_merchant_delivery_order_detail.Qty', 'tx_merchant_delivery_order_detail.Price', 'ms_product.ProductName', 'ms_product.ProductImage', 'tx_merchant_delivery_order_detail.Distributor', 'ms_status_order.StatusOrder')
                ->get()->toArray();
            $value->DetailProduct = $deliveryOrderDetail;

            $subTotal = 0;
            foreach ($deliveryOrderDetail as $key => $item) {
                $subTotal += $item->Price * $item->Qty;
                $orderQty = DB::table('tx_merchant_order_detail')
                    ->leftJoin('tx_merchant_delivery_order', function ($join) {
                        $join->on('tx_merchant_delivery_order.StockOrderID', '=', 'tx_merchant_order_detail.StockOrderID');
                        // $join->where('tx_merchant_delivery_order.StatusDO', 'S025');
                    })
                    ->leftJoin('tx_merchant_delivery_order_detail', function ($join) use ($item) {
                        $join->on('tx_merchant_delivery_order_detail.DeliveryOrderID', '=', 'tx_merchant_delivery_order.DeliveryOrderID');
                        $join->where('tx_merchant_delivery_order_detail.ProductID', $item->ProductID);
                    })
                    ->where('tx_merchant_order_detail.StockOrderID', '=', $stockOrderID)
                    ->where('tx_merchant_order_detail.ProductID', '=', $item->ProductID)
                    ->select(
                        'tx_merchant_order_detail.PromisedQuantity',
                        'tx_merchant_order_detail.ProductID',
                        DB::raw("IFNULL(SUM(IF(tx_merchant_delivery_order.StatusDO = 'S025', tx_merchant_delivery_order_detail.Qty, 0)), 0) AS QtyDOSelesai"),
                        DB::raw("IFNULL(SUM(IF(tx_merchant_delivery_order.StatusDO = 'S024', tx_merchant_delivery_order_detail.Qty, 0)), 0) AS QtyDODlmPengiriman")
                    )
                    ->groupBy('tx_merchant_order_detail.PromisedQuantity', 'tx_merchant_order_detail.ProductID')
                    ->first();
                $item->OrderQty = $orderQty->PromisedQuantity;
                $item->QtyDOSelesai = $orderQty->QtyDOSelesai;
                $item->QtyDODlmPengiriman = $orderQty->QtyDODlmPengiriman;

                $item->IsHaistarProduct = 0;

                if ($merchantOrder->IsHaistar == 1) {
                    $productHaistar = $haistarService->haistarGetStock($item->ProductID);
                    if ($productHaistar->status == "success") {
                        $item->IsHaistarProduct = 1;
                    }
                }
            }
            $dueDate = strtotime("$value->FinishDate +5 day");
            if ($value->IsPaid == 0) {
                $timeDiff = time() - $dueDate;
            } else {
                $timeDiff = strtotime($value->PaymentDate) - $dueDate;
            }
            $lateDays = round($timeDiff / (60 * 60 * 24));

            $grandTotal = $subTotal + $value->ServiceCharge + $value->DeliveryFee - $value->Discount;

            if ($lateDays > 0 && $merchantOrder->PaymentMethodID == 14) {
                $sqlLateBillFee = DB::table('tx_merchant_delivery_order_bill')
                    ->where('PaymentMethodID', $merchantOrder->PaymentMethodID)
                    ->whereRaw("$lateDays BETWEEN OverdueStartDay AND OverdueToDay")
                    ->select('TypeFee', 'NominalFee')
                    ->first();

                if ($sqlLateBillFee->TypeFee == "PERCENT") {
                    $lateFee = $grandTotal * $sqlLateBillFee->NominalFee / 100;
                    $grandTotal += $lateFee;
                }

                if ($sqlLateBillFee->TypeFee == 'NOMINAL') {
                    $lateFee = $sqlLateBillFee->NominalFee;
                    $grandTotal += $lateFee;
                }
            } else {
                $lateFee = 0;
            }

            $value->SubTotal = $subTotal;
            $value->LateFee = $lateFee;
            $value->GrandTotal = $grandTotal;
        }

        $productAddDO = DB::table('tx_merchant_order_detail')
            ->join('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_order_detail.ProductID')
            ->where('tx_merchant_order_detail.StockOrderID', '=', $stockOrderID)
            ->select('tx_merchant_order_detail.ProductID', 'tx_merchant_order_detail.PromisedQuantity', 'tx_merchant_order_detail.Nett', 'ms_product.ProductName', 'ms_product.ProductImage')
            ->get();

        $promisedQty = 0;
        $deliveryOrderQty = 0;

        $isHasHaistar = 0;
        foreach ($productAddDO as $key => $value) {
            $productQtyDO = DB::table('tx_merchant_delivery_order')
                ->join('tx_merchant_delivery_order_detail', 'tx_merchant_delivery_order_detail.DeliveryOrderID', '=', 'tx_merchant_delivery_order.DeliveryOrderID')
                ->where('tx_merchant_delivery_order.StockOrderID', '=', $stockOrderID)
                ->where('tx_merchant_delivery_order_detail.ProductID', '=', $value->ProductID)
                // ->where('tx_merchant_delivery_order.StatusDO', '!=', 'S026')
                ->where('tx_merchant_delivery_order_detail.StatusExpedition', '!=', 'S037')
                ->selectRaw('IFNULL(SUM(tx_merchant_delivery_order_detail.Qty), 0) as Qty')
                ->first();
            $value->QtyDO = $productQtyDO->Qty;

            $promisedQty += $value->PromisedQuantity;
            $deliveryOrderQty += $productQtyDO->Qty;

            $value->IsHaistarProduct = 0;

            if ($merchantOrder->IsHaistar == 1) {
                $productHaistar = $haistarService->haistarGetStock($value->ProductID);
                if ($productHaistar->status == "success") {
                    $value->IsHaistarProduct = 1;
                    $isHasHaistar = 1;
                }
            }
        }

        $drivers = DB::table('ms_user')
            ->where('RoleID', 'DRV')
            ->where('IsTesting', 0)
            ->select('UserID', 'Name')
            ->orderBy('Name');

        $helpers = DB::table('ms_user')
            ->where('RoleID', 'HLP')
            ->where('IsTesting', 0)
            ->select('UserID', 'Name')
            ->orderBy('Name');

        if (Auth::user()->Depo == "ALL") {
            $dataDrivers = $drivers->get();
            $dataHelpers = $helpers->get();
        } else {
            $dataDrivers = $drivers->where('Depo', Auth::user()->Depo)->get();
            $dataHelpers = $helpers->where('Depo', Auth::user()->Depo)->get();
        }

        $vehicles = DB::table('ms_vehicle')
            ->whereNotIn('VehicleID', [1, 2, 3])
            ->select('*')
            ->orderBy('VehicleName')->get();

        return view('distribution.restock.detail', [
            'stockOrderID' => $stockOrderID,
            'merchantOrder' => $merchantOrder,
            'merchantOrderDetail' => $merchantOrderDetail,
            'deliveryOrder' => $deliveryOrder,
            'productAddDO' => $productAddDO,
            'isHasHaistar' => $isHasHaistar,
            'promisedQty' => $promisedQty,
            'deliveryOrderQty' => $deliveryOrderQty,
            'drivers' => $dataDrivers,
            'helpers' => $dataHelpers,
            'vehicles' => $vehicles
        ]);
    }

    public function updateStatusRestock(Request $request, $stockOrderID, $status, DeliveryOrderService $deliveryOrderService)
    {
        $txMerchantOrder = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->where('StockOrderID', '=', $stockOrderID)
            ->select('tx_merchant_order.PaymentMethodID', 'tx_merchant_order.DistributorID', 'tx_merchant_order.MerchantID', 'ms_merchant_account.MerchantFirebaseToken', 'ms_distributor.DistributorName', 'ms_payment_method.PaymentMethodCategory')->first();

        $txMerchantOrderDetail = DB::table('tx_merchant_order_detail')
            ->where('StockOrderID', '=', $stockOrderID)
            ->select('*')->get();

        $pesananBaru = "S009";
        $dikonfirmasi = "S010";
        $dalamProses = "S023";
        $dikirim = "S012";
        $selesai = "S018";
        $dibatalkan = "S011";
        $authUser = Auth::user()->Name . "-" . Auth::user()->RoleID . "-" . Auth::user()->Depo;

        $baseImageUrl = config('app.base_image_url');

        if ($status == "reject") {
            $request->validate([
                'cancel_reason' => 'required'
            ]);

            // data untuk update tx merchant order
            $data = [
                'StatusOrderID' => $dibatalkan,
                'CancelReasonID' => 'CO-004',
                'CancelReasonNote' => $request->input('cancel_reason')
            ];

            // data untuk insert tx merchant order log
            $dataLog = [
                'StockOrderId' => $stockOrderID,
                'DistributorID' => $txMerchantOrder->DistributorID,
                'MerchantID' => $txMerchantOrder->MerchantID,
                'StatusOrderId' => $dibatalkan,
                'ProcessTime' => date("Y-m-d H:i:s"),
                'ActionBy' => $authUser
            ];

            try {
                DB::transaction(function () use ($stockOrderID, $data, $dataLog, $txMerchantOrder, $baseImageUrl) {
                    DB::table('tx_merchant_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update($data);
                    DB::table('tx_merchant_order_log')
                        ->insert($dataLog);

                    $fields = array(
                        'registration_ids' => array($txMerchantOrder->MerchantFirebaseToken),
                        'data' => array(
                            "date" => date("Y-m-d H:i:s"),
                            "merchantID" => $txMerchantOrder->MerchantID,
                            "title" => "Pesanan Anda dibatalkan oleh " . $txMerchantOrder->DistributorName,
                            "body" => "Pesanan Restok Dibatalkan",
                            'large_icon' => $baseImageUrl . 'push/merchant_icon.png'
                        )
                    );

                    $headers = array(
                        'Authorization: key=' . config('app.firebase_auth_token'),
                        'Content-Type: application/json'
                    );

                    $fields = json_encode($fields);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    DB::table('ms_notification_log')->insert([
                        'Target' => $txMerchantOrder->MerchantID,
                        'Message' => 'Pesanan Restok Dibatalkan',
                        'JSONSend' => $fields,
                        'JSONReceive' => $response,
                        'CreatedAt' => date('Y-m-d H:i:s'),
                        'Status' => 'SUCCESS'
                    ]);
                });

                return redirect()->route('distribution.restock')->with('success', 'Data pesanan berhasil dibatalkan');
            } catch (\Throwable $th) {
                return redirect()->route('distribution.restock')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
            }
        } elseif ($status == "approved") {
            if ($txMerchantOrder->PaymentMethodCategory == "CASH") { // kategori cash
                $statusOrder = $dalamProses;
                $titleNotif = "Pesanan Restok Dalam Proses";
                $bodyNotif = "Pesanan Anda sedang diproses " . $txMerchantOrder->DistributorName . " dan akan segera dikirim.";
            } else { // non tunai
                $statusOrder = $dikonfirmasi;
                $titleNotif = "Pesanan Restok Dikonfirmasi dan Menunggu Pembayaran";
                $bodyNotif = "Pesanan Anda telah dikonfirmasi dari " . $txMerchantOrder->DistributorName . ". Silakan periksa kembali pesanan Anda dan segera lakukan pembayaran.";
            }

            // data untuk insert tx merchant order log
            $dataLog = [
                'StockOrderId' => $stockOrderID,
                'DistributorID' => $txMerchantOrder->DistributorID,
                'MerchantID' => $txMerchantOrder->MerchantID,
                'StatusOrderId' => $statusOrder,
                'ProcessTime' => date("Y-m-d H:i:s"),
                'ActionBy' => $authUser
            ];

            try {
                DB::transaction(function () use ($stockOrderID, $statusOrder, $dataLog, $txMerchantOrder, $titleNotif, $bodyNotif, $baseImageUrl, $deliveryOrderService) {
                    if ($txMerchantOrder->PaymentMethodID == 14) {
                        $deliveryOrderService->splitDeliveryOrder($stockOrderID, 3);
                    }

                    DB::table('tx_merchant_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update([
                            'StatusOrderID' => $statusOrder
                        ]);
                    DB::table('tx_merchant_order_log')
                        ->insert($dataLog);

                    $fields = array(
                        'registration_ids' => array($txMerchantOrder->MerchantFirebaseToken),
                        'data' => array(
                            "date" => date("Y-m-d H:i:s"),
                            "merchantID" => $txMerchantOrder->MerchantID,
                            "title" => $titleNotif,
                            "body" => $bodyNotif,
                            'large_icon' => $baseImageUrl . 'push/merchant_icon.png'
                        )
                    );

                    $headers = array(
                        'Authorization: key=' . config('app.firebase_auth_token'),
                        'Content-Type: application/json'
                    );

                    $fields = json_encode($fields);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    DB::table('ms_notification_log')->insert([
                        'Target' => $txMerchantOrder->MerchantID,
                        'Message' => $bodyNotif,
                        'JSONSend' => $fields,
                        'JSONReceive' => $response,
                        'CreatedAt' => date('Y-m-d H:i:s'),
                        'Status' => 'SUCCESS'
                    ]);
                });

                return redirect()->route('distribution.restock')->with('success', 'Data pesanan berhasil diproses');
            } catch (\Throwable $th) {
                return redirect()->route('distribution.restock')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
            }
        } elseif ($status == "send") {
            $request->validate([
                'distributor_note' => 'string|nullable'
            ]);

            // data untuk update tx merchant order
            $data = [
                'StatusOrderID' => $dikirim,
                'DistributorNote' => $request->input('distributor_note')
            ];

            // data untuk update tx merchant order log
            $dataLog = [
                'StockOrderId' => $stockOrderID,
                'DistributorID' => $txMerchantOrder->DistributorID,
                'MerchantID' => $txMerchantOrder->MerchantID,
                'StatusOrderId' => $dikirim,
                'ProcessTime' => date("Y-m-d H:i:s"),
                'ActionBy' => $authUser
            ];

            try {
                DB::transaction(function () use ($stockOrderID, $data, $dataLog) {
                    DB::table('tx_merchant_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update($data);
                    DB::table('tx_merchant_order_log')
                        ->insert($dataLog);
                });

                $fields = array(
                    'registration_ids' => array($txMerchantOrder->MerchantFirebaseToken),
                    'data' => array(
                        "date" => date("Y-m-d H:i:s"),
                        "merchantID" => $txMerchantOrder->MerchantID,
                        "title" => "Pesanan Restok Dikirim",
                        "body" => "Pesanan Anda sedang dikirim menuju alamat Anda oleh " . $txMerchantOrder->DistributorName . ".",
                        'large_icon' => $baseImageUrl . 'push/merchant_icon.png'
                    )
                );

                $headers = array(
                    'Authorization: key=' . config('app.firebase_auth_token'),
                    'Content-Type: application/json'
                );

                $fields = json_encode($fields);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                curl_exec($ch);
                curl_close($ch);

                return redirect()->route('distribution.restock')->with('success', 'Data pesanan berhasil dikirim');
            } catch (\Throwable $th) {
                return redirect()->route('distribution.restock')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
            }
        } elseif ($status == "refund") {
            // data untuk insert tx merchant order log
            $dataLog = [
                'StockOrderId' => $stockOrderID,
                'DistributorID' => $txMerchantOrder->DistributorID,
                'MerchantID' => $txMerchantOrder->MerchantID,
                'StatusOrderId' => $selesai,
                'ProcessTime' => date("Y-m-d H:i:s"),
                'ActionBy' => $authUser
            ];

            try {
                DB::transaction(function () use ($stockOrderID, $selesai, $dataLog) {
                    DB::table('tx_merchant_order')
                        ->where('StockOrderID', '=', $stockOrderID)
                        ->update([
                            'StatusOrderID' => $selesai,
                            'IsRefund' => 1
                        ]);
                    DB::table('tx_merchant_order_log')
                        ->insert($dataLog);
                });
                return redirect()->route('distribution.restock')->with('success', 'Data pesanan berhasil di-refund');
            } catch (\Throwable $th) {
                return redirect()->route('distribution.restock')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
            }
        }
    }

    // Ketika Menyelesaikan DO
    public function updateDeliveryOrder($deliveryOrderId)
    {
        $stockOrderID = DB::table('tx_merchant_delivery_order')
            ->where('DeliveryOrderID', '=', $deliveryOrderId)
            ->select('StockOrderID', 'DriverID', 'VehicleID', 'VehicleLicensePlate')->first();

        try {
            DB::transaction(function () use ($stockOrderID, $deliveryOrderId) {
                DB::table('tx_merchant_delivery_order')
                    ->where('DeliveryOrderID', '=', $deliveryOrderId)
                    ->update([
                        'StatusDO' => 'S025',
                        'FinishDate' => date('Y-m-d H:i:s')
                    ]);

                DB::table('tx_merchant_delivery_order_log')
                    ->insert([
                        'StockOrderID' => $stockOrderID->StockOrderID,
                        'DeliveryOrderID' => $deliveryOrderId,
                        'StatusDO' => 'S025',
                        'DriverID' => $stockOrderID->DriverID,
                        'VehicleID' => $stockOrderID->VehicleID,
                        'VehicleLicensePlate' => $stockOrderID->VehicleLicensePlate,
                        'ActionBy' => 'DISTRIBUTOR'
                    ]);
            });

            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID->StockOrderID])->with('success', 'Delivery Order telah diselesaikan');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID->StockOrderID])->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function createDeliveryOrder(Request $request, $stockOrderID, $depoChannel, HaistarService $haistarService, DeliveryOrderService $deliveryOrderService)
    {
        $baseImageUrl = config('app.base_image_url');

        $msMerchant = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
            ->leftJoin('ms_area', 'ms_area.AreaID', 'ms_merchant_account.AreaID')
            ->where('tx_merchant_order.StockOrderID', '=', $stockOrderID)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.Email', 'ms_merchant_account.MerchantFirebaseToken', 'ms_distributor.DistributorName', 'tx_merchant_order.OrderAddress', 'tx_merchant_order.PaymentMethodID', 'ms_area.PostalCode', 'ms_area.Province', 'ms_area.City', 'ms_area.Subdistrict', 'tx_merchant_order.DistributorNote', 'tx_merchant_order.MerchantNote')
            ->first();

        $newDeliveryOrderID = $deliveryOrderService->generateDeliveryOrderID();

        $createdDateDO = str_replace("T", " ", $request->input('created_date_do'));
        // $vehicleLicensePlate = str_replace(" ", "-", $request->input('license_plate'));

        $productId = $request->input('product_id');
        $qty = $request->input('qty_do');

        $dataDetailDO = array_map(function () {
            return func_get_args();
        }, $productId, $qty);

        if ($depoChannel == "rtmart") {
            $request->validate([
                'created_date_do' => 'required',
                // 'driver' => 'required',
                // 'vehicle' => 'required',
                // 'license_plate' => 'required',
                'qty_do' => 'required',
                'qty_do.*' => 'required|numeric|lte:max_qty_do.*|gte:1'
            ]);

            $dataDO = [
                'DeliveryOrderID' => $newDeliveryOrderID,
                'StockOrderID' => $stockOrderID,
                'StatusDO' => 'S028',
                // 'DriverID' => $request->input('driver'),
                // 'HelperID' => $request->input('helper'),
                // 'VehicleID' => $request->input('vehicle'),
                // 'VehicleLicensePlate' => $vehicleLicensePlate,
                'Distributor' => "RT MART",
                'CreatedDate' => $createdDateDO
            ];

            $validationStatus = true;
            $arrayDetailDO = [];
            foreach ($dataDetailDO as $key => $value) {
                $value = array_combine(['ProductID', 'Qty'], $value);
                $value += ['DeliveryOrderID' => $newDeliveryOrderID];
                $value += ['Distributor' => 'RT MART'];

                $validation = $deliveryOrderService->validateRemainingQty($stockOrderID, "", $value['ProductID'], $value['Qty'], "CreateDO");
                $value += ['Price' => $validation['price']];
                if ($validation['status'] == false) {
                    $validationStatus = false;
                    break;
                }
                array_push($arrayDetailDO, $value);
            }

            $dataLogDO = [
                'StockOrderID' => $stockOrderID,
                'DeliveryOrderID' => $newDeliveryOrderID,
                'StatusDO' => 'S028',
                // 'DriverID' => $request->input('driver'),
                // 'HelperID' => $request->input('helper'),
                // 'VehicleID' => $request->input('vehicle'),
                // 'VehicleLicensePlate' => $vehicleLicensePlate,
                'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo . ' ' . Auth::user()->Name
            ];

            if ($validationStatus == true) {
                try {
                    DB::transaction(function () use ($dataDO, $arrayDetailDO, $dataLogDO) {
                        DB::table('tx_merchant_delivery_order')
                            ->insert($dataDO);
                        DB::table('tx_merchant_delivery_order_detail')
                            ->insert($arrayDetailDO);
                        DB::table('tx_merchant_delivery_order_log')
                            ->insert($dataLogDO);
                    });

                    // $fields = array(
                    //     'registration_ids' => array($msMerchant->MerchantFirebaseToken),
                    //     'data' => array(
                    //         "date" => date("Y-m-d H:i:s"),
                    //         "merchantID" => $msMerchant->MerchantID,
                    //         "title" => "Pesanan Restok Dikirim",
                    //         "body" => "Pesanan Anda sedang dikirim menuju alamat Anda oleh " . $msMerchant->DistributorName . " dengan nomor delivery " . $newDeliveryOrderID . ".",
                    //         'large_icon' => $baseImageUrl . 'push/merchant_icon.png'
                    //     )
                    // );

                    // $headers = array(
                    //     'Authorization: key=' . config('app.firebase_auth_token'),
                    //     'Content-Type: application/json'
                    // );

                    // $fields = json_encode($fields);
                    // $ch = curl_init();
                    // curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                    // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    // curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    // curl_setopt($ch, CURLOPT_POST, TRUE);
                    // curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                    // curl_exec($ch);
                    // curl_close($ch);

                    return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('success', 'Delivery Order berhasil dibuat');
                } catch (\Throwable $th) {
                    return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
                }
            } else {
                return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', 'Quantity yang dikirim tidak mencukupi');
            }
        } elseif ($depoChannel == "haistar") {
            $request->validate([
                'created_date_do' => 'required',
                // 'driver' => 'required',
                // 'vehicle' => 'required',
                // 'license_plate' => 'required',
                'qty_do' => 'required',
                'qty_do.*' => 'required|numeric|lte:max_qty_do.*|gte:1'
            ]);

            $totalPrice = 0;
            $arrayItems = [];
            $objectItems = new stdClass;
            foreach ($dataDetailDO as &$value) {
                $value = array_combine(['ProductID', 'Qty'], $value);

                $value += ['DeliveryOrderID' => $newDeliveryOrderID];
                $value += ['Distributor' => 'HAISTAR'];

                $validation = $deliveryOrderService->validateRemainingQty($stockOrderID, "", $value['ProductID'], $value['Qty'], "CreateDO");
                $value += ['Price' => $validation['price']];

                // $checkStock = $haistarService->haistarGetStock($value['ProductID']);
                // // $stockHaistar = 0;
                // $arrayExistStock = $checkStock->data->detail;
                // $existStock = array_sum(array_column($arrayExistStock, "exist_quantity"));

                // if ($value['Qty'] > $existStock) {
                //     return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', 'Gagal, Stock Haistar tidak mencukupi!');
                // }

                $totalPrice += $value['Qty'] * $value['Price'];

                $objectItems->item_code = $value['ProductID'];
                $objectItems->quantity = $value['Qty'] * 1;
                $objectItems->unit_price = $value['Price'] * 1;

                array_push($arrayItems, clone $objectItems);
            }

            if ($msMerchant->PaymentMethodID == 1) {
                $codPrice = "$totalPrice";
            } else {
                $codPrice = "0";
            }

            // Parameter Push Order Haistar
            // $objectParams = new stdClass;
            // $objectParams->code = $newDeliveryOrderID;
            // $objectParams->cod_price = $codPrice;
            // $objectParams->total_price = $totalPrice;
            // $objectParams->total_product_price = "$totalPrice";
            // $objectParams->items = $arrayItems;

            // $haistarPushOrder = $haistarService->haistarPushOrder($stockOrderID, $objectParams);

            // if ($haistarPushOrder->status == 200) {
            $dataDO = [
                'DeliveryOrderID' => $newDeliveryOrderID,
                'StockOrderID' => $stockOrderID,
                'StatusDO' => 'S028',
                // 'DriverID' => $request->input('driver'),
                // 'HelperID' => $request->input('helper'),
                // 'VehicleID' => $request->input('vehicle'),
                // 'VehicleLicensePlate' => $vehicleLicensePlate,
                'Distributor' => "HAISTAR",
                'CreatedDate' => $createdDateDO
            ];

            $dataLogDO = [
                'StockOrderID' => $stockOrderID,
                'DeliveryOrderID' => $newDeliveryOrderID,
                'StatusDO' => 'S028',
                // 'DriverID' => $request->input('driver'),
                // 'HelperID' => $request->input('helper'),
                // 'VehicleID' => $request->input('vehicle'),
                // 'VehicleLicensePlate' => $vehicleLicensePlate,
                'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo . ' ' . Auth::user()->Name
            ];

            try {
                DB::transaction(function () use ($dataDO, $dataDetailDO, $dataLogDO) {
                    DB::table('tx_merchant_delivery_order')
                        ->insert($dataDO);
                    DB::table('tx_merchant_delivery_order_detail')
                        ->insert($dataDetailDO);
                    DB::table('tx_merchant_delivery_order_log')
                        ->insert($dataLogDO);
                });

                // $fields = array(
                //     'registration_ids' => array($msMerchant->MerchantFirebaseToken),
                //     'data' => array(
                //         "date" => date("Y-m-d H:i:s"),
                //         "merchantID" => $msMerchant->MerchantID,
                //         "title" => "Pesanan Restok Dikirim",
                //         "body" => "Pesanan Anda sedang dikirim menuju alamat Anda oleh " . $msMerchant->DistributorName . " dengan nomor delivery " . $newDeliveryOrderID . ".",
                //         'large_icon' => $baseImageUrl . 'push/merchant_icon.png'
                //     )
                // );

                // $headers = array(
                //     'Authorization: key=' . config('app.firebase_auth_token'),
                //     'Content-Type: application/json'
                // );

                // $fields = json_encode($fields);
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
                // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                // curl_setopt($ch, CURLOPT_HEADER, FALSE);
                // curl_setopt($ch, CURLOPT_POST, TRUE);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                // curl_exec($ch);
                // curl_close($ch);

                return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('success', 'Delivery Order berhasil dibuat');
            } catch (\Throwable $th) {
                return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
            }
            // } else {
            //     return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', $haistarPushOrder->data);
            // }
        } else {
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', 'Gagal');
        }
    }

    // Edit DO Ketika Status Dalam Pengiriman
    public function updateQtyDO(Request $request, $deliveryOrderId, DeliveryOrderService $deliveryOrderService)
    {
        $request->validate([
            'edit_qty_do' => 'required',
            'edit_qty_do.*' => 'required|numeric|lte:max_edit_qty_do.*|gte:0',
            'driver' => 'required',
            'vehicle' => 'required',
            'license_plate' => 'required'
        ]);

        $stockOrderID = DB::table('tx_merchant_delivery_order')
            ->where('DeliveryOrderID', '=', $deliveryOrderId)
            ->select('StockOrderID')->first();

        $qty = $request->input('edit_qty_do');
        $productID = $request->input('product_id');

        $dataUpdateDO = array_map(function () {
            return func_get_args();
        }, $productID, $qty);

        $arrayDetailDO = [];
        $validationStatus = true;
        foreach ($dataUpdateDO as $key => $value) {
            $value = array_combine(['ProductID', 'Qty'], $value);
            $value += ['DeliveryOrderID' => $deliveryOrderId];

            $validation = $deliveryOrderService->validateRemainingQty($stockOrderID->StockOrderID, $deliveryOrderId, $value['ProductID'], $value['Qty'], "EditDetailDO");
            if ($validation['status'] == false) {
                $validationStatus = false;
                break;
            }
            array_push($arrayDetailDO, $value);
        }

        $vehicleLicensePlate = str_replace(" ", "-", $request->input('license_plate'));

        $dataDriver = [
            'DriverID' => $request->input('driver'),
            'HelperID' => $request->input('helper'),
            'VehicleID' => $request->input('vehicle'),
            'VehicleLicensePlate' => $vehicleLicensePlate
        ];

        $dataLogDO = [
            'StockOrderID' => $stockOrderID->StockOrderID,
            'DeliveryOrderID' => $deliveryOrderId,
            'StatusDO' => 'S024',
            'DriverID' => $request->input('driver'),
            'HelperID' => $request->input('helper'),
            'VehicleID' => $request->input('vehicle'),
            'VehicleLicensePlate' => $vehicleLicensePlate,
            'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo . ' ' . Auth::user()->Name
        ];

        if ($validationStatus == true) {
            try {
                DB::transaction(function () use ($arrayDetailDO, $deliveryOrderId, $dataDriver, $dataLogDO) {
                    foreach ($arrayDetailDO as $value) {
                        DB::table('tx_merchant_delivery_order_detail')
                            ->where('DeliveryOrderID', '=', $value['DeliveryOrderID'])
                            ->where('ProductID', '=', $value['ProductID'])
                            ->update([
                                'Qty' => $value['Qty']
                            ]);
                    }
                    DB::table('tx_merchant_delivery_order')
                        ->where('DeliveryOrderID', '=', $deliveryOrderId)
                        ->update($dataDriver);
                    DB::table('tx_merchant_delivery_order_log')
                        ->insert($dataLogDO);
                });

                return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID->StockOrderID])->with('success', 'Data Delivery Order berhasil diubah');
            } catch (\Throwable $th) {
                return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID->StockOrderID])->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
            }
        } else {
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID->StockOrderID])->with('failed', 'Quantity yang diubah tidak mencukupi');
        }
    }

    public function cancelDeliveryOrder(Request $request, $deliveryOrderId, HaistarService $haistarService)
    {
        $cancelReason = $request->input('cancel_reason');

        $deliveryOrder = DB::table('tx_merchant_delivery_order')
            ->where('DeliveryOrderID', '=', $deliveryOrderId)
            ->select('*')->first();

        $dataLogDO = [
            'StockOrderID' => $deliveryOrder->StockOrderID,
            'DeliveryOrderID' => $deliveryOrderId,
            'StatusDO' => 'S027',
            'DriverID' => $deliveryOrder->DriverID,
            'VehicleID' => $deliveryOrder->VehicleID,
            'VehicleLicensePlate' => $deliveryOrder->VehicleLicensePlate,
            'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo . ' ' . Auth::user()->Name
        ];

        $haistarCancelOrder = $haistarService->haistarCancelOrder($deliveryOrderId, $cancelReason);

        if ($haistarCancelOrder->status == 200) {
            try {
                DB::transaction(function () use ($deliveryOrderId, $cancelReason, $dataLogDO) {
                    DB::table('tx_merchant_delivery_order')
                        ->where('DeliveryOrderID', '=', $deliveryOrderId)
                        ->update([
                            'StatusDO' => 'S027',
                            'CancelReason' => $cancelReason
                        ]);
                    DB::table('tx_merchant_delivery_order_log')
                        ->insert($dataLogDO);
                });
                return redirect()->route('distribution.restockDetail', ['stockOrderID' => $deliveryOrder->StockOrderID])->with('success', 'Permintaan Batal Data Delivery Order berhasil');
            } catch (\Throwable $th) {
                return redirect()->route('distribution.restockDetail', ['stockOrderID' => $deliveryOrder->StockOrderID])->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
            }
        } else {
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $deliveryOrder->StockOrderID])->with('failed', 'Gagal, terjadi kesalahan');
        }
    }

    public function rejectRequestDO($deliveryOrderId, Request $request, DeliveryOrderService $deliveryOrderService)
    {
        $request->validate([
            'cancel_reason' => 'required'
        ]);

        $cancelReason = $request->input('cancel_reason');
        $stockOrderId = $request->input('stock_order_id');

        try {
            $deliveryOrderService->rejectRequestDeliveryOrder($deliveryOrderId, $cancelReason, $stockOrderId);
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderId])->with('success', 'Delivery Order berhasil dibatalkan');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderId])->with('failed', 'Gagal, terjadi kesalahan');
        }
    }

    public function confirmRequestDO($deliveryOrderId, $depoChannel, Request $request, DeliveryOrderService $deliveryOrderService, HaistarService $haistarService)
    {
        $request->validate([
            'driver' => 'required',
            'vehicle' => 'required',
            'license_plate' => 'required'
        ]);

        $newDeliveryOrderID = $deliveryOrderService->generateDeliveryOrderID();

        $stockOrderID = $request->input('stock_order_id');
        $arrProductRTmart = $request->input('product_id_rtmart');
        $arrQtyRTmart = $request->input('qty_request_do_rtmart');
        $arrPriceRTmart = $request->input('price_rtmart');
        $arrProductHaistar = $request->input('product_id_haistar');
        $arrQtyHaistar = $request->input('qty_request_do_haistar');
        $arrPriceHaistar = $request->input('price_haistar');
        $driverID = $request->input('driver');
        $helperID = $request->input('helper');
        $vehicleID = $request->input('vehicle');
        $licensePlate = $request->input('license_plate');
        $createdDate = $request->input('created_date');

        $getPaymentMethod = DB::table('tx_merchant_order')
            ->where('StockOrderID', $stockOrderID)
            ->select('PaymentMethodID')
            ->first();

        if ($depoChannel == "rtmart") {
            $request->validate([
                'qty_request_do_rtmart' => 'required',
                'qty_request_do_rtmart.*' => 'required|numeric|lte:max_qty_request_do_rtmart.*|gte:0'
            ]);

            // generate DO ID
            if ($arrProductHaistar == null) {
                $confirmDeliveryOrderID = $deliveryOrderId;
            } else {
                $confirmDeliveryOrderID = $newDeliveryOrderID;
            }

            $distributor = "RT MART";
            $dataDetailDO = $deliveryOrderService->dataDetailConfirmDO($deliveryOrderId, $arrProductRTmart, $arrQtyRTmart);
        } elseif ($depoChannel == "haistar") {
            $request->validate([
                'qty_request_do_haistar' => 'required',
                'qty_request_do_haistar.*' => 'required|numeric|lte:max_qty_request_do_haistar.*|gte:0'
            ]);
            if ($arrProductRTmart == null) {
                $confirmDeliveryOrderID = $deliveryOrderId;
            } else {
                $confirmDeliveryOrderID = $newDeliveryOrderID;
            }

            $dataDetailDO = $deliveryOrderService->dataDetailConfirmDO($deliveryOrderId, $arrProductHaistar, $arrQtyHaistar);
            $distributor = "HAISTAR";
        } else {
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', 'Gagal, terjadi kesalahan');
        }

        $dataDO = [
            'StockOrderID' => $stockOrderID,
            'StatusDO' => 'S024',
            'DriverID' => $driverID,
            'HelperID' => $helperID,
            'VehicleID' => $vehicleID,
            'VehicleLicensePlate' => $licensePlate,
            'CreatedDate' => $createdDate,
            'Distributor' => $distributor
        ];

        $dataLogDO = [
            'StockOrderID' => $stockOrderID,
            'DeliveryOrderID' => $confirmDeliveryOrderID,
            'StatusDO' => 'S024',
            'DriverID' => $driverID,
            'HelperID' => $helperID,
            'VehicleID' => $vehicleID,
            'VehicleLicensePlate' => $licensePlate,
            'ActionBy' => 'DISTRIBUTOR ' . Auth::user()->Depo . ' ' . Auth::user()->Name
        ];

        $validationStatus = true;
        $arrayDetailDO = [];
        foreach ($dataDetailDO as &$value) {
            $value = array_combine(['ProductID', 'Qty', 'DeliveryOrderID'], $value);

            $validation = $deliveryOrderService->validateRemainingQty($stockOrderID, $deliveryOrderId, $value['ProductID'], $value['Qty'], "ConfirmRequestDO");
            $value += ['Price' => $validation['price']];
            if ($validation['status'] == false) {
                $validationStatus = false;
                break;
            }
            array_push($arrayDetailDO, $value);
        }

        if ($depoChannel == "haistar") {
            $totalPrice = 0;
            $arrayItems = [];
            $objectItems = new stdClass;
            foreach ($arrayDetailDO as &$value) {

                $checkStock = $haistarService->haistarGetStock($value['ProductID']);

                $arrayExistStock = $checkStock->data->detail;

                $existStock = array_sum(array_column($arrayExistStock, "exist_quantity"));

                if ((int)$value['Qty'] > $existStock) {
                    return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', 'Gagal, Stock Haistar tidak mencukupi!');
                }
                $totalPrice += (int)$value['Qty'] * (int)$value['Price'];

                $objectItems->item_code = $value['ProductID'];
                $objectItems->quantity = (int)$value['Qty'] * 1;
                $objectItems->unit_price = (int)$value['Price'] * 1;

                array_push($arrayItems, clone $objectItems);
            }

            if ($getPaymentMethod->PaymentMethodID == 1) {
                $codPrice = "$totalPrice";
            } else {
                $codPrice = "0";
            }

            // Parameter Push Order Haistar
            $objectParams = new stdClass;
            $objectParams->code = $confirmDeliveryOrderID;
            $objectParams->cod_price = $codPrice;
            $objectParams->total_price = $totalPrice;
            $objectParams->total_product_price = "$totalPrice";
            $objectParams->items = $arrayItems;

            $haistarPushOrder = $haistarService->haistarPushOrder($stockOrderID, $objectParams);
            $haistarResponse = $haistarPushOrder->status;
        } else {
            $haistarResponse = 400;
        }

        if ($validationStatus == true) {
            if ($haistarResponse == 200 || $depoChannel == "rtmart") {
                try {
                    DB::transaction(function () use ($confirmDeliveryOrderID, $dataDO, $arrayDetailDO, $dataLogDO, $deliveryOrderService) {
                        DB::table('tx_merchant_delivery_order')
                            ->updateOrInsert(
                                [
                                    'DeliveryOrderID' => $confirmDeliveryOrderID
                                ],
                                $dataDO
                            );
                        $deliveryOrderService->updateDataDetailConfirmDO($confirmDeliveryOrderID, $arrayDetailDO);
                        DB::table('tx_merchant_delivery_order_log')
                            ->insert($dataLogDO);
                    });
                    return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('success', 'Permintaan Delivery Order berhasil dikonfirmasi');
                } catch (\Throwable $th) {
                    return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', 'Gagal, terjadi kesalahan');
                }
            } else {
                return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', $haistarPushOrder->data);
            }
        } else {
            return redirect()->route('distribution.restockDetail', ['stockOrderID' => $stockOrderID])->with('failed', 'Quantity yang dikirim tidak mencukupi');
        }
    }

    public function billPayLater()
    {
        return view('distribution.bill.index');
    }

    public function getBillPayLater(PayLaterService $payLaterService, Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $filterIsPaid = $request->input('filterIsPaid');

        $sqlbillPayLater = $payLaterService->billPayLaterGet();

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlbillPayLater->where('ms_distributor.Depo', '=', $depoUser);
        }
        if ($fromDate != '' && $toDate != '') {
            $sqlbillPayLater->whereDate('tx_merchant_delivery_order_log.ProcessTime', '>=', $fromDate)
                ->whereDate('tx_merchant_delivery_order_log.ProcessTime', '<=', $toDate);
        }
        if ($filterIsPaid == "paid") {
            $sqlbillPayLater->where('tmdo.IsPaid', 1);
        } elseif ($filterIsPaid == "unpaid") {
            $sqlbillPayLater->where('tmdo.IsPaid', 0);
        }

        $data = $sqlbillPayLater;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('StockOrderID', function ($data) {
                    $link = '<a href="/distribution/restock/detail/' . $data->StockOrderID . '" target="_blank">' . $data->StockOrderID . '</a>';
                    return $link;
                })
                ->editColumn('FinishDate', function ($data) {
                    if ($data->FinishDate == null) {
                        $finishDate = "-";
                    } else {
                        $finishDate = date('d-M-Y H:i', strtotime($data->FinishDate));
                    }
                    return $finishDate;
                })
                ->editColumn('DeliveryDate', function ($data) {
                    $date = date('d-M-Y H:i', strtotime($data->DeliveryDate));
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
                ->addColumn('DueDate', function ($data) {
                    if ($data->FinishDate == null) {
                        $dueDate = "H+5 setelah barang diterima";
                    } else {
                        $dueDate = date("d-M-Y", strtotime("$data->FinishDate +5 day"));
                    }
                    return $dueDate;
                })
                ->addColumn('RemainingDay', function ($data) {
                    $dueDate = strtotime("$data->FinishDate +5 day");
                    $timeDiff = time() - $dueDate;
                    $dateDiff = round($timeDiff / (60 * 60 * 24));

                    if ($dateDiff == 0) {
                        $remainingDay = "<a class='badge badge-danger'>H " . $dateDiff . " (Hari H)</a>";
                    } elseif (Str::contains($dateDiff, '-')) {
                        if ($dateDiff == -1 || $dateDiff == -2) {
                            $remainingDay = "<a class='badge badge-warning'>H" . $dateDiff . "</a>";
                        } else {
                            $remainingDay = "H" . $dateDiff;
                        }
                    } else {
                        $remainingDay = "<a class='badge badge-danger'>H+" . $dateDiff . "</a>";
                    }

                    if ($data->FinishDate != null && $data->IsPaid == 0) {
                        $remainingDay = $remainingDay;
                    } else {
                        $remainingDay = "-";
                    }

                    return $remainingDay;
                })
                ->addColumn('BillNominal', function ($data) {
                    // DENDA
                    $dueDate = strtotime("$data->FinishDate +5 day");
                    if ($data->IsPaid == 0) {
                        $timeDiff = time() - $dueDate;
                    } else {
                        $timeDiff = strtotime($data->PaymentDate) - $dueDate;
                    }
                    $lateDays = round($timeDiff / (60 * 60 * 24));

                    $grandTotal = $data->SubTotal + $data->ServiceCharge + $data->DeliveryFee - $data->Discount;

                    if ($lateDays > 0) {
                        $sqlLateBillFee = DB::table('tx_merchant_delivery_order_bill')
                            ->where('PaymentMethodID', $data->PaymentMethodID)
                            ->whereRaw("$lateDays BETWEEN OverdueStartDay AND OverdueToDay")
                            ->select('TypeFee', 'NominalFee')
                            ->first();

                        if ($sqlLateBillFee->TypeFee == "PERCENT") {
                            $lateFee = $grandTotal * $sqlLateBillFee->NominalFee / 100;
                            $grandTotal += round($lateFee);
                        }

                        if ($sqlLateBillFee->TypeFee == 'NOMINAL') {
                            $lateFee = $sqlLateBillFee->NominalFee;
                            $grandTotal += $lateFee;
                        }
                    } else {
                        $lateFee = 0;
                    }

                    if ($data->StatusDO == "S024" || $data->StatusDO == "S025") {
                        $bill = $grandTotal;
                    } else {
                        $bill = "-";
                    }
                    return $bill;
                })
                ->editColumn('IsPaid', function ($data) {
                    if ($data->IsPaid == 1) {
                        $isPaid = '<span class="badge badge-success">Sudah Lunas</span>';
                    } else {
                        $isPaid = '<span class="badge badge-danger">Belum Lunas</span>';
                    }
                    return $isPaid;
                })
                ->editColumn('PaymentSlip', function ($data) {
                    $baseImageUrl = config('app.base_image_url');
                    if ($data->PaymentSlip != null) {
                        $paymentSlip = '<a data-store-name="' . $data->StoreName . '" data-do-id="' . $data->DeliveryOrderID . '" class="lihat-bukti" target="_blank" href="' . $baseImageUrl . 'paylater_slip_payment/' . $data->PaymentSlip . '">Lihat Bukti</a>';
                    } else {
                        $paymentSlip = "-";
                    }

                    return $paymentSlip;
                })
                ->addColumn('Action', function ($data) {
                    if ($data->FinishDate != null && $data->IsPaid == 0 && Auth::user()->RoleID != "AD" && Auth::user()->RoleID != "BM") {
                        $action = '<a class="btn btn-xs btn-warning btn-payment my-1" data-do-id="' . $data->DeliveryOrderID . '" data-store-name="' . $data->StoreName . '">Update Pelunasan</a>';
                    } else {
                        $action = '';
                    }
                    $invoice = '<a class="btn btn-xs btn-info my-1 mr-1" target="_blank" href="/restock/deliveryOrder/invoice/' . $data->DeliveryOrderID . '">Delivery Invoice</a>';

                    return $invoice . $action;
                })
                ->filterColumn('Sales', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(ms_merchant_account.ReferralCode, ' ', ms_sales.SalesName) like ?", ["%$keyword%"]);
                })
                ->rawColumns(['StockOrderID', 'IsPaid', 'RemainingDay', 'PaymentSlip', 'Action'])
                ->make(true);
        }
    }

    public function updateBillPayLater($deliveryOrderID, Request $request)
    {
        $request->validate([
            'payment_date' => 'required',
            'nominal' => 'required',
            'payment_slip' => 'required'
        ]);


        $imageName = date('YmdHis') . '_' . $deliveryOrderID . '.' . $request->file('payment_slip')->extension();
        $request->file('payment_slip')->move($this->saveImageUrl . 'paylater_slip_payment/', $imageName);

        $data = [
            'IsPaid' => 1,
            'PaymentDate' => $request->input('payment_date'),
            'PaymentNominal' => $request->input('nominal'),
            'PaymentSlip' => $imageName
        ];

        $update = DB::table('tx_merchant_delivery_order')
            ->where('DeliveryOrderID', $deliveryOrderID)
            ->update($data);

        if ($update) {
            return redirect()->route('distribution.billPayLater')->with('success', 'Data pelunasan PayLater berhasil disimpan');
        } else {
            return redirect()->route('distribution.billPayLater')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function product()
    {
        return view('distribution.product.index');
    }

    public function getProduct(Request $request)
    {
        $distributorId = $request->input('distributorId');

        $distributorProducts = DB::table('ms_distributor_product_price')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_distributor_product_price.DistributorID')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'ms_distributor_product_price.ProductID')
            ->join('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_product_price.GradeID')
            ->join('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
            ->join('ms_product_type', 'ms_product_type.ProductTypeID', '=', 'ms_product.ProductTypeID')
            ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
            ->select('ms_distributor_product_price.DistributorID', 'ms_distributor.DistributorName', 'ms_distributor_product_price.ProductID', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_product_category.ProductCategoryName', 'ms_product_type.ProductTypeName', 'ms_product_uom.ProductUOMName', 'ms_product.ProductUOMDesc', 'ms_distributor_product_price.Price', 'ms_distributor_product_price.GradeID', 'ms_distributor_grade.Grade', 'ms_distributor_product_price.IsPreOrder');

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $distributorProducts->where('ms_distributor.Depo', '=', $depoUser);
        }

        if ($distributorId != null) {
            $distributorProducts->where('ms_distributor.DistributorID', '=', $distributorId);
        }

        $data = $distributorProducts;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('ProductImage', function ($data) {
                    if ($data->ProductImage == null) {
                        $data->ProductImage = 'not-found.png';
                    }
                    return '<img src="' . $this->baseImageUrl . 'product/' . $data->ProductImage . '" alt="Product Image" height="90">';
                })
                ->editColumn('Grade', function ($data) {
                    if ($data->Grade == "Retail") {
                        $grade = '<span class="badge badge-success">' . $data->Grade . '</span>';
                    } elseif ($data->Grade == "SO") {
                        $grade = '<span class="badge badge-warning">' . $data->Grade . '</span>';
                    } elseif ($data->Grade == "WS") {
                        $grade = '<span class="badge badge-primary">' . $data->Grade . '</span>';
                    } else {
                        $grade = $data->Grade;
                    }
                    return $grade;
                })
                ->editColumn('IsPreOrder', function ($data) {
                    if ($data->IsPreOrder == 1) {
                        $preOrder = "Ya";
                    } else {
                        $preOrder = "Tidak";
                    }
                    return $preOrder;
                })
                ->addColumn('Action', function ($data) {
                    if (Auth::user()->RoleID != "AD") {
                        $actionBtn = '<a href="#" data-distributor-id="' . $data->DistributorID . '" data-product-id="' . $data->ProductID . '" data-grade-id="' . $data->GradeID . '" data-product-name="' . $data->ProductName . '" data-grade-name="' . $data->Grade . '" data-price="' . $data->Price . '" data-pre-order="' . $data->IsPreOrder . '" class="btn-edit btn btn-sm btn-warning mr-1">Edit</a>
                        <a data-distributor-id="' . $data->DistributorID . '" data-product-id="' . $data->ProductID . '" data-grade-id="' . $data->GradeID . '" data-product-name="' . $data->ProductName . '" data-grade-name="' . $data->Grade . '" href="#" class="btn-delete btn btn-sm btn-danger">Delete</a>';
                    } else {
                        $actionBtn = '';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['Grade', 'ProductImage', 'IsPreOrder', 'Action'])
                ->make(true);
        }
    }

    public function addProduct()
    {
        $getDistributorSql = DB::table('ms_distributor')
            ->where('IsActive', '=', 1)
            ->where('Email', '!=', NULL)
            ->select('DistributorID', 'DistributorName', 'Email', 'Address', 'CreatedDate');

        if (Auth::user()->Depo != "ALL") {
            $getDistributorSql->where('Depo', '=', Auth::user()->Depo);
        }

        $getDistributor = $getDistributorSql->get();

        return view('distribution.product.new', [
            'distributor' => $getDistributor
        ]);

        // if (Auth::user()->RoleID == "AD") {
        //     $distributorName = DB::table('ms_distributor')
        //         ->where('Depo', '=', Auth::user()->Depo)
        //         ->select('DistributorID', 'DistributorName')
        //         ->first();

        //     $productNotInDistributor = DB::table('ms_product')
        //         ->leftJoin('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
        //         ->leftJoin('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
        //         ->leftJoin('ms_distributor_product_price', 'ms_distributor_product_price.ProductID', '=', 'ms_product.ProductID')
        //         ->whereNotIn('ms_product.ProductID', function ($query) use ($distributorName) {
        //             $query->select('ms_distributor_product_price.ProductID')->from('ms_distributor_product_price')->where('ms_distributor_product_price.DistributorID', '=', $distributorName->DistributorID);
        //         })
        //         ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_category.ProductCategoryName', 'ms_product_uom.ProductUOMName')
        //         ->distinct()
        //         ->get();

        //     $gradeDistributor = DB::table('ms_distributor_grade')
        //         ->where('ms_distributor_grade.DistributorID', '=', $distributorName->DistributorID)
        //         ->select('ms_distributor_grade.GradeID', 'ms_distributor_grade.Grade')
        //         ->get();

        //     return view('distribution.product.new', [
        //         'distributor' => $getDistributor,
        //         'depo' => $distributorName,
        //         'productNotInDistributor' => $productNotInDistributor,
        //         'gradeDistributor' => $gradeDistributor
        //     ]);
        // } else {
        // return view('distribution.product.new', [
        //     'distributor' => $getDistributor
        // ]);
        // }
    }

    public function ajaxGetProduct($distributorId)
    {
        $getProduct = DB::table('ms_product')
            ->leftJoin('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
            ->leftJoin('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
            ->leftJoin('ms_distributor_product_price', 'ms_distributor_product_price.ProductID', '=', 'ms_product.ProductID')
            ->whereNotIn('ms_product.ProductID', function ($query) use ($distributorId) {
                $query->select('ms_distributor_product_price.ProductID')->from('ms_distributor_product_price')->where('ms_distributor_product_price.DistributorID', '=', $distributorId);
            })
            ->select('ms_product.ProductID', 'ms_product.ProductName', 'ms_product.ProductUOMDesc', 'ms_product_category.ProductCategoryName', 'ms_product_uom.ProductUOMName')
            ->distinct()
            ->get();

        return response()->json($getProduct);
    }

    public function insertProduct(Request $request)
    {
        $request->validate([
            'distributor' => 'exists:ms_distributor,DistributorID',
            'product' => 'required|exists:ms_product,ProductID',
            'grade_price' => 'required',
            'grade_price.*' => 'numeric'
        ]);

        $gradeID = $request->input('grade_id');
        $gradePrice = $request->input('grade_price');
        $data = array_map(function () {
            return func_get_args();
        }, $gradeID, $gradePrice);
        foreach ($data as $key => $value) {
            $data[$key][] = $request->input('distributor');
            $data[$key][] = $request->input('product');
        }

        try {
            DB::transaction(function () use ($data) {
                foreach ($data as &$value) {
                    $value = array_combine(['GradeID', 'Price', 'DistributorID', 'ProductID'], $value);
                    DB::table('ms_distributor_product_price')
                        ->insert([
                            'DistributorID' => $value['DistributorID'],
                            'ProductID' => $value['ProductID'],
                            'GradeID' => $value['GradeID'],
                            'Price' => $value['Price']
                        ]);
                    $getProduct = DB::table('ms_product')->where('ProductID', $value['ProductID'])->select('ProductName')->first();
                    DB::table('ms_product_price_log')
                        ->insert([
                            'LogType' => 'DISTRIBUTOR PRODUCT',
                            'LogAction' => 'INSERT PRODUCT',
                            'OldPrice' => 0,
                            'NewPrice' => $value['Price'],
                            'DistributorID' => $value['DistributorID'],
                            'GradeID' => $value['GradeID'],
                            'ProductID' => $value['ProductID'],
                            'ProductName' => $getProduct->ProductName,
                            'ActionByID' => Auth::user()->UserID,
                            'ActionByName' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
                            'CreatedDate' => date('Y-m-d H:i:s')
                        ]);
                }
            });

            return redirect()->route('distribution.product')->with('success', 'Data Produk berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.product')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function updateProduct(Request $request, $distributorId, $productId, $gradeId)
    {
        $request->validate([
            'price' => 'required|integer',
            'is_pre_order' => 'required|in:1,0'
        ]);

        $getProduct = DB::table('ms_product')->where('ProductID', $productId)->select('ProductName')->first();
        $getOldPrice = DB::table('ms_distributor_product_price')
            ->where('DistributorID', $distributorId)
            ->where('ProductID', $productId)
            ->where('GradeID', $gradeId)
            ->select('Price')->first();

        $data = [
            'LogType' => 'DISTRIBUTOR PRODUCT',
            'LogAction' => 'UPDATE',
            'OldPrice' => $getOldPrice->Price,
            'NewPrice' => $request->input('price'),
            'DistributorID' => $distributorId,
            'GradeID' => $gradeId,
            'ProductID' => $productId,
            'ProductName' => $getProduct->ProductName,
            'ActionByID' => Auth::user()->UserID,
            'ActionByName' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'CreatedDate' => date('Y-m-d H:i:s')
        ];

        try {
            DB::transaction(function () use ($distributorId, $productId, $gradeId, $request, $data) {
                DB::table('ms_distributor_product_price')
                    ->where('DistributorID', '=', $distributorId)
                    ->where('ProductID', '=', $productId)
                    ->where('GradeID', '=', $gradeId)
                    ->update([
                        'Price' => $request->input('price'),
                        'IsPreOrder' => $request->input('is_pre_order')
                    ]);
                DB::table('ms_product_price_log')->insert($data);
            });
            return redirect()->route('distribution.product')->with('success', 'Harga produk telah berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.product')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function deleteProduct($distributorId, $productId, $gradeId)
    {
        $getProduct = DB::table('ms_product')->where('ProductID', $productId)->select('ProductName')->first();
        $getOldPrice = DB::table('ms_distributor_product_price')
            ->where('DistributorID', $distributorId)
            ->where('ProductID', $productId)
            ->where('GradeID', $gradeId)
            ->select('Price')->first();

        $data = [
            'LogType' => 'DISTRIBUTOR PRODUCT',
            'LogAction' => 'REMOVE PRODUCT',
            'OldPrice' => $getOldPrice->Price,
            'NewPrice' => 0,
            'DistributorID' => $distributorId,
            'GradeID' => $gradeId,
            'ProductID' => $productId,
            'ProductName' => $getProduct->ProductName,
            'ActionByID' => Auth::user()->UserID,
            'ActionByName' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo,
            'CreatedDate' => date('Y-m-d H:i:s')
        ];

        try {
            DB::transaction(function () use ($distributorId, $productId, $gradeId, $data) {
                DB::table('ms_distributor_product_price')
                    ->where('DistributorID', '=', $distributorId)
                    ->where('ProductID', '=', $productId)
                    ->where('GradeID', '=', $gradeId)
                    ->delete();
                DB::table('ms_product_price_log')->insert($data);
            });
            return redirect()->route('distribution.product')->with('success', 'Data produk berhasil dihapus');
        } catch (\Throwable $th) {
            return redirect()->route('distribution.product')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function merchant()
    {
        return view('distribution.merchant.index');
    }

    public function getMerchant(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $distributorId = $request->input('distributorId');

        // Get data account, jika tanggal filter kosong tampilkan semua data.
        $sqlAllAccount = DB::table('ms_merchant_account')
            ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'ms_merchant_account.ReferralCode')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', '=', 'ms_merchant_account.MerchantID')
            ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_merchant_grade.GradeID')
            ->where('ms_merchant_account.IsTesting', 0)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.DistributorID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.CreatedDate', 'ms_merchant_account.StoreAddress', 'ms_merchant_account.ReferralCode', 'ms_distributor.DistributorName', 'ms_distributor_grade.Grade', 'ms_distributor_merchant_grade.GradeID', 'ms_merchant_account.Partner', 'ms_sales.SalesName');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('ms_merchant_account.CreatedDate', '>=', $fromDate)
                ->whereDate('ms_merchant_account.CreatedDate', '<=', $toDate);
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlAllAccount->where('ms_distributor.Depo', '=', $depoUser);
        }

        if ($distributorId != null) {
            $sqlAllAccount->where('ms_merchant_account.DistributorID', '=', $distributorId);
        }

        // Get data response
        $data = $sqlAllAccount;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('Grade', function ($data) {
                    if ($data->Grade == null) {
                        $grade = "Retail";
                    } else {
                        $grade = $data->Grade;
                    }
                    return $grade;
                })
                ->addColumn('Action', function ($data) {
                    if (Auth::user()->RoleID != "AD") {
                        $ubahGrade = '<a href="#" data-distributor-id="' . $data->DistributorID . '" data-merchant-id="' . $data->MerchantID . '" 
                            data-store-name="' . $data->StoreName . '" data-owner-name="' . $data->OwnerFullName . '" data-grade-id="' . $data->GradeID . '" 
                            class="btn btn-xs btn-warning edit-grade mb-1">Ubah Grade</a>';
                    } else {
                        $ubahGrade = '';
                    }

                    $actionBtn = '<a href="/distribution/merchant/specialprice/' . $data->MerchantID . '" class="btn btn-xs btn-secondary mb-1">Special Price</a>';

                    return $ubahGrade . $actionBtn;
                })
                ->addColumn('SpecialPrice', function ($data) {
                    $specialPriceBtn = '<a href="/distribution/merchant/specialprice/' . $data->MerchantID . '" class="btn btn-sm btn-secondary">Special Price</a>';
                    return $specialPriceBtn;
                })
                ->filterColumn('ms_merchant_account.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(ms_merchant_account.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->rawColumns(['Action', 'SpecialPrice'])
                ->make(true);
        }
    }

    public function updateGrade(Request $request, $merchantId)
    {
        $merchantGrade = DB::table('ms_distributor_merchant_grade')
            ->where('MerchantID', '=', $merchantId)
            ->select('MerchantID')->first();

        $request->validate([
            'grade' => 'required|exists:ms_distributor_grade,GradeID'
        ]);

        $dataGrade = [
            'DistributorID' => $request->input('distributor'),
            'MerchantID' => $merchantId,
            'GradeID' => $request->input('grade')
        ];

        if ($merchantGrade) {
            $updateGrade = DB::table('ms_distributor_merchant_grade')
                ->where('MerchantID', '=', $merchantId)
                ->update($dataGrade);
        } else {
            $updateGrade =  DB::table('ms_distributor_merchant_grade')
                ->insert($dataGrade);
        }

        if ($updateGrade) {
            return redirect()->route('distribution.merchant')->with('success', 'Data grade merchant berhasil diubah');
        } else {
            return redirect()->route('distribution.merchant')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function specialPrice(MerchantService $merchantService, $merchantID)
    {
        return view('distribution.merchant.specialprice', [
            'merchant' => $merchantService->merchantAccount($merchantID)->first(),
            'grade' => $merchantService->merchantSpecialPrice($merchantID)->first()
        ]);
    }

    public function getSpecialPrice($merchantID, MerchantService $merchantService, Request $request)
    {
        $data = $merchantService->merchantSpecialPrice($merchantID);

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('SpecialPrice', function ($data) {
                    if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "BM" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "RBTAD") {
                        $specialPrice = '<input type="text" value="' . $data->SpecialPrice . '" class="special-price" autocomplete="off">';
                    } else {
                        if ($data->SpecialPrice == null) {
                            $specialPrice = '';
                        } else {
                            $specialPrice = number_format($data->SpecialPrice, 0, '', '.');
                        }
                    }
                    return $specialPrice;
                })
                ->addColumn('Action', function ($data) use ($merchantID) {
                    if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "BM" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "RBTAD") {
                        if ($data->SpecialPrice != null) {
                            $btn = '<button class="btn btn-xs btn-success btn-simpan" data-product-id="' . $data->ProductID . '" 
                                    data-merchant-id="' . $merchantID . '" data-grade-id="' . $data->GradeID . '">Simpan</button>
                                <button class="btn btn-xs btn-danger btn-hapus ml-1" data-product-id="' . $data->ProductID . '" 
                                    data-merchant-id="' . $merchantID . '" data-grade-id="' . $data->GradeID . '">Hapus</button>';
                        } else {
                            $btn = '<button class="btn btn-xs btn-success btn-simpan" data-product-id="' . $data->ProductID . '" data-merchant-id="' . $merchantID . '" data-grade-id="' . $data->GradeID . '">Simpan</button>';
                        }
                    } else {
                        $btn = '';
                    }
                    return $btn;
                })
                ->rawColumns(['SpecialPrice', 'Action'])
                ->make(true);
        }
    }

    public function insertOrUpdateSpecialPrice(Request $request, MerchantService $merchantService)
    {
        $merchantID = $request->merchantID;
        $productID = $request->productID;
        $gradeID = $request->gradeID;
        $specialPrice = $request->specialPrice;

        if ($specialPrice != null) {
            try {
                $merchantService->updateOrInsertSpecialPrice($merchantID, $productID, $gradeID, $specialPrice);
                $status = "success";
                $message = "Special Price Merchant berhasil disimpan";
            } catch (\Throwable $th) {
                $status = "failed";
                $message = "Terjadi kesalahan";
            }
        } else {
            $status = "failed";
            $message = "Terjadi kesalahan, pastikan input data dengan benar";
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteSpecialPrice(Request $request, MerchantService $merchantService)
    {
        $merchantID = $request->merchantID;
        $productID = $request->productID;
        $gradeID = $request->gradeID;

        try {
            $merchantService->deleteSpecialPriceMerchant($merchantID, $productID, $gradeID);
            $status = "success";
            $message = "Special Price Merchant berhasil dihapus";
        } catch (\Throwable $th) {
            $status = "failed";
            $message = "Terjadi kesalahan sistem atau jaringan";
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function resetSpecialPrice(Request $request, MerchantService $merchantService)
    {
        $merchantID = $request->merchantID;
        $gradeID = $request->gradeID;

        try {
            $merchantService->resetSpecialPriceMerchant($merchantID, $gradeID);
            $status = "success";
            $message = "Special Price Merchant berhasil direset";
        } catch (\Throwable $th) {
            $status = "failed";
            $message = "Terjadi kesalahan";
        }

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }
}
