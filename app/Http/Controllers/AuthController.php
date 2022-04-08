<?php

namespace App\Http\Controllers;

use App\Models\MsUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
            'password' => $request->input('password'),
            'IsDashboardRTMart' => 1
        );

        if (Auth::attempt($credentials)) {
            return redirect('/home')->with('success', 'Berhasil. Selamat datang!');
        }

        return redirect()->back()->with('failed', 'Gagal. Email atau password salah silahkan coba lagi!');
    }

    public function loginRabat()
    {
        return view('auth.login_rabat');
    }

    public function validateLoginRabat(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = array(
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'IsDashboardRTRabat' => 1
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
            ->select('ms_user.*', 'ms_role.RoleName')
            ->orderByDesc('ms_user.CreatedDate');

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
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/setting/users/edit/' . $data->UserID . '" class="btn btn-sm btn-warning">Edit</a>
                    <a data-user-name="' . $data->Name . '" data-user-id="' . $data->UserID . '" href="#" class="btn btn-sm btn-danger reset-password">Reset Password</a>';
                    return $actionBtn;
                })
                ->editColumn('CreatedDate', function ($data) {
                    if ($data->CreatedDate != null) {
                        $createdDate = date('d M Y H:i', strtotime($data->CreatedDate));
                    } else {
                        $createdDate = $data->CreatedDate;
                    }

                    return $createdDate;
                })
                ->rawColumns(['Action'])
                ->make(true);
        }
    }

    public function newUser()
    {
        $roleUser = DB::table('ms_role')
            ->whereNotIn('RoleID', ['R001', 'R002', 'R003', 'R004', 'R005', 'R006'])
            ->select('*')->get();

        return view('setting.user.new', [
            'roleUser' => $roleUser
        ]);
    }

    public function createNewUser(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|unique:ms_user,Email',
            'name' => 'required|string',
            'phonenumber' => 'required|numeric|unique:ms_user,PhoneNumber',
            'role_id' => 'required|string|exists:ms_role,RoleID',
            'depo' => 'required|string|in:ALL,CRS,CKG,BDG',
            'password' => 'required|string',
            'access' => 'required'
        ]);

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

        $inputAccess = array_flip($request->input('access'));
        $outputAccess = array_map(function () {
            return 1;
        }, $inputAccess);

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

        $data = array_merge($data, $outputAccess);

        $createUser = DB::table('ms_user')->insert($data);

        if ($createUser) {
            return redirect()->route('setting.users')->with('success', 'Berhasil, data user baru telah ditambahkan');
        } else {
            return redirect()->route('setting.users')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function editUser($user)
    {
        $userById = DB::table('ms_user')
            ->where('UserID', '=', $user)
            ->select('*')->first();

        $roleUser = DB::table('ms_role')
            ->whereNotIn('RoleID', ['R001', 'R002', 'R003', 'R004', 'R005', 'R006'])
            ->select('*')->get();

        return view('setting.user.edit', [
            'userById' => $userById,
            'roleUser' => $roleUser
        ]);
    }

    public function updateUser(Request $request, $user)
    {
        $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('ms_user', 'Email')->ignore($user, 'UserID')
            ],
            'name' => 'required|string',
            'phonenumber' => [
                'required',
                'numeric',
                Rule::unique('ms_user', 'PhoneNumber')->ignore($user, 'UserID')
            ],
            'role_id' => 'required|string|exists:ms_role,RoleID',
            'depo' => 'required|string|in:ALL,CRS,CKG,BDG',
            'access' => 'required'
        ]);

        $updateAccess = array_flip($request->input('access'));
        $outputUpdateAccess = array_map(function () {
            return 1;
        }, $updateAccess);

        $currentTime = date("Y-m-d H:i:s");

        $data = [
            'Email' => $request->input('email'),
            'Name' => $request->input('name'),
            'PhoneNumber' => $request->input('phonenumber'),
            'RoleID' => $request->input('role_id'),
            'Depo' => $request->input('depo'),
            'LastDate' => $currentTime,
            'IsDashboardRTMart' => 0,
            'IsDashboardRTSales' => 0
        ];

        $data = array_merge($data, $outputUpdateAccess);

        $updateUser = DB::table('ms_user')
            ->where('UserID', '=', $user)
            ->update($data);

        if ($updateUser) {
            return redirect()->route('setting.users')->with('success', 'Data user telah diubah');
        } else {
            return redirect()->route('setting.users')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function resetPassword($user)
    {
        $newPassword = Hash::make("rtmart2020");

        $data = [
            'Password' => $newPassword
        ];

        $updatePassword = DB::table('ms_user')
            ->where('UserID', '=', $user)
            ->update($data);

        if ($updatePassword) {
            return redirect()->route('setting.users')->with('success', 'Password user telah diubah');
        } else {
            return redirect()->route('setting.users')->with('failed', 'Terjadi kesalahan sistem atau jaringan');
        }
    }

    public function role()
    {
        return view('setting.role.index');
    }

    public function getRoles(Request $request)
    {
        $sqlRoles = DB::table('ms_role')
            ->whereNotIn('RoleID', ['R001', 'R002', 'R003', 'R004', 'R005', 'R006'])
            ->select('*');

        $data = $sqlRoles->get();

        if ($request->ajax()) {
            return Datatables::of($data)
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/setting/role/edit/' . $data->RoleID . '" class="btn-sm btn-warning">Edit</a>';
                    return $actionBtn;
                })
                ->rawColumns(['Action'])
                ->make(true);
        }
    }

    public function newRole()
    {
        return view('setting.role.new');
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|string|unique:ms_role,RoleID',
            'role_name' => 'required|string|unique:ms_role,RoleName'
        ]);

        $data = [
            'RoleID' => $request->input('role_id'),
            'RoleName' => $request->input('role_name')
        ];

        $createRole = DB::table('ms_role')->insert($data);

        if ($createRole) {
            return redirect()->route('setting.role')->with('success', 'Data Role baru telah ditambahkan');
        } else {
            return redirect()->route('setting.role')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function editRole($role)
    {
        $roleById = DB::table('ms_role')
            ->where('RoleID', '=', $role)
            ->select('*')->first();

        return view('setting.role.edit', [
            'roleById' => $roleById
        ]);
    }

    public function updateRole(Request $request, $role)
    {
        $request->validate([
            'role_id' => 'required|string',
            'role_name' => 'required|string'
        ]);

        $data = [
            'RoleID' => $request->input('role_id'),
            'RoleName' => $request->input('role_name')
        ];

        $updateRole = DB::table('ms_role')
            ->where('RoleID', '=', $role)
            ->update($data);

        if ($updateRole) {
            return redirect()->route('setting.role')->with('success', 'Data Role baru telah ditambahkan');
        } else {
            return redirect()->route('setting.role')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }
}