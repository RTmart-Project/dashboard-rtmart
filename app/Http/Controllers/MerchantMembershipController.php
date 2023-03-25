<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Services\MerchantMembershipService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MerchantMembershipController extends Controller
{
    protected $merchantMembershipService;

    public function __construct(MerchantMembershipService $merchantMembershipService)
    {
        $this->merchantMembershipService = $merchantMembershipService;
    }

    public function index()
    {
        $statusMembership = DB::table('ms_status_couple_preneur')
            ->where('StatusCouplePreneurID', '!=', 0)
            ->where('StatusNote', 'MEMBERSHIP')
            ->get();

        return view('merchant.membership.index', [
            'statusMembership' => $statusMembership,
        ]);
    }

    public function data(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $filterStatus = $request->input('filterStatus');

        $sqlMembership = $this->merchantMembershipService->merchantMembershipData();

        if ($startDate != '' && $endDate != '') {
            $sqlMembership->whereDate('ms_merchant_account.MembershipCoupleSubmitDate', '>=', $startDate)
                ->whereDate('ms_merchant_account.MembershipCoupleSubmitDate', '<=', $endDate);
        }

        if ($filterStatus) {
            $sqlMembership->whereIn('ms_merchant_account.ValidationStatusMembershipCouple', $filterStatus);
        }

        $data = $sqlMembership;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('Sales', function ($data) {
                    return $data->ReferralCode . ' - ' . $data->SalesName;
                })
                ->editColumn('StatusName', function ($data) {
                    if ($data->ValidationStatusMembershipCouple === 2) {
                        $badge = '<span class="badge badge-danger">' . $data->StatusName . '</span>';
                    } elseif ($data->ValidationStatusMembershipCouple === 3) {
                        $badge = '<span class="badge badge-success">' . $data->StatusName . '</span>';
                    } else {
                        $badge = '<span class="badge badge-warning">' . $data->StatusName . '</span>';
                    }
                    return $badge;
                })
                ->editColumn('StatusNameCrowdo', function ($data) {
                    if ($data->StatusCrowdo == 5) {
                        $badge = '<span class="badge badge-warning">' . $data->StatusNameCrowdo . '</span>';
                    } elseif ($data->StatusCrowdo == 6) {
                        $badge = '<span class="badge badge-success">' . $data->StatusNameCrowdo . '</span>';
                    } elseif ($data->StatusCrowdo == 7) {
                        $badge = '<span class="badge badge-danger">' . $data->StatusNameCrowdo . '</span>';
                    } else {
                        $badge = '<span class="badge badge-info">' . $data->StatusNameCrowdo . '</span>';
                    }
                    return $badge;
                })
                ->editColumn('MembershipCoupleSubmitDate', function ($data) {
                    return date('d-M-Y H:i:s', strtotime($data->MembershipCoupleSubmitDate));
                })
                ->editColumn('BirthDate', function ($data) {
                    if ($data->BirthDate !== null) {
                        $date = date('d-M-Y', strtotime($data->BirthDate));
                    } else {
                        $date = "";
                    }
                    return $date;
                })
                ->editColumn('BirthDateCouple', function ($data) {
                    if ($data->BirthDateCouple !== null) {
                        $date = date('d-M-Y', strtotime($data->BirthDateCouple));
                    } else {
                        $date = "";
                    }
                    return $date;
                })
                ->addColumn('StoreSize', function ($data) {
                    if ($data->StoreLength != null && $data->StoreWidth != null) {
                        $storeSize = $data->StoreLength . "x" . $data->StoreWidth . "m";
                    } else {
                        $storeSize = "";
                    }
                    return $storeSize;
                })
                ->editColumn('MembershipCoupleConfirmDate', function ($data) {
                    if ($data->MembershipCoupleConfirmDate !== null) {
                        $date = date('d-M-Y H:i:s', strtotime($data->MembershipCoupleConfirmDate));
                    } else {
                        $date = "-";
                    }
                    return $date;
                })
                ->editColumn('CrowdoApprovedDate', function ($data) {
                    if ($data->CrowdoApprovedDate !== null) {
                        $date = date('d-M-Y', strtotime($data->CrowdoApprovedDate));
                    } else {
                        $date = "-";
                    }
                    return $date;
                })
                ->addColumn('Photo', function ($data) {
                    return "<button data-merchant-id='$data->MerchantID' data-store='$data->StoreName'
                                id='survey-photo' type='button' class='btn btn-xs btn-info btn-photo'>
                                Lihat
                            </button>";
                })
                ->addColumn('Action', function ($data) {
                    return "<button class='btn btn-xs btn-warning btn-update-crowdo' 
                                data-merchant-id='$data->MerchantID' data-store='$data->StoreName' data-status-crowdo='$data->StatusCrowdo'>
                                Update Status Crowdo
                            </button>";
                })
                ->addColumn('Disclaimer', function ($data) {
                    if ($data->Disclaimer == 1) {
                        $disclaimer = "<a href='/merchant/membership/disclaimer/$data->MerchantID' target='_blank' class='btn btn-sm btn-info'>Lihat</a>";
                    } else {
                        $disclaimer = '';
                    }
                    return $disclaimer;
                })
                ->filterColumn('Sales', function ($query, $keyword) {
                    $sql = "CONCAT(ms_merchant_account.ReferralCode,' - ', ms_sales.SalesName)  like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['StatusNameCrowdo', 'StatusName', 'Photo', 'Action', 'Disclaimer'])
                ->make();
        }
    }

    public function photo($merchantID)
    {
        $data = $this->merchantMembershipService->merchantMembershipPhoto($merchantID);

        return $data;
    }

    public function confirm($merchantID, $status, Request $request)
    {
        $note = $request->input('note');
        $user = Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo;
        $dateNow = date('Y-m-d H:i:s');
        $data = $this->merchantMembershipService->merchantMembershipPhoto($merchantID);

        if ($status === "approve") {
            $statusID = 3;
            $dataMerchantAccount = [
                'ValidationStatusMembershipCouple' => $statusID,
                'MembershipCoupleConfirmDate' => $dateNow,
                'MembershipCoupleConfirmBy' => $user
            ];
        } else {
            $statusID = 2;
            $dataMerchantAccount = [
                'ValidationStatusMembershipCouple' => $statusID,
                'MembershipCoupleConfirmDate' => $dateNow,
                'MembershipCoupleConfirmBy' => $user,
                'ValidationNoteMembershipCouple' => $note
            ];
        }

        $dataMerchantCouplePreneurLog = [
            'MerchantID' => $merchantID,
            'PhotoIDCard' => $data->PhotoIDCard,
            'NumberIDCard' => $data->NumberIDCard,
            'UsernameIDCard' => $data->UsernameIDCard,
            'AsIDCard' => $data->AsIDCard,
            'PhotoIDCardCouple' => $data->PhotoIDCardCouple,
            'NumberIDCardCouple' => $data->NumberIDCardCouple,
            'UsernameIDCardCouple' => $data->UsernameIDCardCouple,
            'AsIDCardCouple' => $data->AsIDCardCouple,
            'StorePhotoMembership' => $data->StorePhotoMembership,
            'StatusMembershipCouple' => $statusID,
            'NoteMembershipCouple' => $note,
            'CreatedDate' => $dateNow,
            'ActionBy' => $user
        ];

        try {
            $this->merchantMembershipService->merchantMembershipConfirm($merchantID, $status, $dataMerchantAccount, $dataMerchantCouplePreneurLog);

            return redirect()->route('merchant.membership')->with('success', 'Data membership merchant berhasil dikonfirmasi');
        } catch (\Throwable $th) {
            return redirect()->route('merchant.membership')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function updateCrowdo($merchantID, Request $request)
    {
        $status = $request->input('status-crowdo');
        $loanID = $request->input('loan_id');
        $amount = $request->input('amount');
        $batch = $request->input('batch');
        $approvedDate = $request->input('approved_date');

        $dataCrowdo = [
            'CrowdoLoanID' => $loanID,
            'CrowdoAmount' => $amount,
            'CrowdoBatch' => $batch,
            'CrowdoApprovedDate' => $approvedDate
        ];

        $dataCouplePreneurCrowdoLog = [
            'MerchantID' => $merchantID,
            'StatusCrowdo' => $status,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'ActionBy' => Auth::user()->Name . ' ' . Auth::user()->RoleID . ' ' . Auth::user()->Depo
        ];

        try {
            $this->merchantMembershipService->updateStatusCrowdo($merchantID, $status, $dataCrowdo, $dataCouplePreneurCrowdoLog);
            return redirect()->route('merchant.membership')->with('success', 'Status Crowdo Merchant berhasil di-update');
        } catch (\Throwable $th) {
            return redirect()->route('merchant.membership')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function disclaimer($merchantID)
    {
        $dayName = Carbon::now()->locale('id')->translatedFormat('l');
        $date = Carbon::now()->locale('id')->translatedFormat('d F Y');

        // $merchant = DB::table('ms_merchant_account AS mma')
        //     ->join('ms_merchant_couple_preneur_log AS mmcpl', 'mma.MerchantID', 'mmcpl.MerchantID')
        //     ->select(DB::raw("MAX(mmcpl.ID) AS SeriesNumber"), 'mma.MerchantID', 'mma.UsernameIDCard', 'mma.NumberIDCard', 'mma.StoreAddress', 'mma.MembershipCoupleSubmitDate')
        //     ->where('mma.MerchantID', $merchantID)
        //     ->groupBy('mma.MerchantID')
        //     ->first();

        // $merchant = DB::table('ms_merchant_account AS mma')
        //     ->join('ms_history_disclaimer AS mhd', 'mma.MerchantID', 'mhd.merchant_id')
        //     ->selectRaw("ANY_VALUE(mhd.disclaimer_id) AS disclaimer_id, COUNT(mhd.disclaimer_id) as total, 
        //     ANY_VALUE(mma.MerchantID) AS MerchantID, ANY_VALUE(mma.UsernameIDCard) AS UsernameIDCard, 
        //     ANY_VALUE(mma.NumberIDCard) AS NumberIDCard, ANY_VALUE(mma.StoreAddress) AS StoreAddress")
        //     ->where('mma.MerchantID', '=', $merchantID)
        //     ->orderByDesc('mhd.disclaimer_id')
        //     ->first();

        $merchant = DB::table('ms_merchant_account AS mma')
            ->join('ms_history_disclaimer AS mhd', 'mma.MerchantID', 'mhd.merchant_id')
            ->selectRaw("MAX(mhd.disclaimer_id) AS disclaimer_id, COUNT(*) as TotalSubmit, 
                ANY_VALUE(mma.MerchantID) AS MerchantID, ANY_VALUE(mma.UsernameIDCard) AS UsernameIDCard, 
                ANY_VALUE(mma.NumberIDCard) AS NumberIDCard, ANY_VALUE(mma.StoreAddress) AS StoreAddress,
                ANY_VALUE(mhd.nominal) AS Nominal")
            ->where('mma.MerchantID', '=', $merchantID)
            ->groupBy('mma.MerchantID')
            ->first();

        $merchant->Penyebut = Helper::convertToWords($merchant->Nominal);
        $merchant->Nominal = Helper::formatCurrency($merchant->Nominal, '');

        $orderSeriesNumber = substr("0000" . $merchant->disclaimer_id, strlen($merchant->disclaimer_id));
        $orderTotalSubmit = substr("000" . $merchant->TotalSubmit, strlen($merchant->TotalSubmit));
        $merchant->SeriesNumber = date('y') . ".$orderSeriesNumber.$orderTotalSubmit/B-KRTM/III";

        return view('merchant.membership.disclaimer', ['merchant' => $merchant, 'dayName' => $dayName, 'date' => $date]);
    }
}
