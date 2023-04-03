<?php

namespace App\Http\Controllers;

use App\Models\MsUser;
use App\Models\User;
use DateTime;
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
            $data = [
                'UserID' => Auth::user()->UserID,
                'URL' => $request->path(),
                'RouteName' => $request->route()->getName(),
                'IPAddress' => $request->ip(),
                'Browser' => $request->header('user-agent'),
                'CreatedDate' => date('Y-m-d H:i:s')
            ];

            DB::table('ms_user_activity_log')->insert($data);

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
        $isRTRabat = Auth::user()->IsDashboardRTRabat;
        Auth::logout();

        if ($isRTRabat == 1) {
            return redirect()->route('auth.login.rabat');
        } else {
            return redirect()->route('auth.login');
        }
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
            ->join('ms_roles', 'ms_roles.RoleID', '=', 'ms_user.RoleID')
            ->leftJoin('ms_user_activity_log', function ($join) {
                $join->on('ms_user_activity_log.UserID', 'ms_user.UserID');
                $join->whereRaw("ms_user_activity_log.UserActivityLogID = (
                    SELECT MAX(UserActivityLogID) FROM ms_user_activity_log WHERE ms_user_activity_log.UserID = ms_user.UserID
                )");
            })
            ->where('ms_user.IsTesting', '=', 0)
            ->select('ms_user.*', 'ms_roles.RoleName', 'ms_user_activity_log.URL', 'ms_user_activity_log.CreatedDate as LastActivityDate')
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
                ->editColumn('URL', function ($data) {
                    if ($data->URL != null) {
                        $url = $data->URL;
                    } else {
                        $url = "-";
                    }
                    return $url;
                })
                ->editColumn('LastActivityDate', function ($data) {
                    if ($data->LastActivityDate != null) {
                        $lastActivityDate = date('d M Y H:i', strtotime($data->LastActivityDate));
                    } else {
                        $lastActivityDate = "-";
                    }

                    return $lastActivityDate;
                })
                ->editColumn('CreatedDate', function ($data) {
                    if ($data->CreatedDate != null) {
                        $createdDate = date('d M Y H:i', strtotime($data->CreatedDate));
                    } else {
                        $createdDate = $data->CreatedDate;
                    }

                    return $createdDate;
                })
                ->addColumn('Action', function ($data) {
                    $actionBtn = '<a href="/setting/users/edit/' . $data->UserID . '" class="btn btn-sm btn-warning">Edit</a>
                    <a data-user-name="' . $data->Name . '" data-user-id="' . $data->UserID . '" href="#" class="btn btn-sm btn-danger reset-password">Reset Password</a>';
                    return $actionBtn;
                })
                ->addColumn('Detail', function ($data) {
                    $detailBtn = '<a href="/setting/users/log/' . $data->UserID . '" class="btn-sm btn-info">Detail</a>';
                    return $detailBtn;
                })
                ->rawColumns(['Detail', 'Action'])
                ->make(true);
        }
    }

    public function userLogDetail($userID, Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        $startDate = new DateTime($fromDate) ?? new DateTime();
        $endDate = new DateTime($toDate) ?? new DateTime();
        $startDateFormat = $startDate->format('Y-m-d');
        $endDateFormat = $endDate->format('Y-m-d');

        $sqlGetUserLog = DB::table('ms_user')
            ->join('ms_roles', 'ms_roles.RoleID', 'ms_user.RoleID')
            ->where('ms_user.UserID', $userID)
            ->select('ms_user.UserID', 'ms_user.Email', 'ms_user.Name', 'ms_user.PhoneNumber', 'ms_user.Depo', 'ms_roles.RoleName')
            ->first();

        $sqlGetUserLogDetail = DB::table('ms_user')
            ->leftJoin('ms_user_activity_log', 'ms_user_activity_log.UserID', 'ms_user.UserID')
            ->where('ms_user.UserID', $userID)
            ->whereDate('ms_user_activity_log.CreatedDate', '>=', $startDateFormat)
            ->whereDate('ms_user_activity_log.CreatedDate', '<=', $endDateFormat)
            ->select('ms_user_activity_log.*', 'ms_user.Name');

        $data = $sqlGetUserLogDetail;

        if ($request->ajax()) {
            return Datatables::of($data)
                ->editColumn('CreatedDate', function ($data) {
                    return date('d-M-Y H:i', strtotime($data->CreatedDate));
                })
                ->make(true);
        }

        return view('setting.user.detail', [
            'user' => $sqlGetUserLog
        ]);
    }

    public function newUser()
    {
        $roleUser = DB::table('ms_roles')
            ->whereNotIn('RoleID', ['R001', 'R002', 'R003', 'R004', 'R005', 'R006'])
            ->select('*')->get();

        $depo = DB::table('ms_distributor')
            ->select('Depo', 'DistributorName')
            ->where('Depo', '!=', '')
            ->get();

        return view('setting.user.new', [
            'roleUser' => $roleUser,
            'depo' => $depo
        ]);
    }

    public function createNewUser(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|unique:ms_user,Email',
            'name' => 'required|string',
            'phonenumber' => 'required|numeric|unique:ms_user,PhoneNumber',
            'role_id' => 'required|string|exists:ms_roles,RoleID',
            'depo' => 'required',
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
            'Name' => ucwords($request->input('name')),
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

        $roleUser = DB::table('ms_roles')
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
            'role_id' => 'required|string|exists:ms_roles,RoleID',
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
        $sqlRoles = DB::table('ms_roles')
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
            'role_id' => 'required|string|unique:ms_roles,RoleID',
            'role_name' => 'required|string|unique:ms_roles,RoleName'
        ]);

        $data = [
            'RoleID' => $request->input('role_id'),
            'RoleName' => $request->input('role_name')
        ];

        $createRole = DB::table('ms_roles')->insert($data);

        if ($createRole) {
            return redirect()->route('setting.role')->with('success', 'Data Role baru telah ditambahkan');
        } else {
            return redirect()->route('setting.role')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }

    public function editRole($role)
    {
        $roleById = DB::table('ms_roles')
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

        $updateRole = DB::table('ms_roles')
            ->where('RoleID', '=', $role)
            ->update($data);

        if ($updateRole) {
            return redirect()->route('setting.role')->with('success', 'Data Role baru telah ditambahkan');
        } else {
            return redirect()->route('setting.role')->with('failed', 'Gagal, terjadi kesalahan sistem atau jaringan');
        }
    }
}
