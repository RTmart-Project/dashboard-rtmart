<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PpobController extends Controller
{
    public function topup()
    {

        // Balance MobilePulsa
        $username   = "081906609707";
        $apiKey   = "86660bd858549536";
        $ref_id     = "your ref id";
        $signature  = md5($username . $apiKey . 'cs');

        $json = '{
            "commands" : "balance",
            "username" : "081906609707",
            "sign"     : "86097bc8389ae7b9c362716d699a475f"
        }';

        $url = "https://testprepaid.mobilepulsa.net/v1/legacy/index";

        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);
        curl_close($ch);
        $arrayRes = json_decode($data, true);
        $balanceMobilePulsa = $arrayRes['data']['balance'];

        // Get Sum Total Saldo Merchant
        $sumMerchantSaldo = DB::table('ms_merchant_account')
            ->where('ms_merchant_account.ActivatedPPOB', '=', 1)
            ->sum('ms_merchant_account.SaldoPPOB');

        // Margin Deposit
        $marginDeposit = $arrayRes['data']['balance'] - $sumMerchantSaldo;

        // Transaction Count Topup for Bubble Notif
        $topupTransaction = DB::table('tx_topup_saldo_ppob')
            ->select('TxTopupSaldoPpobID', 'Status')
            ->get();

        $statusMenungguPembayaran = 'TPS-001';
        $statusMenungguValidasi = 'TPS-002';
        $statusSelesai = 'TPS-003';
        $statusGagalValidasi = 'TPS-004';

        $countSemuaData = $topupTransaction->count();
        $countMenungguPembayaran = $topupTransaction->where('Status', '=', $statusMenungguPembayaran)->count();
        $countMenungguValidasi = $topupTransaction->where('Status', '=', $statusMenungguValidasi)->count();
        $countSelesai = $topupTransaction->where('Status', '=', $statusSelesai)->count();
        $countGagalValidasi = $topupTransaction->where('Status', '=', $statusGagalValidasi)->count();

        return view('ppob.topup.index', [
            'sumMerchantSaldo' => $sumMerchantSaldo,
            'balanceMobilePulsa' => $balanceMobilePulsa,
            'marginDeposit' => $marginDeposit,
            'countSemuaData' => $countSemuaData,
            'countMenungguPembayaran' => $countMenungguPembayaran,
            'countMenungguValidasi' => $countMenungguValidasi,
            'countSelesai' => $countSelesai,
            'countGagalValidasi' => $countGagalValidasi,
        ]);
    }

    public function getTopupByStatus(Request $request, $topupStatus)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Get data topup with status, jika tanggal filter kosong tampilkan semua data.
        $sqlTopupWithStatus = DB::table('tx_topup_saldo_ppob')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_topup_saldo_ppob.MerchantID')
            ->join('ms_status_ppob', 'ms_status_ppob.StatusPPOBID', '=', 'tx_topup_saldo_ppob.Status')
            ->where('tx_topup_saldo_ppob.Status', '=', $topupStatus)
            ->select('tx_topup_saldo_ppob.*', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreName', 'ms_status_ppob.StatusName');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlTopupWithStatus->whereDate('tx_topup_saldo_ppob.TransactionDate', '>=', $fromDate)
                ->whereDate('tx_topup_saldo_ppob.TransactionDate', '<=', $toDate);
        }

        // Get data response
        $data = $sqlTopupWithStatus->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('TransactionDate', function ($data) {
                    return date('Y-m-d', strtotime($data->TransactionDate));
                })
                ->editColumn('TransferPhoto', function ($data) {
                    $baseImageUrl = config('app.base_image_url');
                    if ($data->TransferPhoto == null) {
                        $data->TransferPhoto = 'not-found.png';
                    }
                    return '<a data-store-name="' . $data->StoreName . '" data-topup-id="' . $data->TxTopupSaldoPpobID . '" class="lihat-bukti" target="_blank" href="' . $baseImageUrl . 'transfer_ppob_topup/' . $data->TransferPhoto . '">Lihat Bukti</a>';
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a data-jumlah-topup="' . $data->AmountTopup . '" data-store-name="' . $data->StoreName . '" data-topup-id="' . $data->TxTopupSaldoPpobID . '"  style="cursor: pointer;" class="mr-1 btn-konfirmasi btn-sm btn-success" data-toggle="modal" data-target="#konfirmasi-topup-modal">
                    Konfirmasi
                    </a> <a data-store-name="' . $data->StoreName . '" data-topup-id="' . $data->TxTopupSaldoPpobID . '" href="#" class="btn-batal btn-sm btn-danger">Batal</a>';
                    return $actionBtn;
                })
                ->rawColumns(['Action', 'TransferPhoto'])
                ->make(true);
        }
    }

    public function getTopups(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Get data topup with status, jika tanggal filter kosong tampilkan semua data.
        $sqlAllTopup = DB::table('tx_topup_saldo_ppob')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_topup_saldo_ppob.MerchantID')
            ->join('ms_status_ppob', 'ms_status_ppob.StatusPPOBID', '=', 'tx_topup_saldo_ppob.Status')
            ->select('tx_topup_saldo_ppob.*', 'ms_merchant_account.PhoneNumber', 'ms_merchant_account.StoreName', 'ms_status_ppob.StatusName');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlAllTopup->whereDate('tx_topup_saldo_ppob.TransactionDate', '>=', $fromDate)
                ->whereDate('tx_topup_saldo_ppob.TransactionDate', '<=', $toDate);
        }

        // Get data response
        $data = $sqlAllTopup->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('TransactionDate', function ($data) {
                    return date('Y-m-d', strtotime($data->TransactionDate));
                })
                ->editColumn('StatusName', function ($data) {
                    $statusMenungguPembayaran = 'TPS-001';
                    $statusMenungguValidasi = 'TPS-002';
                    $statusSelesai = 'TPS-003';
                    $statusGagalValidasi = 'TPS-004';

                    if ($data->Status == $statusMenungguPembayaran) {
                        $StatusName = '<span class="badge badge-secondary">' . $data->StatusName . '</span>';
                    } else if ($data->Status == $statusMenungguValidasi) {
                        $StatusName = '<span class="badge badge-warning">' . $data->StatusName . '</span>';
                    } else if ($data->Status == $statusSelesai) {
                        $StatusName = '<span class="badge badge-success">' . $data->StatusName . '</span>';
                    } else if ($data->Status == $statusGagalValidasi) {
                        $StatusName = '<span class="badge badge-danger">' . $data->StatusName . '</span>';
                    } else {
                        $StatusName = 'Error, status tidak ditemukan';
                    }

                    return $StatusName;
                })
                ->rawColumns(['StatusName'])
                ->make(true);
        }
    }

    public function confirmTopup(Request $request, $topupId)
    {
        $this->validate($request, [
            'jumlahTopup' => 'required|numeric|min:1',
        ]);

        $jumlahTopup = $request->input('jumlahTopup');
        $statusSelesai = 'TPS-003';
        $currentTime = date("Y-m-d H:i:s");

        $getTopupTransaction = DB::table('tx_topup_saldo_ppob')
            ->where('tx_topup_saldo_ppob.TxTopupSaldoPpobID', '=', $topupId)
            ->select('tx_topup_saldo_ppob.MerchantID')
            ->first();

        // Update Saldo Merchant
        DB::table('ms_merchant_account')
            ->where('MerchantID', $getTopupTransaction->MerchantID)
            ->update([
                'SaldoPPOB' => DB::raw('SaldoPPOB + ' . $jumlahTopup)
            ]);

        // Update Status dan AmountTopup Transaksi Topup
        DB::table('tx_topup_saldo_ppob')
            ->where('TxTopupSaldoPpobID', $topupId)
            ->update([
                'Status' => $statusSelesai,
                'AmountTopup' => $jumlahTopup
            ]);

        // Firebase Notif
        $getMerchant = DB::table('ms_merchant_account')
            ->where('ms_merchant_account.MerchantID', '=', $getTopupTransaction->MerchantID)
            ->select('ms_merchant_account.MerchantFirebaseToken')
            ->first();

        $baseImageUrl = config('app.base_image_url');

        $fields = array(
            'registration_ids' => array($getMerchant->MerchantFirebaseToken),
            'data' => array(
                "date" => $currentTime,
                "merchantID" => $getTopupTransaction->MerchantID,
                "title" => "Topup saldo PPOB berhasil!",
                "body" => "Saldo PPOB anda telah berhasil ditambahkan sebesar Rp." . $jumlahTopup,
                'large_icon' => $baseImageUrl . 'push/merchant_icon.png'
            )
        );

        $headers = array(
            'Authorization: key=' . config('app.firebase_auth_token'),
            'Content-Type: application/json'
        );

        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_exec($ch);
        curl_close($ch);

        return redirect()->back()->with('success', 'Topup telah berhasil dilakukan');
    }

    public function cancelTopup($topupId)
    {
        $statusBatal = 'TPS-004';
        DB::table('tx_topup_saldo_ppob')
            ->where('TxTopupSaldoPpobID', $topupId)
            ->update([
                'Status' => $statusBatal
            ]);

        return redirect()->back()->with('success', 'Topup telah dibatalkan');
    }

    public function transaction()
    {
        $activeMerchant = DB::table('ms_merchant_account')
            ->where('ms_merchant_account.ActivatedPPOB', '=', 1)
            ->count('ms_merchant_account.MerchantID');

        // Transaksi Sukses
        $transactionSuccess = DB::table('tx_ppob_order')
            ->join('ms_status_ppob', 'ms_status_ppob.StatusPPOBID', '=', 'tx_ppob_order.StatusOrder')
            ->where('ms_status_ppob.IsActive', '=', 1)
            ->where('ms_status_ppob.StatusPPOBID', '=', 'PST-002')
            ->orWhere('ms_status_ppob.StatusPPOBID', '=', 'PRE-002');

        $sumProfitMargin = $transactionSuccess
            ->sum('tx_ppob_order.CompanyMargin');

        $sumValueTransaction = $transactionSuccess
            ->sum('tx_ppob_order.TotalPrice');

        $totalTransactionSuccess = $transactionSuccess
            ->count('tx_ppob_order.PPOBOrderID');

        // Transaksi Dalam Proses
        $transactionPending = DB::table('tx_ppob_order')
            ->join('ms_status_ppob', 'ms_status_ppob.StatusPPOBID', '=', 'tx_ppob_order.StatusOrder')
            ->where('ms_status_ppob.IsActive', '=', 1)
            ->Where('ms_status_ppob.StatusPPOBID', '=', 'PST-001')
            ->orWhere('ms_status_ppob.StatusPPOBID', '=', 'PRE-001');

        $totalTransactionPending = $transactionPending
            ->count('tx_ppob_order.PPOBOrderID');

        // Transaksi Gagal
        $transactionFailed = DB::table('tx_ppob_order')
            ->join('ms_status_ppob', 'ms_status_ppob.StatusPPOBID', '=', 'tx_ppob_order.StatusOrder')
            ->where('ms_status_ppob.IsActive', '=', 1)
            ->where('ms_status_ppob.StatusPPOBID', '=', 'PST-003')
            ->orWhere('ms_status_ppob.StatusPPOBID', '=', 'PRE-003');

        $totalTransactionFailed = $transactionFailed
            ->count('tx_ppob_order.PPOBOrderID');

        return view('ppob.transaction.index', [
            'activeMerchant' => $activeMerchant,
            'sumValueTransaction' => $sumValueTransaction,
            'sumProfitMargin' => $sumProfitMargin,
            'totalTransactionSuccess' => $totalTransactionSuccess,
            'totalTransactionPending' => $totalTransactionPending,
            'totalTransactionFailed' => $totalTransactionFailed

        ]);
    }

    public function getTransactions(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlPPPOBTransaction = DB::table('tx_ppob_order')
            ->join('ms_status_ppob', 'ms_status_ppob.StatusPPOBID', '=', 'tx_ppob_order.StatusOrder')
            ->join('ms_merchant_account', 'ms_merchant_account.MerchantID', '=', 'tx_ppob_order.MerchantID')
            ->where('ms_status_ppob.IsActive', '=', 1)
            ->select('tx_ppob_order.*', 'ms_merchant_account.StoreName', 'ms_merchant_account.PhoneNumber', 'ms_status_ppob.StatusName');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlPPPOBTransaction->whereDate('tx_ppob_order.TransactionDate', '>=', $fromDate)
                ->whereDate('tx_ppob_order.TransactionDate', '<=', $toDate);
        }

        // Get data response
        $data = $sqlPPPOBTransaction->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('StatusName', function ($data) {
                    $statusPrepaidDalamProses = 'PRE-001';
                    $statusPrepaidBerhasil = 'PRE-002';
                    $statusPrepaidGagal = 'PRE-003';
                    $statusPostpaidDalamProses = 'PST-001';
                    $statusPostpaidBerhasil = 'PST-002';
                    $statusPostpaidGagal = 'PST-003';

                    if ($data->StatusOrder == $statusPrepaidDalamProses || $data->StatusOrder == $statusPostpaidDalamProses) {
                        $statusName = '<span class="badge badge-warning">' . $data->StatusName . '</span>';
                    } else if ($data->StatusOrder == $statusPrepaidBerhasil || $data->StatusOrder == $statusPostpaidBerhasil) {
                        $statusName = '<span class="badge badge-success">' . $data->StatusName . '</span>';
                    } else if ($data->StatusOrder == $statusPrepaidGagal || $data->StatusOrder == $statusPostpaidGagal) {
                        $statusName = '<span class="badge badge-danger">' . $data->StatusName . '</span>';
                    } else {
                        $statusName = 'Error, status tidak ditemukan';
                    }

                    return $statusName;
                })
                ->editColumn('TransactionDate', function ($data) {
                    return date('Y-m-d', strtotime($data->TransactionDate));
                })
                ->rawColumns(['TransactionDate', 'StatusName'])
                ->make(true);
        }
    }

    public function getActiveMerchant(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $sqlPPOBActivated = DB::table('ms_merchant_account')
            ->where('ms_merchant_account.ActivatedPPOB', '=', 1)
            ->select('ms_merchant_account.*');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlPPOBActivated->whereDate('ms_merchant_account.ActivatedPPOBDate', '>=', $fromDate)
                ->whereDate('ms_merchant_account.ActivatedPPOBDate', '<=', $toDate);
        }

        // Get data response
        $data = $sqlPPOBActivated->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('ActivatedPPOBDate', function ($data) {
                    return date('Y-m-d', strtotime($data->ActivatedPPOBDate));
                })
                ->rawColumns(['ActivatedPPOBDate'])
                ->make(true);
        }
    }
}
