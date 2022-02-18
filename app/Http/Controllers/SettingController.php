<?php

namespace App\Http\Controllers;

use App\Services\HaistarService;
use App\Services\MerchantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends Controller
{
    public function fairbanc(MerchantService $merchantService)
    {
        return view('setting.fairbanc.index', [
            'merchant' => $merchantService->merchantNotFairbanc()->get()
        ]);
    }

    public function getFairbanc(MerchantService $merchantService, Request $request)
    {
        $data = $merchantService->merchantFairbanc();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('Partner', function ($data) {
                    if ($data->Partner != null) {
                        $partner = '<a class="badge badge-info">'.$data->Partner.'</a>';
                    } else {
                        $partner = '';
                    }
                    return $partner;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="#" class="btn-sm btn-danger delete-merchant-fairbanc" data-merchant-name="'.$data->StoreName.'" data-merchant-id="'.$data->MerchantID.'">Hapus</a>';
                    return $actionBtn;
                })
                ->rawColumns(['Partner', 'Action'])
                ->make(true);
        }
    }

    public function insertFairbanc(MerchantService $merchantService, Request $request)
    {
        $request->validate([
            'merchant_fairbanc' => 'required',
            'merchant_fairbanc.*' => 'exists:ms_merchant_account,MerchantID'
        ]);

        $arrMerchantID = $request->input('merchant_fairbanc');

        $data = $merchantService->dataMerchantFairbanc($arrMerchantID);
        
        try {
            $merchantService->insertBulkMerchantFairbanc($data);
            return redirect()->route('setting.fairbanc')->with('success', 'Data Merchant Fairbanc berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('setting.fairbanc')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function deleteFairbanc($merchantID, MerchantService $merchantService)
    {
        $deleteFairbanc = $merchantService->deleteMerchantFairbanc($merchantID);
        if ($deleteFairbanc) {
            return redirect()->route('setting.fairbanc')->with('success', 'Data Merchant Fairbanc berhasil dihapus');
        } else {
            return redirect()->route('setting.fairbanc')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function haistar(HaistarService $haistarService)
    {
        return view('setting.haistar.index', [
            'distributor' => $haistarService->distributorNotHaistar()->get()
        ]);
    }

    public function getHaistar(HaistarService $haistarService, Request $request)
    {
        $data = $haistarService->distributorHaistar();

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d M Y H:i', strtotime($data->CreatedDate));
                })
                ->editColumn('IsHaistar', function ($data) {
                    if ($data->IsHaistar == 1) {
                        $badge = '<span class="badge badge-info">HAISTAR</badge>';
                    } else {
                        $badge = '';
                    }
                    return $badge;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="#" class="btn-sm btn-danger delete-distributor-haistar" data-distributor-name="'.$data->DistributorName.'" data-distributor-id="'.$data->DistributorID.'">Hapus</a>';
                    return $actionBtn;
                })
                ->rawColumns(['CreatedDate', 'Action', 'IsHaistar'])
                ->make(true);
        }
    }

    public function insertHaistar(HaistarService $haistarService, Request $request)
    {
        $request->validate([
            'distributor_haistar' => 'required',
            'distributor_haistar.*' => 'exists:ms_distributor,DistributorID'
        ]);

        $arrDistributorID = $request->input('distributor_haistar');

        $data = $haistarService->dataDistributorHaistar($arrDistributorID);
        
        try {
            $haistarService->insertBulkDistributorHaistar($data);
            return redirect()->route('setting.haistar')->with('success', 'Data Distributor Haistar berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->route('setting.haistar')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function deleteHaistar($distributorID, HaistarService $haistarService)
    {
        $deleteHaistar = $haistarService->deleteDistributorHaistar($distributorID);
        if ($deleteHaistar) {
            return redirect()->route('setting.haistar')->with('success', 'Data Distributor Haistar berhasil dihapus');
        } else {
            return redirect()->route('setting.haistar')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }
}
