<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use App\Models\Role;
use DateTime;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    //login
   public function login(Request $request) {
    // $validated = $request->validate([
    //     'email' => 'required',
    //     'password' =>'required'
    // ]);
    $password = $request->password;
    $user = User::where('email', $request->email)->first();
    if(!$user ) {
        return response()->json([
            //khong ton tai email vua nhap
            'loginStatus' => 1 ,
        ]);
    }
    if ( !Hash::check($password, $user->password)){
        return response()->json([
            // sai pass
            'loginStatus' => 2 ,
        ]);
    }
    $token = strval($user->createToken('app')->plainTextToken);
    return response()->json([
        'user' => $user,
        'tokenUser' => $token,
        'loginStatus' => 0
    ]);
   }
   // register new user
   public function userRegister(Request $request) {
    $email = $request->email .'.@gmail.com';
    // $date = DateTime::createFromFormat('Y-m-d H:i:s' , $request->date . ' 00:00:00');
    $checkemail = User::where('email' , $email)->first() ;
    if($checkemail) {
        return response()->json([
            'statusCode' => 0
        ]);
    };
   $user = User::create([
        'name' => $request->names,
        'email' => $email,
        'password' => Hash::make($request->password),
        'address' => $request->names,
        'role_id' => 1,
        'sex' => $request->sex ,
        'address' => $request->address,
        'birth_date' =>$request->date ,
        'job' => $request->job ,
        'phone_number' => $request->phone_number ,
    ]);
    return response()->json([
        'statusCode' => '1',
        'user' => $user
    ]);
   }

   //register new motel user role
   public function motelRegister(Request $request){
    $data = json_decode( $request->motel);
    $images = json_decode($request->motel_img);

    return response()->json([
        'motel' => $data,
        'gg' => $images
    ]);
   }

}
