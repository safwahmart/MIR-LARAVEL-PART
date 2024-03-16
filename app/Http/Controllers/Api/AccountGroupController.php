<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Models\AccountGroup;
use Illuminate\Support\Facades\DB;

class AccountGroupController extends Controller
{
    public function index()
    {
        $areas = AccountGroup::all();
        return AreaResource::collection($areas);
    }
    public function getAccountControl()
    {
        $controls = DB::table('account_controls as ac')->select('ag.name as group_name', 'ag.name_bn as group_name_bn', 'ac.name as control_name', 'ac.name_bn as control_name_bn', 'ac.status as status')->leftJoin('account_groups as ag', 'ag.id', '=', 'ac.account_group_id')->get();
        return $controls;
    }
    public function getAccountSubsidary()
    {
        $controls = DB::table('account_subsidiaries as as')->select('ag.name as group_name', 'ag.name_bn as group_name_bn', 'ac.name as control_name', 'ac.name_bn as control_name_bn', 'as.name as sub_name', 'as.name_bn as sub_name_bn', 'ac.status as status')->leftJoin('account_groups as ag', 'ag.id', '=', 'as.account_group_id')
            ->leftJoin('account_controls as ac', 'ag.id', '=', 'as.account_control_id')
            ->get();
        return $controls;
    }
}
