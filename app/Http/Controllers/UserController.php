<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use App\Models\Role;
use DateTime;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    //login
    public function login(Request $request)
    {
        // $validated = $request->validate([
        //     'email' => 'required',
        //     'password' =>'required'
        // ]);
        $password = $request->password;
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                //khong ton tai email vua nhap
                'loginStatus' => 1,
            ]);
        }
        if (!Hash::check($password, $user->password)) {
            return response()->json([
                // sai pass
                'loginStatus' => 2,
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
    public function userRegister(Request $request)
    {
        $email = $request->email . '@gmail.com';
        // $date = DateTime::createFromFormat('Y-m-d H:i:s' , $request->date . ' 00:00:00');
        $checkemail = $this->emailCheck($email);
        if ($checkemail == 1) {
            return response()->json([
                'statusCode' => 0
            ]);
        };
        $user = User::create([
            'name' => $request->names,
            'email' => $email,
            'password' => Hash::make($request->password),
            'role_id' => 1,
            'sex' => $request->sex,
            'address' => $request->address,
            'birth_date' => $request->date,
            'job' => $request->job,
            'phone_number' => $request->phone_number,
        ]);
        return response()->json([
            'statusCode' => '1',
            'user' => $user
        ]);
    }

    //register new motel user role
    public function motelRegister(Request $request)
    {
        $users = json_decode($request->users);
        $motel = json_decode($request->motel);
        $motel_imgs = json_decode($request->motel_img);
        $motel_equip = json_decode($request->motel_equip);
        $motel_equips = json_decode($request->motel_equips);
        $room_types = json_decode($request->room_types);
        //motel_img
        $motel_img_num = $request->motel_img_num;
        $motel_equip_num = $request->motel_equip_num;
        $motel_equips_num = $request->motel_equips_num;
        //check email
        $email =  $users->email . '@gmail.com';
        if ($this->emailCheck($email) == 1) {
            return response()->json(['statusCode' => 0]);
        }
        DB::transaction(function () use($request ,$users, $motel, $motel_imgs , $motel_equip, $motel_equips, $room_types , $motel_img_num, $motel_equip_num, $motel_equips_num, $email) {
            //create user
            $user = User::create([
                'name' => $users->names,
                'email' => $email,
                'password' => Hash::make($users->password),
                'role_id' => 2,
                'sex' => $users->sex,
                'address' => $users->address,
                'birth_date' => $users->date,
                'job' => $users->job,
                'phone_number' => $users->phone_number,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            //create motel
            $motels = $user->motel()->create([
                'name' => $motel->names,
                'phone_number' => $motel->phone_number,
                'address' => $motel->address,
                'latitude' => $motel->latitude,
                'longitude' => $motel->longitude,
                'closed' => $this->tohave($motel->closed),
                'open' => $this->tohave($motel->open),
                'camera' => $this->toInterger($motel->camera),
                'parking' => $motel->parking,
                'deposit' => $motel->deposit,
                'elec_cost' => $this->tohave($motel->elec_cost),
                'water_cost' => $this->tohave($motel->water_cost),
                'people_cost' => $this->tohave($motel->people_cost),
                'content' => $motel->content,
                'auto_post' => $this->toInterger($motel->auto_post),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            //create motel_imgs
            $public_img = $motels->motel_imgs()->create($this->createMotelImgs($motel_imgs));
            $motel_equip1 = $motels->motel_imgs()->create($this->createMotelImgs($motel_equip));
            $motel_equip2 = $motels->motel_imgs()->create($this->createMotelImgs($motel_equips));
            //create img_details
            $pfname = 'motel_img';
            $this->storeImgDetail($pfname, $motel_img_num, $public_img, $request);
            $pfname = 'motel_equip';
            $this->storeImgDetail($pfname, $motel_equip_num, $motel_equip1, $request);
            $pfname = 'motel_equips';
            $this->storeImgDetail($pfname, $motel_equips_num, $motel_equip2, $request);

            //--------------------------
            //create room_types
            $a = count($room_types);
            $filename = 'room';
            $phong = 1;
            for ($i = 0; $i < $a; $i++) {
                $room_typ = $motels->room_types()->create($this->dataRoomType($room_types[$i]));
                $room_num = $room_types[$i]->room_num;
                $this->createRooms($room_num, $room_typ, $phong);
                $phong += $room_num;
                $fname = $filename . $i;
                $num = $request->input($fname . '_num');
                $this->storeImgDetail1($fname, $num, $room_typ, $request);
                if ($motel->auto_post) {
                    $room_typ->posts()->create([
                        'title' => $motel->names,
                        'room_id' => null,
                        'conpound_content' => '',
                        'content' => '',
                        'status' => 1,
                        'post_type_id' => 1
                    ]);
                }
            }
        });

        //create posts


        return response()->json([
            'statusCode' => 1,
            'lat' => $motel->latitude,
            'log' => $motel->longitude,
        ]);
    }
    //find user
    public function findUser($email)
    {
        $user = User::where('email',$email)->get();
        $statusCode = 1;
        if (!$user) $statusCode = 2;
        return response()->json([
            'user' => $user,
            'statusCode' => $statusCode,
        ]);
    }
    public function getUser(Request $request) {
        $user = User::find($request->user()->id) ;
        return response()->json([
            'statusCode' => 1 ,
            'user' => $user,
        ]);
    }
    public function logoutAllDevice(Request $request) {
        $request->user()->tokens()->delete() ;
        return response()->json([
            'statusCode' => 1 ,
        ]);
    }

    public function updateUP(Request $request) {

        if (!Hash::check($request->password, $request->user()->password)) {
            return response()->json([
                // sai pass
                'statusCode'=> 0 ,
            ]);
        }
        $request->user()->update([
            'username' => $request->username,
            'password' =>Hash::make( $request->newpass),
        ]);
        return response()->json([
            'statusCode' => 1 ,
        ]);
    }

    public function updateAccount(Request $request){
        $request->user()->update([
            'name' => $request->name ,
            'address' => $request->address,
            'sex' => $request->sex ,
            'phone_number' => $request->phone_number ,
            'job' => $request->job ,
            'birth_date' => $request->birth_date ,
        ]);
        return response()->json([
            'statusCode' => 1 ,
        ]);
    }
    //get all user
    public function getAllUser(Request $request)  {
        $users = User::paginate(10) ;
        // $this->boolNumToString($users->data);
        return response()->json([
            'users' =>$users ,
            'statusCode' => 1 ,
        ]);
    }
    //update user /id
    public function updateUser(Request $request) {
        $userId = $request->userId ;
        $thisUser = User::find($userId);
        $userUpdateData = [
            'name' => $request->name ,
            'phone_number' => $request->phone_number ,
            'job' => $request->job,
            'birth_date' => $request->birth_date ,
            'password' => ($request->password != null)?  Hash::make($request->password) : $thisUser->password ,
        ];
        $thisUser->update($userUpdateData);
        $thisUser->save() ;

        return response()->json([
            'statusCode' => 1 ,
        ]);
    }
    //delete user  /id
    public function deleteUser(Request $request) {
        $thisUser = User::find($request->userId);
        $thisUser->delete() ;

        return response()->json([
            'statusCode' => 1 ,
        ]);
    }

    //support function
    private function emailCheck($email)
    {
        if (User::where('email', $email)->first()) {
            return 1;
        } else {
            return 0;
        }
    }
    private function toInterger($i)
    {
        if ($i) return 1;
        else if (!$i) return 0;
        else return 0;
    }
    private function tohave($i)
    {
        if ($i == 0) return -1;
        else return $i;
    }
    private function moveFileToImage($file)
    {
        $pathfile = 'image';
        $ran = Str::random(20);
        $namefile = Carbon::now()->timestamp . $ran . '.' . $file->getClientOriginalExtension();
        $file->move($pathfile, $namefile);
        return $namefile;
    }
    private function createMotelImgs($motel_img)
    {
        $data = [
            'place' => $motel_img->place,
            'content' => $motel_img->content,
            'img_type_id' => $motel_img->img_type_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        return $data;
    }
    private function storeImgDetail($pfname, $num, $public_img, $request)
    {
        for ($i = 0; $i < $num; $i++) {
            //$img =
            $img = $this->moveFileToImage($request->file($pfname . $i));
            $public_img->img_details()->create([
                'room_type_id' => null,
                'img' => $img,
            ]);
        }
    }
    private function dataRoomType($a)
    {
        $data = [
            'name' => $a->names,
            'area' => $a->area,
            'cost' => $a->const,
            'male' => $this->toInterger($a->male),
            'female' => $this->toInterger($a->female),
            'everyone' => $this->toInterger($a->everyone),
            'content' => "$a->content",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        return $data;
    }
    private function storeImgDetail1($pfname, $num, $public_img, $request)
    {
        for ($i = 0; $i < $num; $i++) {
            //$img =
            $img = $this->moveFileToImage($request->file($pfname . $i));
            $public_img->img_details()->create([
                'motel_img_id' => null,
                'img' => $img,
            ]);
        }
    }
    private function createRooms($room_num, $room_type, $start)
    {
        $i = $start;
        for ($i; $i <= $room_num + $start - 1; $i++) {
            $room_type->rooms()->create([
                'name' => "$i",
                'room_status_id' => 1
            ]);
        }
    }
    private function boolNumToString(&$data){
        $len = count($data) ;
        for($i =0 ; $i<$len ; $i++) {
            $data[$i]->have_room =( $data[$i]->have_room ==1 )? ' đã vào' : ' chưa vào';
            $data[$i]->sex =( $data[$i]->sex ==1 )? ' nữ' : ' nam';
        }
    }
}
