<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MonthlyReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate != null && $endDate != null) {
            $filterDate = "WHERE b.Periode BETWEEN '$startDate-01' AND '$endDate-01'";
        } else {
            $filterDate = "";
        }

        $data = DB::select("select
                            a.AreaName,
                            DATE_FORMAT(b.Periode, '%b-%y') AS Periode,
                            coalesce(c.Sales,'-') 'Sales',
                            coalesce(c.Cogs,'-') 'Cogs',
                            coalesce(c.GPMargin,'-') 'GPMargin',
                            coalesce(c.GPRatio,'-') 'GPRatio',
                            coalesce(c.EndingInventory,'-') 'EndingInventory',
                            coalesce(c.InventoryRatio,'-') 'InventoryRatio'
                        from
                        (select distinct AreaName from ms_monthly_report) a
                        cross join 
                        (select distinct Periode from ms_monthly_report) b
                        left join ms_monthly_report c
                        on a.AreaName = c.AreaName
                        and b.Periode = c.Periode
                        $filterDate
                        order by b.Periode, a.AreaName");

        return view('monthly-report.index', [
            'data' => collect($data)
        ]);
    }

    public function setting()
    {
        return view('setting.monthly-report.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'area' => 'required',
            'periode' => 'required',
            'sales' => 'required',
            'cogs' => 'required',
            'gp_margin' => 'required',
            'gp_ratio' => 'required',
            'ending_inventory' => 'required',
            'inventory_ratio' => 'required'
        ]);

        $data = [
            'AreaName' => $request->input('area'),
            'Periode' => $request->input('periode') . "-01",
            'Sales' => $request->input('sales'),
            'Cogs' => $request->input('cogs'),
            'GPMargin' => $request->input('gp_margin'),
            'GPRatio' => $request->input('gp_ratio'),
            'EndingInventory' => $request->input('ending_inventory'),
            'InventoryRatio' => $request->input('inventory_ratio'),
        ];

        $store = DB::table('ms_monthly_report')->insert($data);
        if ($store) {
            return redirect()->route('monthlyReport')->with('success', 'Report berhasil ditambahkan');
        } else {
            return redirect()->route('monthlyReport')->with('failed', 'Terjadi kesalahan');
        }
    }

    public function getOneData(Request $request)
    {
        $sql = DB::table('ms_monthly_report')
            ->where('AreaName', 'like', '%' . $request->area . '%')
            ->where('Periode', 'like', '%' . $request->periode . '%')
            ->select('*')->first();

        return $sql;
    }

    public function update(Request $request)
    {
        $request->validate([
            'area' => 'required',
            'periode' => 'required',
            'sales' => 'required',
            'cogs' => 'required',
            'gp_margin' => 'required',
            'gp_ratio' => 'required',
            'ending_inventory' => 'required',
            'inventory_ratio' => 'required'
        ]);

        $data = [
            'Sales' => $request->input('sales'),
            'Cogs' => $request->input('cogs'),
            'GPMargin' => $request->input('gp_margin'),
            'GPRatio' => $request->input('gp_ratio'),
            'EndingInventory' => $request->input('ending_inventory'),
            'InventoryRatio' => $request->input('inventory_ratio'),
        ];

        $update = DB::table('ms_monthly_report')
            ->where('AreaName', $request->input('area'))
            ->where('Periode', $request->input('periode') . "-01")
            ->update($data);

        if ($update) {
            return redirect()->route('monthlyReport')->with('success', 'Report berhasil diubah');
        } else {
            return redirect()->route('monthlyReport')->with('failed', 'Terjadi kesalahan');
        }
    }

    public function delete($area, $periode)
    {
        $delete = DB::table('ms_monthly_report')
            ->where('AreaName', $area)
            ->where('Periode', $periode . "-01")
            ->delete();

        if ($delete) {
            return redirect()->route('monthlyReport')->with('success', 'Data report berhasil dihapus');
        } else {
            return redirect()->route('monthlyReport')->with('failed', 'Terjadi kesalahan');
        }
    }
}