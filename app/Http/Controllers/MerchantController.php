<?php

namespace App\Http\Controllers;

use App\Services\MerchantService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\If_;
use Yajra\DataTables\Facades\DataTables;

class MerchantController extends Controller
{
    protected $saveImageUrl;
    protected $baseImageUrl;

    public function __construct()
    {
        $this->saveImageUrl = config('app.save_image_url');
        $this->baseImageUrl = config('app.base_image_url');
    }

    public function account()
    {
        function countMerchantAccount($distributorId = "all", $thisYear = null, $thisMonth = null, $thisDay = null)
        {
            $merchantAccount = DB::table('ms_merchant_account')
                ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
                ->where('ms_merchant_account.IsTesting', 0)
                ->select('ms_merchant_account.MerchantID');

            if ($thisMonth != null && $thisYear != null) {
                $merchantAccount->whereYear('ms_merchant_account.CreatedDate', '=', $thisYear)
                    ->whereMonth('ms_merchant_account.CreatedDate', '=', $thisMonth);
            }

            if ($thisDay != null && $thisMonth != null && $thisYear != null) {
                $merchantAccount->whereYear('ms_merchant_account.CreatedDate', '=', $thisYear)
                    ->whereMonth('ms_merchant_account.CreatedDate', '=', $thisMonth)
                    ->whereDay('ms_merchant_account.CreatedDate', '=', $thisDay);
            }
            if ($distributorId != "all") {
                $merchantAccount->where('ms_merchant_account.DistributorID', '=', $distributorId);
            }
            if (Auth::user()->Depo != "ALL") {
                $depoUser = Auth::user()->Depo;
                $merchantAccount->where('ms_distributor.Depo', '=', $depoUser);
            }

            return $merchantAccount->count();
        }

        $thisDay = date('d');
        $thisMonth = date('m');
        $thisYear = date('Y');

        return view('merchant.account.index', [
            'countTotalMerchant' => countMerchantAccount(),
            'countNewMerchantThisMonth' => countMerchantAccount("all", $thisYear, $thisMonth),
            'countNewMerchantThisDay' => countMerchantAccount("all", $thisYear, $thisMonth, $thisDay),
            'countTotalMerchantBandung' => countMerchantAccount("D-2004-000005"),
            'countNewMerchantBandungThisMonth' => countMerchantAccount("D-2004-000005", $thisYear, $thisMonth),
            'countNewMerchantBandungThisDay' => countMerchantAccount("D-2004-000005", $thisYear, $thisMonth, $thisDay),
            'countTotalMerchantCakung' => countMerchantAccount("D-2004-000001"),
            'countNewMerchantCakungThisMonth' => countMerchantAccount("D-2004-000001", $thisYear, $thisMonth),
            'countNewMerchantCakungThisDay' => countMerchantAccount("D-2004-000001", $thisYear, $thisMonth, $thisDay),
            'countTotalMerchantCiracas' => countMerchantAccount("D-2004-000006"),
            'countNewMerchantCiracasThisMonth' => countMerchantAccount("D-2004-000006", $thisYear, $thisMonth),
            'countNewMerchantCiracasThisDay' => countMerchantAccount("D-2004-000006", $thisYear, $thisMonth, $thisDay),
            'countTotalMerchantSemarang' => countMerchantAccount("D-2004-000002"),
            'countNewMerchantSemarangThisMonth' => countMerchantAccount("D-2004-000002", $thisYear, $thisMonth),
            'countNewMerchantSemarangThisDay' => countMerchantAccount("D-2004-000002", $thisYear, $thisMonth, $thisDay)
        ]);
    }

