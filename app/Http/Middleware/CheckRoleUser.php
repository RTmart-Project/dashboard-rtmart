<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckRoleUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if (in_array($user->RoleID, $roles)) {
            $data = [
                'UserID' => Auth::user()->UserID,
                'URL' => $request->path(),
                'RouteName' => $request->route()->getName(),
                'IPAddress' => $request->ip(),
                'Browser' => $request->header('user-agent'),
                'CreatedDate' => date('Y-m-d H:i:s')
            ];

            DB::table('ms_user_activity_log')
                ->insert($data);

            return $next($request);
        }

        return redirect()->route('home');
    }
}
