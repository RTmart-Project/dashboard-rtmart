<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    public function getPartner()
    {
        $data = DB::table('ms_partner')->where('IsActive', 1)->get();

        return $data;
    }
}
