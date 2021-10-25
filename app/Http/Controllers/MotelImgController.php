<?php

namespace App\Http\Controllers;

use App\Http\Resources\ImgDetailResource;
use App\Http\Resources\MotelImgResource;
use App\Models\Motel;
use Illuminate\Http\Request;

class MotelImgController extends Controller
{
    //
    public function getMotelImgs(Request $request){
        $motel= Motel::find($request->motelId) ;
        $motel_imgs = $motel->motel_imgs;
        $img_details = $motel_imgs->loadMissing('img_details');
        $toArray = MotelImgResource::collection($img_details);

        return response()->json([
            'motelImgs' => $toArray,
            'statusCode' => 1 ,
        ]);
    }
}
