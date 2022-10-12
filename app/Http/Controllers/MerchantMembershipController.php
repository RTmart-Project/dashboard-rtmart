<?php

namespace App\Http\Controllers;

use App\Services\MerchantMembershipService;
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
        $statusMembership = DB::table('ms_status_couple_preneur')->where('StatusCouplePreneurID', '!=', 0)->get();

        return view('merchant.membership.index', [
            'statusMembership' => $statusMembership
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
            // $stringStatus = "'" . implode("', '", $filterStatus) . "'";
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
                ->editColumn('MembershipCoupleSubmitDate', function ($data) {
                    return date('d-M-Y H:i:s', strtotime($data->MembershipCoupleSubmitDate));
                })
                ->editColumn('MembershipCoupleConfirmDate', function ($data) {
                    if ($data->MembershipCoupleConfirmDate !== null) {
                        $date = date('d-M-Y H:i:s', strtotime($data->MembershipCoupleConfirmDate));
                    } else {
                        $date = "-";
                    }
                    return $date;
                })
                ->addColumn('Photo', function ($data) {
                    return "<button data-merchant-id='$data->MerchantID' data-store='$data->StoreName'
                                id='survey-photo' type='button' class='btn btn-sm btn-info btn-photo'>
                                Lihat
                            </button>";
                })
                ->filterColumn('Sales', function ($query, $keyword) {
                    $sql = "CONCAT(ms_merchant_account.ReferralCode,' - ', ms_sales.SalesName)  like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['StatusName', 'Photo'])
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
            dd($th->getMessage());
            return redirect()->route('merchant.membership')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }
}