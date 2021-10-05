<?php

namespace App\Http\Controllers;

use App\Models\MotelImg;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    //
    public function uploadImg(Request $request)
    {
        $motelImgId = $request->count;
        $img_num = $request->img_num;

        $motelImg = MotelImg::find($motelImgId);

        DB::transaction(function () use ($motelImg, $img_num, $request) {
            $imgDetails = $motelImg->img_details;
            foreach ($imgDetails as $imgDetail) {
                if (file_exists(public_path('image/' . $imgDetail->img))) {
                    unlink(public_path('image/' . $imgDetail->img));
                }
            }
            $motelImg->img_details()->delete();
            $pathfile = 'image';
            for ($i = 0; $i < $img_num; $i++) {
                $files = $request->file('img' . $i);
                $ran = Str::random(20);
                $namefile = Carbon::now()->timestamp . $ran . '.' . $files->getClientOriginalExtension();
                $files->move($pathfile, $namefile);
                $motelImg->img_details()->create([
                    'room_type_id' => null,
                    'img' => $namefile,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        });
        return response()->json([
            'statusCode' => 1,
            'img_num' => $img_num,
        ]);
    }
    public function uploadRoomImg(Request $request)
    {
        $roomTypeId = $request->roomTypeId;
        $img_num = $request->img_num;

        $motelImg = RoomType::find($roomTypeId);

        DB::transaction(function () use ($motelImg, $img_num, $request) {
            $imgDetails = $motelImg->img_details;
            foreach ($imgDetails as $imgDetail) {
                if (file_exists(public_path('image/' . $imgDetail->img))) {
                    unlink(public_path('image/' . $imgDetail->img));
                }
            }
            $motelImg->img_details()->delete();
            $pathfile = 'image';
            for ($i = 0; $i < $img_num; $i++) {
                $files = $request->file('img' . $i);
                $ran = Str::random(20);
                $namefile = Carbon::now()->timestamp . $ran . '.' . $files->getClientOriginalExtension();
                $files->move($pathfile, $namefile);
                $motelImg->img_details()->create([
                    'room_type_id' => null,
                    'img' => $namefile,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        });
        return response()->json([
            'statusCode' => 1,
            'img_num' => $img_num,
        ]);
    }
}
