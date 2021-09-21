<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImgDetailController extends Controller
{
    //
    public function image($filename) {
        $path =public_path().'/image/'.$filename;
        return response()->download($path);
    }
}
