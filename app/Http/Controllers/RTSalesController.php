<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RTSalesService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class RTSalesController extends Controller
{
    public function saleslist()
    {
        return view('rtsales.saleslist.index');
    }

    public function getDataSales(Request $request, RTSalesService $rTSalesService)
    {
        $data = $rTSalesService->salesLists();
        $depoUser = Auth::user()->Depo;

        if ($depoUser != "ALL" && $depoUser != "REG1" && $depoUser != "REG2") {
            $data->where('ms_sales.Team', $depoUser);
        }
        if ($depoUser === "REG1") {
            $data->whereIn('ms_sales.Team', ['SMG', 'YYK']);
        }
        if ($depoUser === "REG2") {
            $data->whereIn('ms_sales.Team', ['CRS', 'CKG', 'BDG']);
        }

        // Return Data Using DataTables with Ajax
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
                ->addColumn('Action', function ($data) {
                    $btn = '<a class="btn btn-xs btn-warning" href="/rtsales/saleslist/edit/' . $data->SalesCode . '">Ubah</a>
                                <a class="btn btn-xs btn-danger delete-sales" href="#" data-sales-name="' . $data->SalesName . '" data-sales-code="' . $data->SalesCode . '">Hapus</a>';
                    return $btn;
                })
                ->filterColumn('Team', function ($query, $keyword) {
                    $sql = "CONCAT(ms_sales.TeamBy, ' ', ms_team_name.TeamName) like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->rawColumns(['IsActive', 'Action'])
                ->make(true);
        }
    }

    public function addSales()
    {
        $sqlTeam = DB::table('ms_team_name')
            ->select('*')->get();

        $sqlProductGroup = DB::table('ms_product_group')
            ->select('*')->get();

        $sqlWorkStatus = DB::table('ms_sales_work_status')
            ->select('*')->get();

        return view('rtsales.saleslist.add', [
            'teams' => $sqlTeam,
            'productGroup' => $sqlProductGroup,
            'workStatus' => $sqlWorkStatus
        ]);
    }

    public function insertSales(Request $request)
    {
        $request->validate([
            'sales_name' => 'string|required',
            'sales_level' => 'required|numeric',
            'team' => 'required|exists:ms_team_name,TeamCode',
            'team_by' => 'required',
            'product_group' => 'required',
            'product_group.*' => 'exists:ms_product_group,ProductGroupID',
            'work_status' => 'required|exists:ms_sales_work_status,SalesWorkStatusID',
            'phone_number' => 'required|digits_between:10,13|unique:ms_sales,PhoneNumber',
            // 'email' => 'required|email:rfc|unique:ms_sales,Email'
            // 'password' => 'required|string'
        ]);

        $prefixSalesCode = $request->input('prefix_sales_code');
        $team = $request->input('team');
        if ($prefixSalesCode) {
            $salesCode = $prefixSalesCode . '-' . $team;
            $maxSalesCode = DB::table('ms_sales')
                ->where('SalesCode', 'like', '%' . $salesCode . '%')
                ->max('SalesCode');
        } else {
            $salesCode = $team;
            $maxSalesCode = DB::table('ms_sales')
                ->where('SalesCode', 'like', $salesCode . '%')
                ->max('SalesCode');
        }

        if ($maxSalesCode == null) {
            $newSalesCode = $salesCode . '001';
        } else {
            $maxSalesCodeNumber = substr($maxSalesCode, -3);
            $newSalesCodeNumber = $maxSalesCodeNumber + 1;
            $newSalesCode = $salesCode . str_pad($newSalesCodeNumber, 3, '0', STR_PAD_LEFT);
        }

        $data = [
            'SalesName' => ucwords($request->input('sales_name')),
            'SalesCode' => $newSalesCode,
            'SalesLevel' => $request->input('sales_level'),
            'Team' => $request->input('team'),
            'TeamBy' => $request->input('team_by'),
            'SalesWorkStatus' => $request->input('work_status'),
            'PhoneNumber' => $request->input('phone_number'),
            // 'Email' => $request->input('email'),
            'Password' => $newSalesCode . 'bisa',
            'JoinDate' => date('Y-m-d')
        ];

        $productGroupId = $request->input('product_group');
        $productGroup = array_map(function () {
            return func_get_args();
        }, $productGroupId);
        foreach ($productGroup as $key => $value) {
            $productGroup[$key][] = $newSalesCode;
        }

        try {
            DB::transaction(function () use ($data, $productGroup) {
                DB::table('ms_sales')
                    ->insert($data);
                foreach ($productGroup as &$value) {
                    $value = array_combine(['ProductGroupID', 'SalesCode'], $value);
                    DB::table('ms_sales_product_group')
                        ->insert([
                            'SalesCode' => $value['SalesCode'],
                            'ProductGroupID' => $value['ProductGroupID']
                        ]);
                }
            });
            return redirect()->route('rtsales.saleslist')->with('success', 'Data Sales berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('rtsales.saleslist')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function editSales($salesCode)
    {
        $sqlSales = DB::table('ms_sales')
            ->where('SalesCode', '=', $salesCode)
            ->select('SalesName', 'SalesCode', 'SalesLevel', 'Team', 'TeamBy', 'Email', 'PhoneNumber', 'Password', 'SalesWorkStatus', 'IsActive')
            ->first();

        $sqlSalesProductGroup = DB::table('ms_sales_product_group')
            ->where('SalesCode', '=', $salesCode)
            ->select('*')->get();

        $sqlTeam = DB::table('ms_team_name')
            ->select('*')->get();

        $sqlProductGroup = DB::table('ms_product_group')
            ->select('*')->get();

        $sqlWorkStatus = DB::table('ms_sales_work_status')
            ->select('*')->get();

        return view('rtsales.saleslist.edit', [
            'salesCode' => $salesCode,
            'sales' => $sqlSales,
            'salesProductGroup' => $sqlSalesProductGroup,
            'teams' => $sqlTeam,
            'productGroup' => $sqlProductGroup,
            'workStatus' => $sqlWorkStatus
        ]);
    }

    public function updateSales(Request $request, $salesCode)
    {

        $request->validate([
            'sales_name' => 'string',
            'sales_level' => 'numeric',
            'team' => 'exists:ms_team_name,TeamCode',
            'product_group.*' => 'exists:ms_product_group,ProductGroupID',
            'work_status' => 'exists:ms_sales_work_status,SalesWorkStatusID',
            'phone_number' => 'digits_between:10,13',
            'email' => 'email:rfc',
            'password' => 'string',
        ]);

        $data = [
            'SalesName' => $request->input('sales_name'),
            'SalesLevel' => $request->input('sales_level'),
            'Team' => $request->input('team'),
            'TeamBy' => $request->input('team_by'),
            'SalesWorkStatus' => $request->input('work_status'),
            'PhoneNumber' => $request->input('phone_number'),
            'Email' => $request->input('email'),
            'Password' => $request->input('password'),
            'IsActive' => $request->input('is_active')
        ];

        if ($request->is_active == 0) {
            $data['ResignDate'] = date('Y-m-d');
        } else {
            $data['ResignDate'] = NULL;
            $data['JoinDate'] = date('Y-m-d');
        }

        $productGroupId = $request->input('product_group');
        $productGroup = array_map(function () {
            return func_get_args();
        }, $productGroupId);
        foreach ($productGroup as $key => $value) {
            $productGroup[$key][] = $salesCode;
        }

        try {
            DB::transaction(function () use ($salesCode, $data, $productGroup) {
                DB::table('ms_sales')
                    ->where('SalesCode', '=', $salesCode)
                    ->update($data);
                DB::table('ms_sales_product_group')
                    ->where('SalesCode', '=', $salesCode)
                    ->delete();
                foreach ($productGroup as &$value) {
                    $value = array_combine(['ProductGroupID', 'SalesCode'], $value);
                    DB::table('ms_sales_product_group')
                        ->insert([
                            'SalesCode' => $value['SalesCode'],
                            'ProductGroupID' => $value['ProductGroupID']
                        ]);
                }
            });
            return redirect()->route('rtsales.saleslist')->with('success', 'Data Sales berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('rtsales.saleslist')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function deleteSales($salesCode)
    {
        try {
            DB::transaction(function () use ($salesCode) {
                DB::table('ms_sales')
                    ->where('SalesCode', '=', $salesCode)
                    ->delete();
                DB::table('ms_sales_product_group')
                    ->where('SalesCode', '=', $salesCode)
                    ->delete();
            });
            return redirect()->route('rtsales.saleslist')->with('success', 'Data Sales berhasil dihapus');
        } catch (\Throwable $th) {
            return redirect()->route('rtsales.saleslist')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function summary()
    {
        return view('rtsales.summary.index');
    }

    public function callReport()
    {
        return view('rtsales.callReport.index');
    }

    public function getCallReport(Request $request, RTSalesService $rTSalesService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $data = $rTSalesService->callReportData($fromDate, $toDate);

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addColumn('Sales', function ($data) {
                    return $data->SalesCode . ' - ' . $data->SalesName;
                })
                ->addColumn('Tim', function ($data) {
                    return $data->TeamBy . ' ' . $data->NamaTeam;
                })
                ->editColumn('CheckIn', function ($data) {
                    if ($data->CheckIn) {
                        $date = date('d-M-Y H:i:s', strtotime($data->CheckIn));
                    } else {
                        $date = '';
                    }
                    return $date;
                })
                ->editColumn('CheckOut', function ($data) {
                    if ($data->CheckOut) {
                        $date = date('d-M-Y H:i:s', strtotime($data->CheckOut));
                    } else {
                        $date = '';
                    }
                    return $date;
                })
                ->make(true);
        }
    }

    public function surveyReport(RTSalesService $rTSalesService)
    {
        return view('rtsales.surveyReport.index');
    }

    public function getSurveyReport(Request $request, RTSalesService $rTSalesService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $filterValid = $request->input('filterValid');

        $data = $rTSalesService->surveyReportData($fromDate, $toDate, $filterValid);
        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addColumn('Empty', function ($data) {
                    return "";
                })
                ->addColumn('Checkbox', function ($data) {
                    if ($data->IsValid == 1) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }
                    $checkbox = "<input type='checkbox' " . $checked . " class='check-isvalid larger' name='check_isvalid[]' value='" . $data->VisitSurveyID . "' />";
                    return $checkbox;
                })
                ->addColumn('Sales', function ($data) {
                    return $data->SalesCode . ' - ' . $data->SalesName;
                })
                ->addColumn('Photo', function ($data) {
                    $btn = "<button data-photo='$data->SurveyPhoto' id='survey-photo' type='button' class='btn btn-sm btn-info btn-photo' data-toggle='modal' data-target='#modal-photo'>Lihat</button>";
                    return $btn;
                })
                ->rawColumns(['Checkbox', 'Photo'])
                ->make(true);
        }
    }

    public function updateIsValid($visitSurveyID, $isValid)
    {
        if ($isValid === "true") {
            $dataValid = 1;
        } else {
            $dataValid = 0;
        }


        $update = DB::table('ms_visit_survey')
            ->where('VisitSurveyID', $visitSurveyID)
            ->update([
                'IsValid' => $dataValid
            ]);

        if ($update) {
            $status = "success";
            $message = "Data berhasil diupdate";
        } else {
            $status = "failed";
            $message = "Terjadi Kesalahan";
        }


        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function storeList()
    {
        return view('rtsales.storeList.index');
    }

    public function getStoreList(Request $request, RTSalesService $rTSalesService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');
        $distributorID = $request->input('distributorID');
        $depoUser = Auth::user()->Depo;

        $sqlStoreList = $rTSalesService->storeLists();

        if ($fromDate != '' && $toDate != '') {
            $sqlStoreList->whereDate('ms_store.CreatedDate', '>=', $fromDate)
                ->whereDate('ms_store.CreatedDate', '<=', $toDate);
        }

        if ($depoUser != "ALL" && $depoUser != "REG1" && $depoUser != "REG2") {
            $sqlStoreList->where('ms_distributor.Depo', $depoUser);
        }
        if ($depoUser == "REG1") {
            $sqlStoreList->whereIn('ms_distributor.Depo', ['SMG', 'YYK']);
        }
        if ($depoUser == "REG2") {
            $sqlStoreList->whereIn('ms_distributor.Depo', ['CRS', 'CKG', 'BDG']);
        }

        if ($distributorID != '') {
            $sqlStoreList->where('ms_distributor.DistributorID', $distributorID);
        }

        $data = $sqlStoreList;

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    $date = date('d-M-Y H:i', strtotime($data->CreatedDate));
                    return $date;
                })
                ->editColumn('DistributorName', function ($data) {
                    if ($data->DistributorName != null) {
                        $distributor = $data->DistributorName;
                    } else {
                        $distributor = $data->TeamBy . ' ' . $data->TeamName;
                    }
                    return $distributor;
                })
                ->addColumn('Action', function ($data) {
                    $edit = '<a class="btn btn-xs btn-warning mb-1" href="/rtsales/store/edit/' . $data->StoreID . '">Ubah</a>';
                    $delete = '<a class="btn btn-xs btn-danger btn-delete mb-1" data-store-id="' . $data->StoreID . '" data-store-name="' . $data->StoreName . '">Hapus</a>';
                    return $edit . $delete;
                })
                ->rawColumns(['Action'])
                ->make(true);
        }
    }

    public function createStore()
    {
        $merchants = DB::table('ms_merchant_account')
            ->leftJoin('ms_store', function ($join) {
                $join->on('ms_store.MerchantID', 'ms_merchant_account.MerchantID');
                $join->where('ms_store.IsActive', 1);
            })
            ->where('ms_merchant_account.IsTesting', 0)
            ->whereNull('ms_store.StoreID')
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.PhoneNumber')
            ->get();

        $sales = DB::table('ms_sales')
            ->where('IsActive', 1)
            ->select('SalesCode', 'SalesName')->get();

        return view('rtsales.storeList.create', [
            'merchants' => $merchants,
            'sales' => $sales
        ]);
    }

    public function storeStore(Request $request, RTSalesService $rTSalesService)
    {
        $request->validate([
            'store_name' => 'required',
            'owner_name' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'merchant' => 'required|exists:ms_merchant_account,MerchantID',
            'sales' => 'required|exists:ms_sales,SalesCode',
            'grade' => 'required|in:RETAIL,SO,WS',
            'store_type' => 'required|in:NEW,EXISTING',
        ]);

        $newStoreID = $rTSalesService->generateStoreID();

        $data = [
            'StoreID' => $newStoreID,
            'SalesCode' => $request->input('sales'),
            'StoreName' => $request->input('store_name'),
            'OwnerName' => $request->input('owner_name'),
            'PhoneNumber' => $request->input('phone_number'),
            'StoreAddress' => $request->input('address'),
            'Latitude' => $request->input('latitude'),
            'Longitude' => $request->input('longitude'),
            'Grade' => $request->input('grade'),
            'MerchantID' => $request->input('merchant'),
            'StoreType' => $request->input('store_type'),
            'CreatedDate' => date('Y-m-d H:i:s')
        ];

        $insert = DB::table('ms_store')->insert($data);
        if ($insert) {
            return redirect()->route('rtsales.storeList')->with('success', 'Data Store berhasil ditambahkan');
        } else {
            return redirect()->route('rtsales.storeList')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function editStore($storeID)
    {
        $store = DB::table('ms_store')
            ->where('StoreID', $storeID)
            ->select('ms_store.StoreName', 'ms_store.OwnerName', 'ms_store.PhoneNumber', 'ms_store.StoreAddress', 'ms_store.Grade', 'ms_store.MerchantID', 'ms_store.CreatedDate', 'ms_store.StoreType', 'ms_store.SalesCode', 'ms_store.Latitude', 'ms_store.Longitude', 'ms_store.Grade', 'ms_store.StoreType')
            ->first();

        $merchants = DB::table('ms_merchant_account')
            ->leftJoin('ms_store', function ($join) {
                $join->on('ms_store.MerchantID', 'ms_merchant_account.MerchantID');
                $join->where('ms_store.IsActive', 1);
            })
            ->where('ms_merchant_account.IsTesting', 0)
            ->whereNull('ms_store.StoreID')
            ->orWhere('ms_store.StoreID', $storeID)
            ->select('ms_merchant_account.MerchantID', 'ms_merchant_account.StoreName', 'ms_merchant_account.PhoneNumber')
            ->get();

        $sales = DB::table('ms_sales')
            ->where('IsActive', 1)
            ->select('SalesCode', 'SalesName')->get();

        return view('rtsales.storeList.edit', [
            'storeID' => $storeID,
            'store' => $store,
            'merchants' => $merchants,
            'sales' => $sales
        ]);
    }

    public function updateStore($storeID, Request $request)
    {
        $request->validate([
            'store_name' => 'required',
            'owner_name' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'merchant' => 'nullable|exists:ms_merchant_account,MerchantID',
            'sales' => 'required|exists:ms_sales,SalesCode',
            'grade' => 'required|in:RETAIL,SO,WS',
            'store_type' => 'required|in:NEW,EXISTING',
        ]);

        $data = [
            'SalesCode' => $request->input('sales'),
            'StoreName' => $request->input('store_name'),
            'OwnerName' => $request->input('owner_name'),
            'PhoneNumber' => $request->input('phone_number'),
            'StoreAddress' => $request->input('address'),
            'Latitude' => $request->input('latitude'),
            'Longitude' => $request->input('longitude'),
            'Grade' => $request->input('grade'),
            'MerchantID' => $request->input('merchant'),
            'StoreType' => $request->input('store_type')
        ];

        try {
            DB::transaction(function () use ($storeID, $data, $request) {
                DB::table('ms_store')
                    ->where('StoreID', $storeID)
                    ->update($data);
                DB::table('ms_merchant_assessment')
                    ->where('StoreID', $storeID)
                    ->where('IsActive', 1)
                    ->update([
                        'MerchantID' => $request->input('merchant')
                    ]);
            });
            return redirect()->route('rtsales.storeList')->with('success', 'Data Store berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->route('rtsales.storeList')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function deleteStore($storeID)
    {
        $delete = DB::table('ms_store')
            ->where('StoreID', $storeID)
            ->update([
                'IsActive' => 0
            ]);
        if ($delete) {
            return redirect()->route('rtsales.storeList')->with('success', 'Data Store berhasil dihapus');
        } else {
            return redirect()->route('rtsales.storeList')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function callplan(Request $request, RTSalesService $rTSalesService)
    {
        $visitDayName = $request->input('visitDayName');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        
        $data = $rTSalesService->callPlanData($visitDayName, $startDate, $endDate);
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addColumn('Sales', function ($data) {
                    return $data->SalesCode . ' - ' . $data->SalesName;
                })
                ->filterColumn('Sales', function ($query, $keyword) {
                    $sql = "CONCAT(ms_visit_plan.SalesCode, ' ', ms_sales.SalesName) like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->make();
        }
    }

    public function callplanIndex()
    {
        return view('rtsales.callPlan.index');
    }
}
