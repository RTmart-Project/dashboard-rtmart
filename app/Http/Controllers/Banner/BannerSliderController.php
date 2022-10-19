<?php

namespace App\Http\Controllers\Banner;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Services\BannerService\BannerSliderService;

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
                    $btn = '<a href="/banner/slider/edit/' . $data->PromoID . '" class="btn btn-xs btn-warning">Ubah</a>
                            <a class="btn btn-xs btn-danger delete-promo" href="#" data-id="' . $data->ID . '">Hapus</a>';
                    return $btn;
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
            'title' => 'required|min:5',
            'start_date' => 'required|before_or_equal:end_date',
            'end_date' => 'required|after_or_equal:start_date',
            'target' => 'required|string|exists:ms_promo,PromoTarget',
            'banner_image' => 'required|image',
            'description' => 'required|string',
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

    public function edit($promoId)
    {
        $sql = DB::table('ms_promo')
            ->select('PromoID', 'PromoTitle', 'PromoDesc', 'PromoImage', 'PromoStartDate', 'PromoEndDate', 'PromoStatus', 'PromoTarget', 'PromoExpiryDate', 'ClassActivityPage', 'ActivityButtonText')
            ->where('PromoID', $promoId)->first();

        $targetID = DB::table('ms_promo')
            ->where('PromoID', $promoId)
            ->select('TargetID')->get();

        $listTargetID = $this->bannerSliderService->listTargetIDBannerSlider($sql->PromoTarget);

        $promoTarget = DB::table('ms_promo')->selectRaw('DISTINCT(PromoTarget)')->get()->toArray();
        $promoStatus = DB::table('ms_promo')->select('PromoStatus')->distinct()->get()->toArray();

        return view('banner.banner-slider.edit', [
            'targets' => $sql, 
            'targetID' => $targetID,
            'listTargetID' => $listTargetID,
            'promoTarget' => $promoTarget, 
            'promoStatus' => $promoStatus
        ]);
    }

    public function update(Request $request, $promoId)
    {
        $request->validate([
            'title' => 'required|min:5',
            'start_date' => 'required|before_or_equal:end_date',
            'end_date' => 'required|after_or_equal:start_date',
            'target' => 'required|string|exists:ms_promo,PromoTarget',
            'promostatus' => 'between:0,1',
            'banner_image' => 'image',
            'description' => 'required|string',
        ]);

        $target = $request->input('target');

        $res = [
            'PromoID' => $promoId,
            'PromoTitle' => $request->input('title'),
            'PromoDesc' => $request->input('description'),
            'PromoTarget' => $request->input('target'),
            'PromoStatus' => intval($request->input('promo_status')),
            'PromoStartDate' => $request->input('start_date'),
            'PromoEndDate' => $request->input('end_date'),
            'PromoExpiryDate' => $request->input('end_date'),
            'ClassActivityPage' => $request->input('activity_button_page'),
            'ActivityButtonText' => $request->input('activity_button_text'),
            'TargetID' => $request->input('target_id'),
        ];

        if ($request->hasFile('banner_image')) {
            $promoImage = $promoId . '.' . $request->file('banner_image')->extension();
            $request->file('banner_image')->move($this->saveImageUrl . 'promo/', $promoImage);
            $res['PromoImage'] = $promoImage;
        } else {
            $temp = DB::table('ms_promo')->where('PromoID', $promoId)->select('PromoImage')->first();
            $res['PromoImage'] = $temp->PromoImage;
        }

        if ($request->target === "MERCHANT_GLOBAL" || $request->target === "CUSTOMER_GLOBAL") {
            $data = $res;
        } else {
            $data = [];
            $targets = array_map(function () {
                return func_get_args();
            }, $res['TargetID']);
            foreach ($targets as $key => $value) {
                $value = array_combine(['TargetID'], $value);
                $value += ['PromoID' => $promoId];
                $value += ['PromoTitle' => $res['PromoTitle']];
                $value += ['PromoDesc' => $res['PromoDesc']];
                $value += ['PromoImage' => $res['PromoImage']];
                $value += ['PromoStartDate' => $res['PromoStartDate']];
                $value += ['PromoEndDate' => $res['PromoEndDate']];
                $value += ['PromoStatus' => $res['PromoStatus']];
                $value += ['PromoTarget' => $res['PromoTarget']];
                $value += ['PromoEndDate' => $res['PromoEndDate']];
                $value += ['PromoExpiryDate' => $res['PromoEndDate']];
                $value += ['ClassActivityPage' => $res['ClassActivityPage']];
                $value += ['ActivityButtonText' => $res['ActivityButtonText']];
                array_push($data, $value);
            }
        }

        try {
            DB::transaction(function () use ($data, $target, $promoId) {
                DB::table('ms_promo')->where('PromoID', $promoId)->delete();
                if ($target === "MERCHANT_GLOBAL" || $target === "CUSTOMER_GLOBAL") {
                    DB::table('ms_promo')->insert($data);
                } else {
                    DB::table('ms_promo')->insert($data);
                }
            });
            return redirect()->route('banner.slider')->with(['success' => 'Data Banner Slider berhasil diubah']);
        } catch (Exception $e) {
            return redirect()->route('banner.slider')->with(['failed', 'Gagal, terjadi kesalahan sistem atau jaringan']);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                DB::table('ms_promo')->where('ID', $id)->delete();
            });
            return redirect()->route('banner.slider')->with('success', 'Data Banner berhasil dihapus');
        } catch (\Throwable $th) {
            return redirect()->route('banner.slider')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }
}
