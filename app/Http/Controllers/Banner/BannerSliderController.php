<?php

namespace App\Http\Controllers\Banner;

use App\Http\Controllers\Controller;
use App\Services\BannerService\BannerSliderService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BannerSliderController extends Controller
{
    protected $bannerSliderService;
    public function __construct(BannerSliderService $bannerSliderService)
    {
        $this->bannerSliderService = $bannerSliderService;
    }

    public function index()
    {
        return view('banner.banner-slider.index');
    }

    public function data(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $filterStatus = $request->input('filterStatus');
        $filterBy = $request->input('filterBy');

        $sql = $this->bannerSliderService->dataBannerSlider();

        $data = $sql;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->make();
        }
    }
}