    public function getAccounts(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $distributorId = $request->input('distributorId');
        $filterAssessment = $request->input('filterAssessment');

        // Get data account, jika tanggal filter kosong tampilkan semua data.
        $sqlAllAccount = DB::table('ms_merchant_account')
            ->leftJoin('ms_sales', 'ms_sales.SalesCode', 'ms_merchant_account.ReferralCode')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', '=', 'ms_merchant_account.MerchantID')
            ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_merchant_grade.GradeID')
            ->leftJoin('ms_merchant_assessment', function ($join) {
                $join->on('ms_merchant_assessment.MerchantID', 'ms_merchant_account.MerchantID');
                $join->where('ms_merchant_assessment.IsActive', 1);
            })
            ->where('ms_merchant_account.IsTesting', 0)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.Partner', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.CreatedDate', 'ms_merchant_account.StoreAddress', 'ms_merchant_account.ReferralCode', 'ms_distributor.DistributorName', 'ms_distributor_grade.Grade', 'ms_merchant_assessment.MerchantAssessmentID', 'ms_merchant_assessment.IsActive', 'ms_sales.SalesName');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('ms_merchant_account.CreatedDate', '>=', $fromDate)
                ->whereDate('ms_merchant_account.CreatedDate', '<=', $toDate);
        }

        if ($distributorId != null) {
            $sqlAllAccount->where('ms_merchant_account.DistributorID', '=', $distributorId);
        }

        if ($filterAssessment == "already-assessed") {
            $sqlAllAccount->where('ms_merchant_assessment.IsActive', 1);
        } elseif ($filterAssessment == "not-assessed") {
            $sqlAllAccount->whereRaw("(ms_merchant_assessment.MerchantAssessmentID IS NULL OR ms_merchant_assessment.IsActive = 0)");
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlAllAccount->where('ms_distributor.Depo', '=', $depoUser);
        }

        // Get data response
        $data = $sqlAllAccount;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('Partner', function ($data) {
                    if ($data->Partner != null) {
                        $partner = '<a class="badge badge-info">' . $data->Partner . '</a>';
                    } else {
                        $partner = '';
                    }
                    return $partner;
                })
                ->editColumn('Grade', function ($data) {
                    if ($data->Grade == null) {
                        $grade = "Retail";
                    } else {
                        $grade = $data->Grade;
                    }
                    return $grade;
                })
                ->addColumn('Product', function ($data) {
                    $productBtn = '<a href="/merchant/account/product/' . $data->MerchantID . '" class="btn-sm btn-info detail-order">Detail</a>';
                    return $productBtn;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/merchant/account/edit/' . $data->MerchantID . '" class="btn-sm btn-warning detail-order">Edit</a>';
                    return $actionBtn;
                })
                ->addColumn('Assessment', function ($data) {
                    if ($data->IsActive == 1) {
                        $actionBtn = '<a href="/merchant/account/assessment/' . $data->MerchantID . '" class="btn-sm bg-lightblue detail-order">Lihat</a>';
                    } else {
                        $actionBtn = '';
                    }
                    return $actionBtn;
                })
                ->filterColumn('ms_merchant_account.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(ms_merchant_account.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->rawColumns(['Partner', 'Product', 'Action', 'Assessment'])
                ->make(true);
        }
    }

    public function getGrade($distributorId)
    {
        $grade = DB::table('ms_distributor_grade')
            ->where('ms_distributor_grade.DistributorID', '=', $distributorId)
            ->select('ms_distributor_grade.GradeID', 'ms_distributor_grade.Grade')
            ->get();
        return response()->json($grade);
    }

    public function editAccount($merchantId)
    {
        $merchantById = DB::table('ms_merchant_account')
            ->leftJoin('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', 'ms_merchant_account.MerchantID')
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'ms_distributor.DistributorID', 'ms_distributor.DistributorName', 'ms_distributor_merchant_grade.GradeID', 'ms_merchant_account.ReferralCode', 'ms_merchant_account.Latitude', 'ms_merchant_account.Longitude', 'ms_merchant_account.ReferralCode')
            ->where('ms_merchant_account.MerchantID', '=', $merchantId)
            ->first();

        $distributorSql = DB::table('ms_distributor')
            ->select('DistributorID', 'DistributorName');

        $sales = DB::table('ms_sales')
            ->where('IsActive', 1)
            ->select('SalesCode', 'SalesName')->get();

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $distributorSql->where('ms_distributor.Depo', '=', $depoUser);
        }

        $distributor = $distributorSql->get();

        $grade = DB::table('ms_merchant_account')
            ->join('ms_distributor_grade', 'ms_distributor_grade.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->where('ms_merchant_account.MerchantID', '=', $merchantId)
            ->select('ms_distributor_grade.GradeID', 'ms_distributor_grade.Grade')
            ->get();

        return view('merchant.account.edit', [
            'merchantById' => $merchantById,
            'distributor' => $distributor,
            'grade' => $grade,
            'sales' => $sales
        ]);
    }

    public function updateAccount(Request $request, $merchantId)
    {
        $merchantGrade = DB::table('ms_distributor_merchant_grade')
            ->where('MerchantID', '=', $merchantId)
            ->select('MerchantID')->first();

        $request->validate([
            'store_name' => 'required|string',
            'owner_name' => 'required|string',
            'phone_number' => [
                'required',
                'numeric',
                Rule::unique('ms_merchant_account', 'PhoneNumber')->ignore($merchantId, 'MerchantID')
            ],
            'distributor' => 'required|exists:ms_distributor,DistributorID',
            'grade' => 'required|exists:ms_distributor_grade,GradeID',
            'address' => 'max:500',
            'referral_code' => 'string|nullable',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        $data = [
            'StoreName' => $request->input('store_name'),
            'OwnerFullName' => $request->input('owner_name'),
            'PhoneNumber' => $request->input('phone_number'),
            'OwnerPhoneNumber' => $request->input('phone_number'),
            'StorePhoneNumber' => $request->input('phone_number'),
            'DistributorID' => $request->input('distributor'),
            'RealDistributorID' => $request->input('distributor'),
            'StoreAddress' => $request->input('address'),
            'ReferralCode' => $request->input('referral_code'),
            'Latitude' => $request->input('latitude'),
            'Longitude' => $request->input('longitude')
        ];

        $dataGrade = [
            'DistributorID' => $request->input('distributor'),
            'MerchantID' => $merchantId,
            'GradeID' => $request->input('grade')
        ];

        try {
            DB::transaction(function () use ($merchantId, $merchantGrade, $data, $dataGrade) {
                DB::table('ms_merchant_account')
                    ->where('MerchantID', '=', $merchantId)
                    ->update($data);
                if ($merchantGrade) {
                    DB::table('ms_distributor_merchant_grade')
                        ->where('MerchantID', '=', $merchantId)
                        ->update($dataGrade);
                } else {
                    DB::table('ms_distributor_merchant_grade')
                        ->insert($dataGrade);
                }
            });

            return redirect()->route('merchant.account')->with('success', 'Data merchant berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('merchant.account')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function product($merchantId)
    {
        $merchant = DB::table('ms_merchant_account')
            ->where('MerchantID', '=', $merchantId)
            ->select('StoreName', 'OwnerFullName', 'StoreAddress', 'StoreImage', 'PhoneNumber', 'Latitude', 'Longitude')
            ->first();

        $operationalHour = DB::table('ms_operational_hour')
            ->where('MerchantID', '=', $merchantId)
            ->select('DayOfWeek', 'OpeningHour', 'ClosingHour')
            ->get();

        return view('merchant.product.index', [
            'merchantId' => $merchantId,
            'merchant' => $merchant,
            'operationalHour' => $operationalHour
        ]);
    }

    public function getProducts(Request $request, $merchantId)
    {
        $merchantProducts = DB::table('ms_product_merchant')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'ms_product_merchant.ProductID')
            ->join('ms_product_category', 'ms_product_category.ProductCategoryID', '=', 'ms_product.ProductCategoryID')
            ->join('ms_product_type', 'ms_product_type.ProductTypeID', '=', 'ms_product.ProductTypeID')
            ->join('ms_product_uom', 'ms_product_uom.ProductUOMID', '=', 'ms_product.ProductUOMID')
            ->where('ms_product_merchant.MerchantID', '=', $merchantId)
            ->select('ms_product_merchant.MerchantID', 'ms_product_merchant.ProductID', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_product_category.ProductCategoryName', 'ms_product_type.ProductTypeName', 'ms_product_uom.ProductUOMName', 'ms_product.ProductUOMDesc', 'ms_product_merchant.Price', 'ms_product_merchant.PurchasePrice', 'ms_product_merchant.IsFulfillment', 'ms_product_merchant.Quantity');

        $data = $merchantProducts->get();

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('ProductImage', function ($data) {
                    if ($data->ProductImage == null) {
                        $data->ProductImage = 'not-found.png';
                    }
                    return '<img src="' . $this->baseImageUrl . 'product/' . $data->ProductImage . '" alt="Product Image" height="90">';
                })
                ->editColumn('ProductName', function ($data) {
                    if ($data->IsFulfillment == 1) {
                        $fulfillment = '<span class="badge badge-info">Produk Konsinyasi</span>';
                    } else {
                        $fulfillment = '';
                    }
                    return $data->ProductName . '<br>' . $fulfillment;
                })
                ->addColumn('Action', function ($data) {
                    if (
                        Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "BM" ||
                        Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "AH" ||
                        Auth::user()->RoleID == "HR"
                    ) {
                        $actionBtn = '<a href="/merchant/account/product/edit/' . $data->MerchantID . '/' . $data->ProductID . '" class="btn btn-sm btn-warning mr-1">Edit</a>
                        <a data-merchant-id="' . $data->MerchantID . '" data-product-id="' . $data->ProductID . '" data-product-name="' . $data->ProductName . '" href="#" class="btn-delete btn btn-sm btn-danger">Delete</a>';
                    } else {
                        $actionBtn = '';
                    }

                    return $actionBtn;
                })
                ->rawColumns(['ProductName', 'ProductImage', 'Action'])
                ->make(true);
        }
    }

    public function editProduct($merchantId, $productId)
    {
        $merchantProduct = DB::table('ms_product_merchant')
            ->leftJoin('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'ms_product_merchant.MerchantID')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'ms_product_merchant.ProductID')
            ->where('ms_product_merchant.MerchantID', '=', $merchantId)
            ->where('ms_product_merchant.ProductID', '=', $productId)
            ->select('ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.StoreAddress', 'ms_merchant_account.StoreImage', 'ms_merchant_account.PhoneNumber', 'ms_product.ProductName', 'ms_product.ProductImage', 'ms_product_merchant.Price', 'ms_product_merchant.PurchasePrice', 'ms_product_merchant.IsFulfillment', 'ms_product_merchant.Quantity')
            ->first();

        return view('merchant.product.edit', [
            'merchantId' => $merchantId,
            'productId' => $productId,
            'merchantProduct' => $merchantProduct
        ]);
    }

    public function updateProduct(Request $request, $merchantId, $productId)
    {
        $request->validate([
            'price' => 'required|integer',
            'purchase_price' => 'required|integer',
            'is_fulfillment' => 'required',
            'quantity' => 'integer|nullable'
        ]);

        $updateMerchantProduct = DB::table('ms_product_merchant')
            ->where('MerchantID', '=', $merchantId)
            ->where('ProductID', '=', $productId)
            ->update([
                'Price' => $request->input('price'),
                'PurchasePrice' => $request->input('purchase_price'),
                'IsFulfillment' => $request->input('is_fulfillment'),
                'Quantity' => $request->input('quantity')
            ]);

        if ($updateMerchantProduct) {
            return redirect()->route('merchant.product', ['merchantId' => $merchantId])->with('success', 'Data produk merchant telah diubah');
        } else {
            return redirect()->route('merchant.product', ['merchantId' => $merchantId])->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function deleteProduct($merchantId, $productId)
    {
        $deleteProduct = DB::table('ms_product_merchant')
            ->where('MerchantID', '=', $merchantId)
            ->where('ProductID', '=', $productId)
            ->delete();

        if ($deleteProduct) {
            return redirect()->route('merchant.product', ['merchantId' => $merchantId])->with('success', 'Data produk merchant telah dihapus');
        } else {
            return redirect()->route('merchant.product', ['merchantId' => $merchantId])->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function editOperationalHour($merchantId)
    {
        $merchant = DB::table('ms_merchant_account')
            ->where('MerchantID', '=', $merchantId)
            ->select('StoreName', 'OwnerFullName', 'StoreAddress', 'StoreImage', 'PhoneNumber')
            ->first();

        $merchantOperationalHour = DB::table('ms_operational_hour')
            ->where('MerchantID', '=', $merchantId)
            ->select('DayOfWeek', 'OpeningHour', 'ClosingHour')
            ->get();

        return view('merchant.operationalHour.edit', [
            'merchantId' => $merchantId,
            'merchant' => $merchant,
            'merchantOperationalHour' => $merchantOperationalHour
        ]);
    }

    public function updateOperationalHour(Request $request, $merchantId)
    {
        $request->validate(
            [
                'opening_hour' => 'required',
                'opening_hour.*' => 'required|date_format:H:i',
                'closing_hour' => 'required',
                'closing_hour.*' => 'required|date_format:H:i|after:opening_hour.*',
            ],
            [
                'required' => 'The field is required.',
                'after' => 'The closing hour must be after opening hour.'
            ]
        );

        $day = $request->input('day');
        $open = $request->input('opening_hour');
        $close = $request->input('closing_hour');
        $dataOperationalHour = array_map(function () {
            return func_get_args();
        }, $day, $open, $close);

        try {
            DB::transaction(function () use ($dataOperationalHour, $merchantId) {
                foreach ($dataOperationalHour as &$value) {
                    $value = array_combine(['Day', 'Open', 'Close'], $value);
                    DB::table('ms_operational_hour')
                        ->where('MerchantID', '=', $merchantId)
                        ->where('DayOfWeek', '=', $value['Day'])
                        ->update([
                            'OpeningHour' => $value['Open'],
                            'ClosingHour' => $value['Close']
                        ]);
                }
            });

            return redirect()->route('merchant.product', ['merchantId' => $merchantId])->with('success', 'Data operational hour merchant telah diubah');
        } catch (\Throwable $th) {
            return redirect()->route('merchant.product', ['merchantId' => $merchantId])->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function merchantAssessment($merchantId)
    {
        $assessment = DB::table('ms_merchant_assessment')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', 'ms_merchant_assessment.MerchantID')
            ->join('ms_merchant_assessment_transaction', 'ms_merchant_assessment_transaction.MerchantAssessmentID', 'ms_merchant_assessment.MerchantAssessmentID')
            ->where('ms_merchant_assessment.MerchantID', $merchantId)
            ->where('ms_merchant_assessment.IsActive', 1)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_assessment.MerchantAssessmentID', 'ms_merchant_assessment.PhotoMerchantFront', 'ms_merchant_assessment.PhotoMerchantSide', 'ms_merchant_assessment.StruckDistribution', 'ms_merchant_assessment.TurnoverAverage', 'ms_merchant_assessment.PhotoStockProduct', 'ms_merchant_assessment.PhotoIDCard', 'ms_merchant_assessment.NumberIDCard')->first();

        $assessmentTransaction = DB::table('ms_merchant_assessment_transaction')
            ->where('MerchantAssessmentID', $assessment->MerchantAssessmentID)
            ->select('TransactionName')
            ->get()->toArray();

        $assessment->AssessmentTransaction = $assessmentTransaction;

        return view('merchant.account.assessment', [
            'assessment' => $assessment
        ]);
    }

    public function resetMerchantAssessment($merchantAssessmentId)
    {
        $resetAssessment = DB::table('ms_merchant_assessment')
            ->where('MerchantAssessmentID', $merchantAssessmentId)
            ->update([
                'IsActive' => 0
            ]);

        if ($resetAssessment) {
            return redirect()->route('merchant.account')->with('success', 'Data Assessment Merchant berhasil dihapus');
        } else {
            return redirect()->route('merchant.account')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function assessment()
    {
        return view('merchant.assessment.index');
    }

    public function getAssessments(MerchantService $merchantService, Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlAssessments = $merchantService->getDataAssessments();

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAssessments->whereDate('ms_merchant_assessment.CreatedAt', '>=', $fromDate)
                ->whereDate('ms_merchant_assessment.CreatedAt', '<=', $toDate);
        }

        $data = $sqlAssessments;
        // dd($data->get());
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedAt', function ($data) {
                    return date('d-M-Y', strtotime($data->CreatedAt));
                })
                ->editColumn('MerchantID', function ($data) {
                    if ($data->MerchantID == "null") {
                        $merchantID = "N/A";
                    } else {
                        $merchantID = $data->MerchantID;
                    }
                    return $merchantID;
                })
                ->editColumn('MerchantName', function ($data) {
                    if ($data->MerchantName == null) {
                        $merchantName = "N/A";
                    } else {
                        $merchantName = $data->MerchantName;
                    }
                    return $merchantName;
                })
                ->editColumn('MerchantNumber', function ($data) {
                    if ($data->MerchantNumber == null) {
                        $merchantNumber = "N/A";
                    } else {
                        $merchantNumber = $data->MerchantNumber;
                    }
                    return $merchantNumber;
                })
                ->editColumn('ReferralCode', function ($data) {
                    if ($data->ReferralCode == null) {
                        $salesCode = $data->SalesCodeStore;
                    } else {
                        $salesCode = $data->ReferralCode;
                    }
                    return $salesCode;
                })
                ->editColumn('SalesName', function ($data) {
                    if ($data->SalesName == null) {
                        $salesName = $data->SalesNameStore;
                    } else {
                        $salesName = $data->SalesName;
                    }
                    return $salesName;
                })
                ->addColumn('MerchantPhoto', function ($data) {
                    $img1 = '<div class="border text-center px-2">
                                <img src="' . $this->baseImageUrl . '/rtsales/merchantassessment/' . $data->PhotoMerchantFront . '" width="90px" height="70px" style="object-fit:cover;" />
                                <p>Tampak Depan</p>
                            </div>';
                    $img2 = '<div class="border text-center px-2">
                                <img src="' . $this->baseImageUrl . '/rtsales/merchantassessment/' . $data->PhotoMerchantSide . '" width="90px" height="70px" style="object-fit:cover;" />
                                <p>Tampak Samping</p>
                            </div>';
                    $fotoToko = '<div class="d-flex border">' . $img1 . $img2 . '</div>';

                    return $fotoToko;
                })
                ->addColumn('StruckPhoto', function ($data) {
                    $fotoStruk = '<div class="border text-center px-2">
                                    <img src="' . $this->baseImageUrl . '/rtsales/merchantassessment/' . $data->StruckDistribution . '" width="90px" height="70px" style="object-fit:cover;" />
                                </div>';
                    return $fotoStruk;
                })
                ->addColumn('StockPhoto', function ($data) {
                    $fotoStok = '<div class="border text-center px-2">
                                    <img src="' . $this->baseImageUrl . '/rtsales/merchantassessment/' . $data->PhotoStockProduct . '" width="90px" height="70px" style="object-fit:cover;" />
                                </div>';
                    return $fotoStok;
                })
                ->addColumn('IdCardPhoto', function ($data) {
                    $fotoKTP = '<div class="border text-center px-2">
                                    <img src="' . $this->baseImageUrl . '/rtsales/merchantassessment/' . $data->PhotoIDCard . '" width="90px" height="70px" style="object-fit:cover;" />
                                </div>';
                    return $fotoKTP;
                })
                ->filterColumn('MerchantName', function ($query, $keyword) {
                    $query->whereRaw("ANY_VALUE(ms_merchant_account.StoreName) like ?", ["%$keyword%"]);
                })
                ->filterColumn('MerchantNumber', function ($query, $keyword) {
                    $query->whereRaw("ANY_VALUE(ms_merchant_account.PhoneNumber) like ?", ["%$keyword%"]);
                })
                ->filterColumn('ReferralCode', function ($query, $keyword) {
                    $query->whereRaw("ANY_VALUE(ms_merchant_account.ReferralCode) like ?", ["%$keyword%"]);
                })
                ->filterColumn('SalesName', function ($query, $keyword) {
                    $query->whereRaw("ANY_VALUE(sales_merchant.SalesName) like ?", ["%$keyword%"]);
                })
                ->rawColumns(['MerchantPhoto', 'StruckPhoto', 'StockPhoto', 'IdCardPhoto'])
                ->make(true);
        }
    }

    public function createAssessment()
    {
        $stores = DB::table('ms_store')
            ->leftJoin('ms_merchant_assessment', function ($join) {
                $join->on('ms_merchant_assessment.StoreID', 'ms_store.StoreID');
                $join->where('ms_merchant_assessment.IsActive', 1);
            })
            ->whereNull('ms_merchant_assessment.MerchantAssessmentID')
            ->select('ms_store.StoreID', 'ms_store.StoreName')
            ->orderBy('ms_store.StoreID')
            ->get();

        $merchants = DB::table('ms_merchant_account')
            ->leftJoin('ms_merchant_assessment', function ($join) {
                $join->on('ms_merchant_assessment.MerchantID', 'ms_merchant_account.MerchantID');
                $join->where('ms_merchant_assessment.IsActive', 1);
            })
            ->whereNull('ms_merchant_assessment.MerchantAssessmentID')
            ->where('ms_merchant_account.IsTesting', 0)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName')
            ->orderBy('ms_merchant_account.MerchantID')
            ->get();

        return view('merchant.assessment.create', [
            'stores' => $stores,
            'merchants' => $merchants
        ]);
    }

    public function storeAssessment(Request $request)
    {
        $request->validate([
            'merchant_front_photo' => 'required|image',
            'merchant_side_photo' => 'required|image',
            'struck_photo' => 'required|image',
            'stock_photo' => 'required|image',
            'id_card_photo' => 'required|image',
            'id_card_number' => [
                'required', 'numeric', 'digits:16',
                Rule::unique('ms_merchant_assessment', 'NumberIDCard')
                    ->where(function ($query) {
                        return $query->where('IsActive', 1);
                    })
            ],
            'average_omzet' => 'required|numeric',
            'transaction' => 'required',
            'store' => 'required',
            'merchant' => 'required'
        ]);

        $frontPhotoName = $request->input('store') . 'photoMerchantFront' . time() . '.' . $request->file('merchant_front_photo')->extension();
        $sidePhotoName = $request->input('store') . 'photoMerchantSide' . time() . '.' . $request->file('merchant_side_photo')->extension();
        $struckPhotoName = $request->input('store') . 'struckDistribution' . time() . '.' . $request->file('struck_photo')->extension();
        $stockPhotoName = $request->input('store') . 'photoStockProduct' . time() . '.' . $request->file('stock_photo')->extension();
        $idCardPhotoName = $request->input('store') . 'photoIDCard' . time() . '.' . $request->file('id_card_photo')->extension();

        $request->file('merchant_front_photo')->move($this->saveImageUrl . 'rtsales/merchantassessment/', $frontPhotoName);
        $request->file('merchant_side_photo')->move($this->saveImageUrl . 'rtsales/merchantassessment/', $sidePhotoName);
        $request->file('struck_photo')->move($this->saveImageUrl . 'rtsales/merchantassessment/', $struckPhotoName);
        $request->file('stock_photo')->move($this->saveImageUrl . 'rtsales/merchantassessment/', $stockPhotoName);
        $request->file('id_card_photo')->move($this->saveImageUrl . 'rtsales/merchantassessment/', $idCardPhotoName);

        $dataAssessment = [
            'PhotoMerchantFront' => $frontPhotoName,
            'PhotoMerchantSide' => $sidePhotoName,
            'StruckDistribution' => $struckPhotoName,
            'PhotoStockProduct' => $stockPhotoName,
            'PhotoIDCard' => $idCardPhotoName,
            'TurnoverAverage' => $request->input('average_omzet'),
            'NumberIDCard' => $request->input('id_card_number'),
            'StoreID' => $request->input('store'),
            'MerchantID' => $request->input('merchant'),
            'IsActive' => 1,
            'CreatedAt' => date('Y-m-d H:i:s'),
            'CreatedFrom' => 'DASHBOARD'
        ];

        try {
            DB::transaction(function () use ($dataAssessment, $request) {
                $assessmentID = DB::table('ms_merchant_assessment')->insertGetId($dataAssessment, 'MerchantAssessmentID');

                $dataAssessmentTransaction = [];
                $assessmentTransaction = array_map(function () {
                    return func_get_args();
                }, $request->input('transaction'));

                foreach ($assessmentTransaction as $key => $value) {
                    $value = array_combine(['TransactionCode'], $value);
                    $value += ['MerchantAssessmentID' => $assessmentID];
                    if ($value['TransactionCode'] == "TN") {
                        $value += ['TransactionName' => "Tunai"];
                    } elseif ($value['TransactionCode'] == "UM") {
                        $value += ['TransactionName' => "Uangme"];
                    } else {
                        $value += ['TransactionName' => "Kredit"];
                    }
                    array_push($dataAssessmentTransaction, $value);
                }

                DB::table('ms_merchant_assessment_transaction')->insert($dataAssessmentTransaction);
            });
            return redirect()->route('merchant.assessment')->with('success', 'Data Assessment Merchant berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('merchant.assessment')->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function powerMerchant()
    {
        $sqlMerchant = DB::table('ms_merchant_account')
            ->where('IsTesting', 0)
            ->where('IsPowerMerchant', 0)
            ->select('MerchantID', 'StoreName')
            ->get();

        return view('merchant.powerMerchant.index', [
            'merchant' => $sqlMerchant
        ]);
    }

    public function getPowerMerchant(Request $request)
    {
        $sqlPowerMerchant = DB::table('ms_merchant_account')
            ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'ms_merchant_account.DistributorID')
            ->leftJoin('ms_distributor_merchant_grade', 'ms_distributor_merchant_grade.MerchantID', '=', 'ms_merchant_account.MerchantID')
            ->leftJoin('ms_distributor_grade', 'ms_distributor_grade.GradeID', '=', 'ms_distributor_merchant_grade.GradeID')
            ->where('ms_merchant_account.IsTesting', 0)
            ->where('ms_merchant_account.IsPowerMerchant', 1)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.Partner', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'ms_distributor.DistributorName', 'ms_distributor_grade.Grade');

        $data = $sqlPowerMerchant;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('Partner', function ($data) {
                    if ($data->Partner != null) {
                        $partner = '<a class="badge badge-info">' . $data->Partner . '</a>';
                    } else {
                        $partner = '';
                    }
                    return $partner;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a class="btn btn-sm btn-danger delete-power-merchant" data-merchant-name="' . $data->StoreName . '" data-merchant-id="' . $data->MerchantID . '">Hapus</a>';
                    return $actionBtn;
                })
                ->rawColumns(['Partner', 'Action'])
                ->make(true);
        }
    }

    public function insertPowerMerchant(Request $request)
    {
        $request->validate([
            'power_merchant' => 'required',
            'power_merchant.*' => 'exists:ms_merchant_account,MerchantID'
        ]);

        $data = array_map(function () {
            return func_get_args();
        }, $request->input('power_merchant'));

        foreach ($data as $key => $value) {
            $data[$key][] = 1;
        }

        try {
            foreach ($data as &$value) {
                $value = array_combine(['MerchantID', 'IsPowerMerchant'], $value);
                DB::table('ms_merchant_account')
                    ->where('MerchantID', '=', $value['MerchantID'])
                    ->update([
                        'IsPowerMerchant' => $value['IsPowerMerchant']
                    ]);
            }

            return redirect()->route('merchant.powermerchant')->with('success', 'Data Power Merchant berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('merchant.powermerchant')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function deletePowerMerchant($merchantId)
    {
        $deletePowerMerchant = DB::table('ms_merchant_account')
            ->where('MerchantID', '=', $merchantId)
            ->update([
                'IsPowerMerchant' => 0
            ]);

        if ($deletePowerMerchant) {
            return redirect()->route('merchant.powermerchant')->with('success', 'Data Power Merchant berhasil dihapus');
        } else {
            return redirect()->route('merchant.powermerchant')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function otp()
    {
        return view('merchant.otp.index');
    }

    public function getOtps(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Get data otp, jika tanggal filter kosong tampilkan semua data.
        $sqlAllAccount = DB::table('ms_verification')
            ->join('ms_verification_log', 'ms_verification_log.PhoneNumber', '=', 'ms_verification.PhoneNumber')
            ->where('ms_verification.Type', '=', 'MERCHANT')
            ->select('ms_verification.PhoneNumber', 'ms_verification.OTP', 'ms_verification.IsVerified', 'ms_verification_log.SendOn', 'ms_verification_log.ReceiveOn');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllAccount->whereDate('ms_verification_log.SendOn', '>=', $fromDate)
                ->whereDate('ms_verification_log.SendOn', '<=', $toDate);
        }

        // Get data response
        $data = $sqlAllAccount;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('IsVerified', function ($data) {
                    if ($data->IsVerified == "0") {
                        $isVerified = '<span class="badge badge-danger">Belum Terverifikasi</span>';
                    } elseif ($data->IsVerified == "1") {
                        $isVerified = '<span class="badge badge-success">Terverifikasi</span>';
                    }

                    return $isVerified;
                })
                ->editColumn('SendOn', function ($data) {
                    return date('d-M-Y H:i:s', strtotime($data->SendOn));
                })
                ->editColumn('ReceiveOn', function ($data) {
                    return date('d-M-Y H:i:s', strtotime($data->ReceiveOn));
                })
                ->filterColumn('ms_verification_log.SendOn', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(ms_verification_log.SendOn,'%d-%b-%Y %H:%i:%s') like ?", ["%$keyword%"]);
                })
                ->filterColumn('ms_verification_log.ReceiveOn', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(ms_verification_log.ReceiveOn,'%d-%b-%Y %H:%i:%s') like ?", ["%$keyword%"]);
                })
                ->rawColumns(['IsVerified'])
                ->make(true);
        }
    }

    public function restock()
    {
        function countMerchantRestock($distributorId = "all", $thisYear = null, $thisMonth = null, $thisDay = null)
        {
            $merchantRestock = DB::table('tx_merchant_order')
                ->leftJoin('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
                ->join('ms_distributor', 'ms_distributor.DistributorID', '=', 'tx_merchant_order.DistributorID')
                ->where('ms_merchant_account.IsTesting', 0)
                ->select('tx_merchant_order.StockOrderID');

            if ($thisMonth != null && $thisYear != null) {
                $merchantRestock->whereYear('tx_merchant_order.CreatedDate', '=', $thisYear)
                    ->whereMonth('tx_merchant_order.CreatedDate', '=', $thisMonth);
            }

            if ($thisDay != null && $thisMonth != null && $thisYear != null) {
                $merchantRestock->whereYear('tx_merchant_order.CreatedDate', '=', $thisYear)
                    ->whereMonth('tx_merchant_order.CreatedDate', '=', $thisMonth)
                    ->whereDay('tx_merchant_order.CreatedDate', '=', $thisDay);
            }
            if ($distributorId != "all") {
                $merchantRestock->where('tx_merchant_order.DistributorID', '=', $distributorId);
            }
            if (Auth::user()->Depo != "ALL") {
                $depoUser = Auth::user()->Depo;
                $merchantRestock->where('ms_distributor.Depo', '=', $depoUser);
            }

            return $merchantRestock->count();
        }

        $thisDay = date('d');
        $thisMonth = date('m');
        $thisYear = date('Y');

        return view('merchant.restock.index', [
            'countTotalRestock' => countMerchantRestock(),
            'countRestockThisMonth' => countMerchantRestock("all", $thisYear, $thisMonth),
            'countRestockThisDay' => countMerchantRestock("all", $thisYear, $thisMonth, $thisDay),
            'countTotalRestockBandung' => countMerchantRestock("D-2004-000005"),
            'countRestockBandungThisMonth' => countMerchantRestock("D-2004-000005", $thisYear, $thisMonth),
            'countRestockBandungThisDay' => countMerchantRestock("D-2004-000005", $thisYear, $thisMonth, $thisDay),
            'countTotalRestockCakung' => countMerchantRestock("D-2004-000001"),
            'countRestockCakungThisMonth' => countMerchantRestock("D-2004-000001", $thisYear, $thisMonth),
            'countRestockCakungThisDay' => countMerchantRestock("D-2004-000001", $thisYear, $thisMonth, $thisDay),
            'countTotalRestockCiracas' => countMerchantRestock("D-2004-000006"),
            'countRestockCiracasThisMonth' => countMerchantRestock("D-2004-000006", $thisYear, $thisMonth),
            'countRestockCiracasThisDay' => countMerchantRestock("D-2004-000006", $thisYear, $thisMonth, $thisDay),
            'countTotalRestockSemarang' => countMerchantRestock("D-2004-000002"),
            'countRestockSemarangThisMonth' => countMerchantRestock("D-2004-000002", $thisYear, $thisMonth),
            'countRestockSemarangThisDay' => countMerchantRestock("D-2004-000002", $thisYear, $thisMonth, $thisDay)
        ]);
    }

    public function getRestocks(Request $request, MerchantService $merchantService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $paymentMethodId = $request->input('paymentMethodId');

        $startDate = new DateTime($fromDate) ?? new DateTime();
        $endDate = new DateTime($toDate) ?? new DateTime();
        $startDateFormat = $startDate->format('Y-m-d');
        $endDateFormat = $endDate->format('Y-m-d');

        $sqlAllAccount = $merchantService->merchantRestock()
            ->whereDate('Restock.CreatedDate', '>=', $startDateFormat)
            ->whereDate('Restock.CreatedDate', '<=', $endDateFormat);

        if ($paymentMethodId != null) {
            $sqlAllAccount->where('Restock.PaymentMethodID', '=', $paymentMethodId);
        }
        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlAllAccount->where('Restock.Depo', '=', $depoUser);
        }

        // Get data response
        $data = $sqlAllAccount;

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->CreatedDate));
                })
                ->addColumn('MarginRealPercentage', function ($data) {
                    if ($data->MarginReal == null) {
                        $data->MarginReal = 0;
                    }

                    $marginReal = round(($data->MarginReal / $data->NettPrice) * 100, 2);
                    return $marginReal;
                })
                ->addColumn('MarginEstimationPercentage', function ($data) {
                    if ($data->MarginEstimation == null) {
                        $data->MarginEstimation = 0;
                    }

                    if ($data->NettPrice - $data->TotalPriceNotInStock == 0) {
                        $marginEstimation = 0;
                    } else {
                        $marginEstimation = round((($data->MarginEstimation / ($data->NettPrice - $data->TotalPriceNotInStock)) * 100), 2);
                    }
                    return $marginEstimation;
                })
                ->addColumn('TotalMargin', function ($data) {
                    return $data->MarginReal + $data->MarginEstimation;
                })
                ->addColumn('TotalMarginPercentage', function ($data) {
                    if ($data->NettPrice - $data->TotalPriceNotInStock == 0) {
                        $totalMarginPercentage = 0;
                    } else {
                        $totalMarginPercentage = round((($data->MarginReal + $data->MarginEstimation) / ($data->NettPrice - $data->TotalPriceNotInStock)) * 100, 2);
                    }
                    return $totalMarginPercentage;
                })
                ->addColumn('Notes', function ($data) {
                    if ($data->TotalPriceNotInStock > 0) {
                        $notes = "Terdapat produk yg tidak tersedia di list stock.";
                    } else {
                        $notes = "";
                    }
                    return $notes;
                })
                ->editColumn('Grade', function ($data) {
                    if ($data->Grade != null) {
                        $grade = $data->Grade;
                    } else {
                        $grade = 'Retail';
                    }
                    return $grade;
                })
                ->editColumn('Partner', function ($data) {
                    if ($data->Partner != null) {
                        $partner = '<a class="badge badge-info">' . $data->Partner . '</a>';
                    } else {
                        $partner = '';
                    }
                    return $partner;
                })
                ->addColumn('Invoice', function ($data) {
                    if ($data->StatusOrderID == "S009") {
                        $invoice = "";
                    } else {
                        $invoice = '<a href="/restock/invoice/' . $data->StockOrderID . '" target="_blank" class="btn-sm btn-primary">Cetak</a>';
                    }
                    return $invoice;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/merchant/restock/detail/' . $data->StockOrderID . '" class="btn-sm btn-info detail-order">Detail</a>';
                    return $actionBtn;
                })
                ->addColumn('TotalAmount', function ($data) {
                    return $data->NettPrice + $data->ServiceChargeNett + $data->DeliveryFee;
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
                ->filterColumn('tx_merchant_order.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(tx_merchant_order.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->rawColumns(['Partner', 'Action', 'Invoice', 'StatusOrder'])
                ->make(true);
        }
    }

    public function getRestockProduct(Request $request, MerchantService $merchantService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $filterAssessment = $request->input('filterAssessment');

        $startDate = new DateTime($fromDate) ?? new DateTime();
        $endDate = new DateTime($toDate) ?? new DateTime();
        $startDateFormat = $startDate->format('Y-m-d');
        $endDateFormat = $endDate->format('Y-m-d');

        $paymentMethodId = $request->input('paymentMethodId');

        $sqlMain = $merchantService->merchantRestockAllProduct()->toSql();

        $sqlAllAccount = DB::table(DB::raw("($sqlMain) AS RestockProduct"))
            ->whereDate('RestockProduct.CreatedDate', '>=', $startDateFormat)
            ->whereDate('RestockProduct.CreatedDate', '<=', $endDateFormat)
            ->selectRaw("
                RestockProduct.*,
                (SELECT IFNULL(SUM(tx_merchant_delivery_order_detail.Qty), 0) FROM tx_merchant_delivery_order_detail 
                    WHERE tx_merchant_delivery_order_detail.DeliveryOrderID IN (
                        SELECT DeliveryOrderID FROM tx_merchant_delivery_order WHERE tx_merchant_delivery_order.StockOrderID = RestockProduct.StockOrderID 
                        AND tx_merchant_delivery_order.StatusDO = 'S025'
                    ) AND tx_merchant_delivery_order_detail.ProductID = RestockProduct.ProductID
                ) AS DOSelesai,

                (SELECT IFNULL(SUM(Qty), 0) FROM tx_merchant_delivery_order_detail 
                    WHERE DeliveryOrderID IN (
                        SELECT DeliveryOrderID FROM tx_merchant_delivery_order WHERE StockOrderID = RestockProduct.StockOrderID 
                        AND (StatusDO = 'S024' OR StatusDO = 'S025')
                    ) AND ProductID = RestockProduct.ProductID
                    AND (StatusExpedition = 'S030' OR StatusExpedition = 'S031')
                ) AS QtyDOkirim,

                (SELECT ms_stock_product_log.PurchasePrice FROM ms_stock_product_log
                    LEFT JOIN tx_merchant_expedition_detail ON tx_merchant_expedition_detail.MerchantExpeditionDetailID = ms_stock_product_log.MerchantExpeditionDetailID
                    LEFT JOIN tx_merchant_delivery_order_detail ON tx_merchant_delivery_order_detail.DeliveryOrderDetailID = tx_merchant_expedition_detail.DeliveryOrderDetailID
                    WHERE tx_merchant_delivery_order_detail.DeliveryOrderID IN (
                        SELECT DeliveryOrderID FROM tx_merchant_delivery_order 
                        WHERE StockOrderID = RestockProduct.StockOrderID 
                        AND (StatusDO = 'S024' OR StatusDO = 'S025')
                    ) AND tx_merchant_delivery_order_detail.ProductID = RestockProduct.ProductID
                    AND (tx_merchant_delivery_order_detail.StatusExpedition = 'S030' OR tx_merchant_delivery_order_detail.StatusExpedition = 'S031')
                    ORDER BY ms_stock_product_log.CreatedDate LIMIT 1
                ) AS PurchasePriceReal,

                (SELECT PurchasePrice FROM ms_stock_product 
                    WHERE ProductID = RestockProduct.ProductID AND DistributorID = RestockProduct.DistributorID 
                    AND ConditionStock = 'GOOD STOCK' AND Qty > 0
                    ORDER BY LevelType, CreatedDate LIMIT 1
                ) AS PurchasePriceEstimation
            ");

        if ($paymentMethodId != null) {
            $sqlAllAccount->where('RestockProduct.PaymentMethodID', '=', $paymentMethodId);
        }

        if (Auth::user()->Depo != "ALL") {
            $depoUser = Auth::user()->Depo;
            $sqlAllAccount->where('RestockProduct.Depo', '=', $depoUser);
        }

        if ($filterAssessment == "already-assessed") {
            $sqlAllAccount->whereNotNull('RestockProduct.NumberIDCard');
        } elseif ($filterAssessment == "not-assessed") {
            $sqlAllAccount->whereNull('RestockProduct.NumberIDCard');
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
                    if ($data->Grade != null) {
                        $grade = $data->Grade;
                    } else {
                        $grade = 'Retail';
                    }
                    return $grade;
                })
                ->editColumn('Partner', function ($data) {
                    if ($data->Partner != null) {
                        $partner = '<a class="badge badge-info">' . $data->Partner . '</a>';
                    } else {
                        $partner = '';
                    }
                    return $partner;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/merchant/restock/detail/' . $data->StockOrderID . '" class="btn-sm btn-info detail-order">Detail</a>';
                    return $actionBtn;
                })
                ->addColumn('TotalAmount', function ($data) {
                    return $data->NettPrice + $data->ServiceChargeNett + $data->DeliveryFee;
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
                ->editColumn('PurchasePriceEstimation', function ($data) {
                    if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "BM") {
                        $purchasePriceEstimation = $data->PurchasePriceEstimation;
                    } else {
                        $purchasePriceEstimation = "";
                    }
                    return $purchasePriceEstimation;
                })
                ->addColumn('MarginEstimation', function ($data) {
                    if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "BM") {
                        if ($data->PurchasePriceEstimation == null) {
                            $marginEstimation = "-";
                        } else {
                            $marginEstimation = ($data->Nett - $data->PurchasePriceEstimation) * ($data->PromisedQuantity - $data->QtyDOkirim);
                        }
                    } else {
                        $marginEstimation = "";
                    }
                    return $marginEstimation;
                })
                ->addColumn('MarginEstimationPercentage', function ($data) {
                    if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "BM") {
                        if ($data->PurchasePriceEstimation == null) {
                            $marginEstimation = "-";
                            $marginEstimationPercentage = "-";
                        } else {
                            $marginEstimation = ($data->Nett - $data->PurchasePriceEstimation) * ($data->PromisedQuantity - $data->QtyDOkirim);
                            $divided = ($data->PromisedQuantity - $data->QtyDOkirim) * $data->Nett;
                            if ($divided == 0) {
                                $marginEstimationPercentage = 0;
                            } else {
                                $marginEstimationPercentage = round(($marginEstimation / $divided) * 100, 2);
                            }
                        }
                    } else {
                        $marginEstimationPercentage = "";
                    }

                    return $marginEstimationPercentage;
                })
                ->editColumn('PurchasePriceReal', function ($data) {
                    if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "BM") {
                        $purchasePriceReal = $data->PurchasePriceReal;
                    } else {
                        $purchasePriceReal = "";
                    }
                    return $purchasePriceReal;
                })
                ->addColumn('MarginReal', function ($data) {
                    if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "BM") {
                        if ($data->PurchasePriceReal == null) {
                            $marginReal = "-";
                        } else {
                            $marginReal = ($data->Nett - $data->PurchasePriceReal) * $data->QtyDOkirim;
                        }
                    } else {
                        $marginReal = "";
                    }
                    return $marginReal;
                })
                ->addColumn('MarginRealPercentage', function ($data) {
                    if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "BM") {
                        if ($data->PurchasePriceReal == null) {
                            $marginReal = "-";
                        } else {
                            $marginReal = ($data->Nett - $data->PurchasePriceReal) * $data->QtyDOkirim;
                        }
                        if ($marginReal == "-") {
                            $marginRealPercentage = "-";
                        } else {
                            $marginRealPercentage = round(($marginReal / ($data->QtyDOkirim * $data->Nett)) * 100, 2);
                        }
                    } else {
                        $marginRealPercentage = "";
                    }

                    return $marginRealPercentage;
                })
                ->addColumn('TotalMargin', function ($data) {
                    if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "BM") {
                        if ($data->PurchasePriceEstimation == null) {
                            $marginEstimation = 0;
                        } else {
                            $marginEstimation = ($data->Nett - $data->PurchasePriceEstimation) * ($data->PromisedQuantity - $data->QtyDOkirim);
                        }
                        if ($data->PurchasePriceReal == null) {
                            $marginReal = 0;
                        } else {
                            $marginReal = ($data->Nett - $data->PurchasePriceReal) * $data->QtyDOkirim;
                        }
                        $totalMargin = $marginEstimation + $marginReal;
                    } else {
                        $totalMargin = "";
                    }

                    return $totalMargin;
                })
                ->addColumn('TotalMarginPercentage', function ($data) {
                    if (Auth::user()->RoleID == "IT" || Auth::user()->RoleID == "FI" || Auth::user()->RoleID == "BM") {
                        if ($data->PurchasePriceEstimation == null) {
                            $marginEstimation = 0;
                        } else {
                            $marginEstimation = ($data->Nett - $data->PurchasePriceEstimation) * ($data->PromisedQuantity - $data->QtyDOkirim);
                        }
                        if ($data->PurchasePriceReal == null) {
                            $marginReal = 0;
                        } else {
                            $marginReal = ($data->Nett - $data->PurchasePriceReal) * $data->QtyDOkirim;
                        }
                        $totalMarginPercentage = round((($marginEstimation + $marginReal) / ($data->PromisedQuantity * $data->Nett)) * 100, 2);
                    } else {
                        $totalMarginPercentage = "";
                    }

                    return $totalMarginPercentage;
                })
                ->filterColumn('RestockProduct.CreatedDate', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(RestockProduct.CreatedDate,'%d-%b-%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->addColumn('SubTotalPrice', function ($data) {
                    $subTotalPrice = $data->Nett * $data->PromisedQuantity;
                    return "$subTotalPrice";
                })
                ->editColumn('Price', function ($data) {
                    return "$data->Price";
                })
                ->rawColumns(['Partner', 'Action', 'StatusOrder'])
                ->make(true);
        }
    }

    public function restockDetails($stockOrderId)
    {
        $merchant = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->where('tx_merchant_order.StockOrderID', '=', $stockOrderId)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.DiscountVoucher', 'tx_merchant_order.ServiceChargeNett', 'tx_merchant_order.DeliveryFee', 'ms_status_order.StatusOrder', 'ms_payment_method.PaymentMethodName', 'tx_merchant_order.DistributorID', 'tx_merchant_order.StatusOrderID')
            ->first();

        $merchantOrderHistory = DB::table('tx_merchant_order_log')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_order_log.StatusOrderId')
            ->where('tx_merchant_order_log.StockOrderId', '=', $stockOrderId)
            ->selectRaw('tx_merchant_order_log.StatusOrderId, ANY_VALUE(tx_merchant_order_log.ProcessTime) AS ProcessTime, ANY_VALUE(ms_status_order.StatusOrder) AS StatusOrder')
            ->orderByDesc('ProcessTime')
            ->groupBy('tx_merchant_order_log.StatusOrderId')
            ->get();

        $stockOrderById = DB::table('tx_merchant_order_detail')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_order_detail.ProductID')
            ->where('tx_merchant_order_detail.StockOrderID', '=', $stockOrderId)
            ->select('tx_merchant_order_detail.*', 'ms_product.ProductName')->get();

        $subTotal = 0;
        foreach ($stockOrderById as $key => $value) {
            $subTotal += $value->Nett * $value->PromisedQuantity;
        }

        return view('merchant.restock.details', [
            'stockOrderId' => $stockOrderId,
            'merchant' => $merchant,
            'merchantOrderHistory' => $merchantOrderHistory,
            'stockOrderById' => $stockOrderById,
            'subTotal' => $subTotal
        ]);
    }

    public function invoice($stockOrderId)
    {
        $merchant = DB::table('tx_merchant_order')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_merchant_order.MerchantID')
            ->join('ms_status_order', 'ms_status_order.StatusOrderID', '=', 'tx_merchant_order.StatusOrderID')
            ->join('ms_payment_method', 'ms_payment_method.PaymentMethodID', '=', 'tx_merchant_order.PaymentMethodID')
            ->where('tx_merchant_order.StockOrderID', '=', $stockOrderId)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.OwnerFullName', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreAddress', 'tx_merchant_order.CreatedDate', 'tx_merchant_order.DiscountPrice', 'tx_merchant_order.ServiceChargeNett', 'ms_status_order.StatusOrder', 'ms_payment_method.PaymentMethodName')
            ->first();

        $stockOrderById = DB::table('tx_merchant_order_detail')
            ->leftJoin('ms_product', 'ms_product.ProductID', '=', 'tx_merchant_order_detail.ProductID')
            ->where('StockOrderID', '=', $stockOrderId)
            ->select('tx_merchant_order_detail.*', 'ms_product.ProductName')->get();

        $subTotal = 0;
        foreach ($stockOrderById as $key => $value) {
            $subTotal += $value->Nett * $value->PromisedQuantity;
        }

        return view('merchant.restock.invoice', [
            'stockOrderId' => $stockOrderId,
            'merchant' => $merchant,
            'stockOrderById' => $stockOrderById,
            'subTotal' => $subTotal
        ]);
    }
}
