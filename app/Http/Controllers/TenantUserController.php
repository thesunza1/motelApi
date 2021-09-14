<?php

namespace App\Http\Controllers;

use App\Http\Resources\TenantUserResource;
use App\Models\Tenant;
use App\Models\TenantUser;
use Illuminate\Http\Request;

class TenantUserController extends Controller
{
    //
    public function getTenantUsers(Request $request)
    {
        $tenantId = $request->tenantId;
        $tenant = Tenant::find($tenantId);
        $tenantUsers = $tenant->tenant_users->loadMissing('user');
        // $tenantUsers->user;
        $tus = TenantUserResource::collection($tenantUsers);
        return response()->json([
            'tenantUsers' => $tus,
            'statusCode' => 1
        ]);
    }
    public function getInfoShare(Request $request)
    {
        $userId = $request->user()->id;
        $tenantUser = TenantUser::where('user_id', $userId)->first();

        return response()->json([
            'tenant_user' => $tenantUser,
            'statusCode' => 1,
        ]);
    }
    public function changeInfoShare(Request $request)
    {
        $tenantUser = TenantUser::find($request->tenant_user_id);
        $tenantUser->infor_share = -$tenantUser->infor_share;
        $tenantUser->save();
        return response()->json([
            'statusCode' => 1,
        ]);
    }
}
