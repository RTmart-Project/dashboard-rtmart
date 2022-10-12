<?php

namespace App\Http\Controllers\Banner;

use App\Http\Controllers\Controller;
use App\Services\BannerService\BannerSliderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BannerSliderController extends Controller
{
    protected $bannerSliderService, $saveImageUrl, $baseImageUrl;
    public function __construct(BannerSliderService $bannerSliderService)
    {
        $this->bannerSliderService = $bannerSliderService;
        $this->saveImageUrl = config('app.save_image_url');
        $this->baseImageUrl = config('app.base_image_url');
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
        $targets = $this->bannerSliderService->targetBannerSlider();
        return view('banner.banner-slider.create', [
            'targets' => $targets
        ]);
    }

    public function listTargetID($target)
    {
        $data = $this->bannerSliderService->listTargetIDBannerSlider($target);
        return $data;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'target' => 'required',
            'banner_image' => 'required',
            'description' => 'required'
        ]);

        $promoID = $this->bannerSliderService->generatePromoID();
        $target = $request->input('target');
        $title = $request->input('title');
        $description = $request->input('description');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $activityButtonPage = $request->input('activity_button_page');
        $activityButtonText = $request->input('activity_button_text');
        $targetID = $request->input('target_id');

        $promoImage = $promoID . '.' . $request->file('banner_image')->extension();
        $request->file('banner_image')->move($this->saveImageUrl . 'promo/', $promoImage);

        if ($target === "MERCHANT_GLOBAL" || $target === "CUSTOMER_GLOBAL") {
            $dataPromo = [
                'PromoID' => $promoID,
                'PromoTitle' => $title,
                'PromoDesc' => $description,
                'PromoImage' => $promoImage,
                'PromoStartDate' => $startDate,
                'PromoEndDate' => $endDate,
                'PromoStatus' => 1,
                'PromoTarget' => $target,
                'PromoExpiryDate' => $endDate,
                'ClassActivityPage' => $activityButtonPage,
                'ActivityButtonText' => $activityButtonText
            ];
        } else {
            $dataPromo = [];
            $targets = array_map(function () {
                return func_get_args();
            }, $targetID);
            foreach ($targets as $key => $value) {
                $value = array_combine(['TargetID'], $value);
                $value += ['PromoID' => $promoID];
                $value += ['PromoTitle' => $title];
                $value += ['PromoDesc' => $description];
                $value += ['PromoImage' => $promoImage];
                $value += ['PromoStartDate' => $startDate];
                $value += ['PromoEndDate' => $endDate];
                $value += ['PromoStatus' => 1];
                $value += ['PromoTarget' => $target];
                $value += ['PromoExpiryDate' => $endDate];
                $value += ['ClassActivityPage' => $activityButtonPage];
                $value += ['ActivityButtonText' => $activityButtonText];
                array_push($dataPromo, $value);
            }
        }

        $insert = DB::table('ms_promo')->insert($dataPromo);
        if ($insert) {
            return redirect()->route('banner.slider')->with('success', 'Data Banner Slider berhasil ditambahkan');
        } else {
            return redirect()->route('banner.slider')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }
}
