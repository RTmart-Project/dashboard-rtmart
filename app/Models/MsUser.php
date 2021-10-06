<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class MsUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'ms_user';
    protected $primaryKey = 'UserID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Email', 'Name', 'PhoneNumber', 'Password', 'RoleID', 'Token', 'OsType', 'IsOnline', 'IsLogin', 'LastPing', 'CreatedBy', 'LastUpdatedBy', 'CreatedDate', 'LastDate', 'IsTesting'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'Password', 'RoleID'
    ];

    public function getAuthPassword()
    {
        return $this->Password;
    }

    // public function getAuthIdentifier()
    // {
    //     return $this->UserID;
    // }
}