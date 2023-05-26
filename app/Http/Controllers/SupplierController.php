<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    protected $baseImageUrl;

    public function __construct()
    {
        $this->baseImageUrl = config('app.base_image_url');
    }

    public function account()
    {
        return view('supplier.account.index');
    }

    public function getAllAccounts(Request $request)
    {
        $sqlAllAccount = DB::table('ms_suppliers')->select('SupplierID', 'SupplierName');

        $data = $sqlAllAccount;

        if ($request->ajax()) {
            return Datatables::of($data)->make(true);
        }
    }

    public function addSupplier()
    {
        return view('supplier.account.create');
    }

    public function insertSupplier(Request $request)
    {
        $request->validate([
            'suppliername' => 'required|string',
        ]);

        try {
            DB::table('ms_suppliers')->insert([
                'SupplierName' => $request->suppliername
            ]);

            return redirect()->route('supplier.account')->with('success', 'Data Supplier berhasil ditambahkan');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function editAccount($distributorId)
    {
        $distributorById = DB::table('ms_distributor')
            ->where('DistributorID', $distributorId)
            ->select('DistributorID', 'DistributorName', 'Email', 'Address', 'IsActive')
            ->first();

        return view('distributor.account.edit', [
            'distributorById' => $distributorById
        ]);
    }

    public function updateAccount(Request $request, $distributorId)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('ms_distributor', 'Email')->ignore($distributorId, 'DistributorID')
            ],
            'address' => 'max:500'
        ]);

        $data = [
            'DistributorName' => $request->input('name'),
            'Email' => $request->input('email'),
            'Address' => $request->input('address'),
            'IsActive' => $request->input('status')
        ];

        $updateDistributor = DB::table('ms_distributor')
            ->where('DistributorID', $distributorId)
            ->update($data);

        if ($updateDistributor) {
            return redirect()->route('distributor.account')->with('success', 'Data distributor telah diubah');
        } else {
            return redirect()->route('distributor.account')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }
}
