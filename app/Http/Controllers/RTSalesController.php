<?php

namespace App\Http\Controllers;

use App\Services\RTSalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Yajra\DataTables\Facades\DataTables;

use function Ramsey\Uuid\v1;

class RTSalesController extends Controller
{
    public function saleslist()
    {
        return view('rtsales.saleslist.index');
    }

    public function getDataSales(Request $request)
    {
        $data = DB::table('ms_sales')
            ->select('SalesName', 'SalesCode', 'SalesLevel', 'Team', 'Email', 'PhoneNumber', 'Password');

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('Action', function ($data) {
                    $btn = '<a class="badge badge-warning" href="/rtsales/saleslist/edit/' . $data->SalesCode . '">Ubah</a> | 
                            <a class="badge badge-danger delete-sales" href="#" data-sales-name="' . $data->SalesName . '" data-sales-code="' . $data->SalesCode . '">Hapus</a>';
                    return $btn;
                })
                ->rawColumns(['Action'])
                ->make(true);
        }
    }

    public function addSales()
    {
        $sqlDepoTeam = DB::table('ms_distributor')
            ->whereNotNull('Depo')
            ->select('Depo')->get();

        $sqlProductGroup = DB::table('ms_product_group')
            ->select('*')->get();

        $sqlWorkStatus = DB::table('ms_sales_work_status')
            ->select('*')->get();

        return view('rtsales.saleslist.add', [
            'depoTeam' => $sqlDepoTeam,
            'productGroup' => $sqlProductGroup,
            'workStatus' => $sqlWorkStatus
        ]);
    }

    public function insertSales(Request $request)
    {
        $request->validate([
            'sales_name' => 'string|required',
            'sales_level' => 'required|numeric',
            'team' => 'required|exists:ms_distributor,Depo',
            'product_group' => 'required',
            'product_group.*' => 'exists:ms_product_group,ProductGroupID',
            'work_status' => 'required|exists:ms_sales_work_status,SalesWorkStatusID',
            'phone_number' => 'required|digits_between:10,13',
            'email' => 'required|email:rfc',
            'password' => 'required|string'
        ]);

        $team = $request->input('team');
        $maxSalesCode = DB::table('ms_sales')
            ->where('SalesCode', 'like', '%' . $team . '%')
            ->max('SalesCode');

        if ($maxSalesCode == null) {
            $newSalesCode = $team . '001';
        } else {
            $maxSalesCodeNumber = substr($maxSalesCode, 3);
            $newSalesCodeNumber = $maxSalesCodeNumber + 1;
            $newSalesCode = $team . str_pad($newSalesCodeNumber, 3, '0', STR_PAD_LEFT);
        }

        $data = [
            'SalesName' => $request->input('sales_name'),
            'SalesCode' => $newSalesCode,
            'SalesLevel' => $request->input('sales_level'),
            'Team' => $request->input('team'),
            'SalesWorkStatus' => $request->input('work_status'),
            'PhoneNumber' => $request->input('phone_number'),
            'Email' => $request->input('email'),
            'Password' => $request->input('password')
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
            ->select('SalesName', 'SalesCode', 'SalesLevel', 'Team', 'Email', 'PhoneNumber', 'Password', 'SalesWorkStatus')
            ->first();

        $sqlSalesProductGroup = DB::table('ms_sales_product_group')
            ->where('SalesCode', '=', $salesCode)
            ->select('*')->get();

        $sqlDepoTeam = DB::table('ms_distributor')
            ->whereNotNull('Depo')
            ->select('Depo')->get();

        $sqlProductGroup = DB::table('ms_product_group')
            ->select('*')->get();

        $sqlWorkStatus = DB::table('ms_sales_work_status')
            ->select('*')->get();

        return view('rtsales.saleslist.edit', [
            'salesCode' => $salesCode,
            'sales' => $sqlSales,
            'salesProductGroup' => $sqlSalesProductGroup,
            'depoTeam' => $sqlDepoTeam,
            'productGroup' => $sqlProductGroup,
            'workStatus' => $sqlWorkStatus
        ]);
    }

    public function updateSales(Request $request, $salesCode)
    {
        $request->validate([
            'sales_name' => 'string|required',
            'sales_level' => 'required|numeric',
            'team' => 'required|exists:ms_distributor,Depo',
            'product_group' => 'required',
            'product_group.*' => 'exists:ms_product_group,ProductGroupID',
            'work_status' => 'required|exists:ms_sales_work_status,SalesWorkStatusID',
            'phone_number' => 'required|digits_between:10,13',
            'email' => 'required|email:rfc',
            'password' => 'required|string'
        ]);

        $data = [
            'SalesName' => $request->input('sales_name'),
            'SalesLevel' => $request->input('sales_level'),
            'Team' => $request->input('team'),
            'SalesWorkStatus' => $request->input('work_status'),
            'PhoneNumber' => $request->input('phone_number'),
            'Email' => $request->input('email'),
            'Password' => $request->input('password')
        ];
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

        $data = $rTSalesService->callReportData($fromDate, $toDate)->get();

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
        $data = $rTSalesService->surveyReportData("2022-02-26", "2022-03-04");
        // foreach ($data as $key => $value) {
        // dd($data);
        // }
        return view('rtsales.surveyReport.index');
    }

    public function getSurveyReport(Request $request, RTSalesService $rTSalesService)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $data = $rTSalesService->surveyReportData($fromDate, $toDate);

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addColumn('Sales', function ($data) {
                    return $data->SalesCode . ' - ' . $data->SalesName;
                })
                ->addColumn('Photo', function ($data) {
                    $btn = "<button data-photo='$data->SurveyPhoto' id='survey-photo' type='button' class='btn btn-sm btn-info btn-photo' data-toggle='modal' data-target='#modal-photo'>Lihat</button>";
                    return $btn;
                })
                ->rawColumns(['Photo'])
                ->make(true);
        }
    }
}