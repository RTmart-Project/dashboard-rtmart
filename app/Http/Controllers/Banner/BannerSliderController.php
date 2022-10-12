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

        $sql = $this->bannerSliderService->dataBannerSlider($startDate, $endDate, $filterStatus, $filterBy);

        $data = $sql;

        if ($request->ajax()) {
            return DataTables::of($data)
                ->editColumn('PromoImage', function ($data) {
                    $baseImageUrl = config('app.base_image_url');
                    $image = '<img src="' . $baseImageUrl . 'promo/' . $data->PromoImage . '" width="180" />';
                    return $image;
                })
                ->editColumn('PromoStartDate', function ($data) {
                    return date('d F Y', strtotime($data->PromoStartDate));
                })
                ->editColumn('PromoExpiryDate', function ($data) {
                    return date('d F Y', strtotime($data->PromoExpiryDate));
                })
                ->editColumn('PromoStatus', function ($data) {
                    if ($data->PromoStatus === 1) {
                        $status = '<span class="badge badge-success">Aktif</span>';
                    } else {
                        $status = '<span class="badge badge-danger">Tidak Aktif</span>';
                    }
                    return $status;
                })
                ->addColumn('Action', function ($data) {
                    $btnEdit = '<button class="btn btn-sm btn-warning">Edit</button>';
                    return $btnEdit;
                })
                ->rawColumns(['PromoImage', 'PromoStatus', 'Action'])
                ->make();
        }
    }

    public function create()
    {
        return view('banner.banner-slider.create');
    }
}
