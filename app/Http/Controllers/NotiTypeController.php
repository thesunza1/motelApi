<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotiType;

class NotiTypeController extends Controller
{
    //
    public function notiType(Request $request) {
        $role_id = $request->user()->role_id ;
        $arr = [];
        if($role_id == 1) $arr=[1,4] ;
        else if($role_id ==2) $arr=[1,4];
        else $arr=[1,2,3,4];
        $notiType = NotiType::whereIn('id' , $arr)->get();

        return response()->json([
            'notiType' => $notiType,
        ]);
    }
}
