<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = array(
            'email' => $request->input('email'),
            'password' => $request->input('password')
        );

        if (Auth::attempt($credentials)) {
            return redirect('/home')->with('success', 'Berhasil. Selamat datang!');
        }

        return redirect()->back()->with('failed', 'Gagal. Email atau password salah silahkan coba lagi!');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function users()
    {
        return view('setting.user.index');
    }

    public function getUsers(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Get data, jika tanggal filter kosong tampilkan semua data.
        $sqlUsers = DB::table('ms_user')
            ->join('ms_role', 'ms_role.RoleID', '=', 'ms_user.RoleID')
            ->where('ms_user.IsTesting', '=', 0)
            ->select('ms_user.*', 'ms_role.RoleName');

        // Jika tanggal tidak kosong, filter data berdasarkan tanggal.
        if ($fromDate != '' && $toDate != '') {
            $sqlUsers->whereDate('ms_user.CreatedDate', '>=', $fromDate)
                ->whereDate('ms_user.CreatedDate', '<=', $toDate);
        }

        // Get data response
        $data = $sqlUsers->get();

        // Return Data Using DataTables with Ajax
        if ($request->ajax()) {
            return Datatables::of($data)
                ->make(true);
        }
    }

    public function newUser()
    {
        return view('setting.user.new');
    }

    public function createNewUser(Request $request)
    {
        $role = $request->input('role_id');

        $maxUserId = DB::table('ms_user')
            ->where('ms_user.IsTesting', '=', 0)
            ->where('ms_user.RoleID', '=', $role)
            ->max('UserID');

        if ($maxUserId == null) {
            $newUserId = $role . '-00001';
        } else {
            $maxUserIdNumber = explode("-", $maxUserId);
            $oldUserIdNumber = end($maxUserIdNumber);
            $newUserIdNumber = $oldUserIdNumber + 1;
            $newUserId = $role . '-' . str_pad($newUserIdNumber, 5, '0', STR_PAD_LEFT);
        }

        $request->validate([
            'email' => 'required|string|email|unique:ms_user,Email',
            'name' => 'required|string',
            'phonenumber' => 'required|numeric|unique:ms_user,PhoneNumber',
            'role_id' => 'required|string|in:IT,FI,BM,HR,AH,AD',
            'depo' => 'required|string|in:ALL,CRS,CKG,BDG',
            'password' => 'required|string',
        ]);

        $currentTime = date("Y-m-d H:i:s");

        $data = [
            'UserID' => $newUserId,
            'Email' => $request->input('email'),
            'Name' => $request->input('name'),
            'PhoneNumber' => $request->input('phonenumber'),
            'RoleID' => $request->input('role_id'),
            'Depo' => $request->input('depo'),
            'Password' => Hash::make($request->input('password')),
            'CreatedDate' => $currentTime,
            'LastDate' => $currentTime,
            'IsTesting' => 0
        ];

        $createUser = DB::table('ms_user')->insert($data);

        if ($createUser) {
            return redirect()->route('setting.users')->with('success', 'Berhasil, data user baru telah ditambahkan');
        } else {
            return redirect()->route('setting.users')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }
}