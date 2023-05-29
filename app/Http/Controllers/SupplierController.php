<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function account()
    {
        return view('supplier.account.index');
    }

    public function getAllAccounts(Request $request)
    {
        $data = DB::table('ms_suppliers')->select('SupplierID', 'SupplierName');

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
}
