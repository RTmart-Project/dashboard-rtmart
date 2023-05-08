<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Services\MerchantMembershipService;

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
                ->addColumn('ActionDate', function ($data) {
                    return $data->action_date;
                })
                ->addColumn('Photo', function ($data) {
                    return "
                        <button data-merchant-id='$data->MerchantID' data-store='$data->StoreName' id='survey-photo' type='button' class='btn btn-xs btn-info btn-photo'>
                            Lihat
                        </button>
                    ";
                })
                ->addColumn('Action', function ($data) {
                    if (($data->status_payment_id == null || $data->status_payment_id == 3) && strpos($data->rejected_reason, "BI Checking") === false) {
                        return "<button class='btn btn-xs btn-warning btn-update-crowdo' data-merchant-id='$data->MerchantID' data-store='$data->StoreName' data-status-crowdo='$data->StatusCrowdo'>
                                    Update
                                </button>";
                    }
                })
                ->filterColumn('MerchantID', function ($query, $keyword) {
                    $sql = "ms_merchant_account.MerchantID like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['StatusNameCrowdo', 'StatusName', 'Photo', 'Action', 'Disclaimer', 'ActionDate'])
                ->make();
        }
    }

    public function partnerView()
    {
        $statusMembership = DB::table('ms_status_couple_preneur')
            ->where('StatusCouplePreneurID', '!=', 0)
            ->where('StatusNote', 'MEMBERSHIP')
            ->get();

        return view('merchant.membership.partner', [
            'statusMembership' => $statusMembership,
        ]);
    }

    public function partnerData(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $filterStatus = $request->input('filterStatus');

        $sqlMembership = $this->merchantMembershipService->merchantMembershipDataPartner();

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
                ->editColumn('StatusPaymentName', function ($data) {
                    $badge = "";
                    if ($data->status_payment_id == 1) {
                        $badge = '<span class="badge badge-info">' . $data->StatusPaymentName . '</span>';
                    } elseif ($data->status_payment_id == 2) {
                        $badge = '<span class="badge badge-danger">' . $data->StatusPaymentName . '</span>';
                    } elseif ($data->status_payment_id == 3) {
                        $badge = '<span class="badge badge-success">' . $data->StatusPaymentName . '</span>';
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
                    return "
                        <button data-merchant-id='$data->MerchantID' data-store='$data->StoreName' id='survey-photo' type='button' class='btn btn-xs btn-info btn-photo'>
                            Lihat
                        </button>
                    ";
                })
                ->addColumn('Action', function ($data) {
                    if ($data->StatusCouplePreneurID == 3) {
                        return "
                            <button class='btn btn-xs btn-warning btn-update-crowdo' data-membership-id='$data->disclaimer_id' data-merchant-id='$data->MerchantID' data-status-crowdo='$data->StatusCrowdo' data-status-payment-id='$data->status_payment_id'>
                                Update
                            </button>
                        ";
                    }
                })
                ->addColumn('Disclaimer', function ($data) {
                    if ($data->Disclaimer == 1) {
                        $disclaimer = "<a href='/merchant/membership/disclaimer/$data->MerchantID' target='_blank' class='btn btn-sm btn-info'>Lihat</a>";
                    } else {
                        $disclaimer = '';
                    }

                    return $disclaimer;
                })
                ->rawColumns(['StatusNameCrowdo', 'StatusName', 'Photo', 'Action', 'Disclaimer', 'StatusPaymentName'])
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
        if ($request->partner == null) {
            return redirect()->route('merchant.membership')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }

        $status = $request->input('status-crowdo');
        // $loanID = $request->input('loan_id'); // March 27 23 by 26kito
        $partner = $request->input('partner');
        $amount = $request->input('amount');
        $batch = $request->input('batch');
        $user = Auth::user()->Name . ' - ' . Auth::user()->RoleID;
        $newStatusMembership = '';
        $approvedDate = $request->input('approved_date');
        $actionDate = $request->input('action_date');
        $rejectedID = $request->input('rejected_id');
        if ($rejectedID) {
            if (count($rejectedID) > 1) {
                $rejectedID = implode(", ", $request->input('rejected_id'));
            } else {
                $rejectedID = $rejectedID[0];
            }
        }

        if ($status == 5) {
            $newStatusMembership = 1;
        } else if ($status == 6) {
            $newStatusMembership = 2;
        } else if ($status == 7) {
            $newStatusMembership = 3;
        }

        $dataCrowdo = [
            // 'CrowdoLoanID' => $loanID,
            'CrowdoAmount' => $amount,
            'CrowdoBatch' => $batch,
            'CrowdoApprovedDate' => $approvedDate
        ];

        $maxDisclaimerID = DB::table('ms_history_membership')->max('disclaimer_id');
        $disclaimerID = $maxDisclaimerID + 1;

        $dataMembership = [
            'disclaimer_id' => $disclaimerID,
            'merchant_id' => $merchantID,
            'partner_id' => $partner,
            'nominal' => $amount,
            'batch_number' => $batch,
            'status_membership' => $newStatusMembership,
            'action_date' => $actionDate,
            'rejected_reason' => $rejectedID,
            'action_by' => $user
        ];

        $dataCouplePreneurCrowdoLog = [
            'MerchantID' => $merchantID,
            'StatusCrowdo' => $status,
            // 'NoteMembershipCouple' => $rejectedReason,
            'CreatedDate' => date('Y-m-d H:i:s'),
            'ActionBy' => $user
        ];

        try {
            $this->merchantMembershipService->updateStatusCrowdo($merchantID, $dataMembership, $status, $dataCrowdo, $dataCouplePreneurCrowdoLog);

            return redirect()->route('merchant.membership')->with('success', 'Status Crowdo Merchant berhasil di-update');
        } catch (\Throwable $th) {
            return redirect()->route('merchant.membership')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function updatePayment($merchantID, $membershipID, Request $request)
    {
        $statusPaymentID = $request->status_payment;

        DB::table('ms_history_membership')
            ->where('merchant_id', $merchantID)
            ->where('disclaimer_id', $membershipID)
            ->update(['status_payment_id' => $statusPaymentID]);

        return redirect()->back()->with('success', 'Status Crowdo Merchant berhasil di-update');
    }

    public function disclaimer($merchantID)
    {
        $dayName = Carbon::now()->locale('id')->translatedFormat('l');
        $date = Carbon::now()->locale('id')->translatedFormat('d F Y');

        $merchant = DB::table('ms_merchant_account AS mma')
            ->join('ms_history_disclaimer AS mhd', 'mma.MerchantID', 'mhd.merchant_id')
            ->selectRaw("mhd.disclaimer_id, 
                ANY_VALUE(mma.MerchantID) AS MerchantID, ANY_VALUE(mma.UsernameIDCard) AS UsernameIDCard, 
                ANY_VALUE(mma.NumberIDCard) AS NumberIDCard, ANY_VALUE(mma.StoreAddress) AS StoreAddress,
                ANY_VALUE(mhd.nominal) AS Nominal")
            ->where('mma.MerchantID', '=', $merchantID)
            ->whereRaw('mhd.disclaimer_id = (SELECT MAX(disclaimer_id) FROM ms_history_disclaimer WHERE merchant_id = ?)', [$merchantID])
            ->groupBy('mma.MerchantID', 'mhd.disclaimer_id')
            ->first();

        // get the submission count for each disclaimer id
        $submissionCount = DB::table('ms_history_disclaimer')
            ->select('disclaimer_id', DB::raw('COUNT(*) as submission_count'))
            ->where('merchant_id', '=', $merchantID)
            ->groupBy('disclaimer_id')
            ->get();

        $merchant->SubmissionCount = $submissionCount->sum('submission_count');

        $merchant->Penyebut = Helper::convertToWords($merchant->Nominal);
        $merchant->Nominal = Helper::formatCurrency($merchant->Nominal, '');

        $orderSeriesNumber = substr("0000" . $merchant->disclaimer_id, strlen($merchant->disclaimer_id));
        $orderTotalSubmit = substr("000" . $merchant->SubmissionCount, strlen($merchant->SubmissionCount));

        // get roman number for month
        $month = Helper::convertMonthToRomanNumerals(date('m'));

        $merchant->SeriesNumber = date('y') . ".$orderSeriesNumber.$orderTotalSubmit/B-KRTM/$month";

        return view('merchant.membership.disclaimer', ['merchant' => $merchant, 'dayName' => $dayName, 'date' => $date]);
    }

    public function rejectedReason()
    {
        $data = DB::table('ms_membership_status_rejection')->get();

        return $data;
    }
}
