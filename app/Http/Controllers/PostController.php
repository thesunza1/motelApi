<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Resources\RoomTypeResource;
use App\Models\Motel;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostType;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    //
    public function createPostUser(Request $request)
    {
        $userId = $request->user()->id;
        $room = TenantUser::where('user_id', $userId)->first()->tenant->room;
        $roomId = $room->id ;
        $posth = count($room->posts) ;
        if($posth > 0 ) {
            return response()->json([
                'statusCode' => 0
                ,'posts' => $posth ,
            ]);
        }
        $title = ' tìm người ở ghép ';
        $conpound_content = $request->content;
        $post = Post::create([
            'title' => $title,
            'room_id' => $roomId,
            'conpound_content' => $conpound_content,
            'content' => '',
            'post_type_id' => 2,
            'status' => 1
        ]);
        return response()->json([
            'statusCode' => 1,
            'posts' => $post,
        ]);
    }
    public function getPostConpound(Request $request)
    {
        $userId = $request->user()->id;
        $roomId = TenantUser::where('user_id', $userId)->first()->tenant->room->id;

        $post = Post::where('room_id', $roomId)->get();
        return response()->json([
            'statusCode' => 1,
            'posts' => $post
        ]);
    }
    public function changeStatusConpound(Request $request)
    {
        $post = Post::find($request->post_id);
        $post->status = -$post->status;
        $post->save();
        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public function deleteConpound(Request $request)
    {
        Post::find($request->post_id)->delete();
        return response()->json([
            'statusCode' => 1,
        ]);
    }

    public function getPost(Request $request)
    {
        $post_type = PostType::all();
        $posts = Post::where('status', 1)->orderByDesc('updated_at');
        $postsPaginator = $posts->with('room_type.first_img_detail')->with('room.room_type.first_img_detail')
            ->with('room_type.motel.user')->with('room.room_type.motel.user')
            ->with('room.latest_tenant.tenant_users.user')->paginate(9);
        return response()->json([
            'statusCode' => 1,
            'posts' => $postsPaginator,
            'post_type' => $post_type,
        ]);
    }
    public function getSearch(Request $request)
    {
    }
    public function detailPost(Request $request)
    {
        $post_id = $request->post_id;
        $post = Post::find($post_id);
        $postWith = $post->loadMissing('room_type.img_details')->loadMissing('room.room_type.img_details')
            ->loadMissing('room_type.motel.user')->loadMissing('room.room_type.motel.user')
            ->loadMissing('room.latest_tenant.tenant_users.user')->loadMissing('room_type.motel.motel_imgs.img_details')->loadMissing('room.room_type.motel.motel_imgs.img_details');
        $postRes = new  PostResource($postWith);
        $rooms = null;
        if ($post->post_type_id == 1) {
            $rooms = $post->room_type->none_rooms;
        }
        return response()->json([
            'statusCode' => 1,
            'post' => $postRes,
            'post_id' => $post_id,
            'rooms' => $rooms,
        ]);
    }
    public function sendIntoNoti(Request $request)
    {
        $title = 'muốn vào phòng trọ';
        $content = '';
        $user = User::find($request->user()->id);
        $content .= 'phòng muốn vào : ' . implode(", ", $request->ListRooms) . '<br>';
        $content .= 'họ tên: ' . $user->name . '<br/>';
        $content .= ' điện thoại: ' . $user->phone_number . '<br/>';
        $content .= ' nghề: ' . $user->job . '<br/>';
        $user = Post::find($request->postId)->room_type->motel->user;
        $senderId = $request->user()->id;
        $receiverId = $user->id;
        NotiController::sendNotiChoose($title, $senderId, $receiverId, $content, 4, null, 0);
        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public function sendIntoNotiRoom(Request $request)
    {
        $title = 'muốn vào phòng trọ';
        $content = '';
        $user = User::find($request->user()->id);
        $content .= 'họ tên: ' . $user->name . '<br/>';
        $content .= ' điện thoại: ' . $user->phone_number . '<br/>';
        $content .= ' nghề: ' . $user->job . '<br/>';
        $users = Post::find($request->postId)->room->latest_tenant->tenant_users;
        $senderId = $request->user()->id;
        foreach ($users as $user) {
            $receiverId = $user->user->id;
            NotiController::sendNotiChoose($title, $senderId, $receiverId, $content, 4, null, 0);
        }
        return response()->json([
            'statusCode' => 1,
        ]);
    }
    public function searchPost(Request $request)
    {
        //motel
        $search = $request->search;
        $address = $request->address;
        $arrSearch = explode(' ', $search);
        $arrAddress = explode(' ', $address);
        $post_type = $request->post_type;
        //room_type
        $sex = $request->sex;
        $price_max = $request->price_max;
        $price_min = $request->price_min;
        $area_max = $request->area_max;
        $area_min = $request->area_min;
        // $test = DB::table('posts')->;
        if ($post_type == 1) {
            $room_types = DB::table('room_types');
            // ->whereBetween('cost', [$price_min, $price_max])
            // ->whereBetween('area', [$area_min, $area_max])
            // ->where('male', $this->toSex('male', $sex))
            // ->where('female', $this->toSex('female', $sex))
            // ->where('everyone', $this->toSex('everyone', $sex));
            // $roMotel =  $room_types->join('motels', function ($join) use ($address, $search, $arrAddress, $arrSearch) {
            //     $join->on('room_types.motel_id', '=', 'motels.id')
            //         ->where('motels.address', 'like', '%' . $address . '%')
            //         ->where('motels.name', 'like', '%' . $search . '%')
            //         ->orwhere(function ($query) use ($arrSearch) {
            //             if (count($arrSearch) > 1) {
            //                 foreach ($arrSearch as $val) {
            //                     $query->orwhere('motels.name', 'like', '%'.$val.'%');
            //                 }
            //             }
            //         })
            //         ->orwhere(function ($query) use ($arrAddress) {
            //             if (count($arrAddress) > 1) {
            //                 foreach ($arrAddress as $val) {
            //                     $query->orwhere('motels.addresss', 'like', '%'.$val.'%');
            //                 }
            //             }
            //         });
            // })
            //     ->select('room_types.id as room_type_id')->get();
            $roMotel = $room_types->join('motels', 'room_types.motel_id', '=', 'motels.id')
                ->where('motels.address', 'like', '%' . $address . '%')
                ->where('motels.name', 'like', '%' . $search . '%')
                ->orwhere(function ($query) use ($arrSearch) {
                    if (count($arrSearch) > 1) {
                        foreach ($arrSearch as $val) {
                            $query->orwhere('motels.name', 'like', '%' . $val . '%');
                        }
                    }
                })
                ->orwhere(function ($query) use ($arrAddress) {
                    if (count($arrAddress) > 1) {
                        foreach ($arrAddress as $val) {
                            $query->orwhere('motels.address', 'like', '%' . $val . '%');
                        }
                    }
                })
                ->select('room_types.id as room_type_id')->get();

            $roMotelArr = [];
            foreach ($roMotel as $rType) {
                array_push($roMotelArr, $rType->room_type_id);
            }
            $post = Post::whereIn('room_type_id', $roMotelArr);
        } else {
            $room_types = DB::table('room_types')
                ->whereBetween('cost', [$price_min, $price_max])
                ->whereBetween('area', [$area_min, $area_max])
                ->where('male', $this->toSex('male', $sex))
                ->where('female', $this->toSex('female', $sex))
                ->where('everyone', $this->toSex('everyone', $sex));
            $roMotel =  $room_types->join('motels', function ($join) use ($address, $search, $arrSearch, $arrAddress) {
                $join->on('room_types.motel_id', '=', 'motels.id')
                    ->where('motels.address', 'like', '%' . $address . '%')
                    ->orwhere('motels.name', 'like', '%' . $search . '%')
                    ->orwhere(function ($query) use ($arrSearch) {
                        if (count($arrSearch) > 1) {
                            foreach ($arrSearch as $val) {
                                $query->where('motels.name', 'like', '%' . $val . '%');
                            }
                        }
                    })
                    ->orwhere(function ($query) use ($arrAddress) {
                        if (count($arrAddress) > 1) {
                            foreach ($arrAddress as $val) {
                                $query->orwhere('motels.address', 'like', '%' . $val . '%');
                            }
                        }
                    });
            })
                ->join('rooms', 'rooms.room_type_id', '=', 'room_types.id')
                ->select('rooms.id as id')->get();

            $roMotelArr = [];
            foreach ($roMotel as $rType) {
                array_push($roMotelArr, $rType->id);
            }
            $post = Post::whereIn('room_id', $roMotelArr);
        }
        $postarr = $post->with('room_type.first_img_detail')->with('room.room_type.first_img_detail')
            ->with('room_type.motel.user')->with('room.room_type.motel.user')
            ->with('room.latest_tenant.tenant_users.user')->paginate(10);
        return response()->json([
            'statusCode' => 1,
            'post' => $postarr,
            'motel' => $roMotel,
            'search' => $arrSearch,
            'address' => $arrAddress,
        ]);
    }
    //changeStatePost post :
    public function changeStatusPost(Request $request)
    {
        $post = Post::find($request->postId);
        $statusPost = $post->status;

        $post->status = ($statusPost == 0) ? 1 : 0;
        $post->updated_at = Carbon::now() ;
        $post->save();

        return response()->json([
            'statusCode' => 1,
        ]);
    }
    //get post motel get :
    public function getPostMotel(Request $request)
    {
        $motel = Motel::find($request->motelId);
        $roomTypes = $motel->room_types->loadMissing('posts')
            ->loadMissing('img_details');
        $ResroomTypes = RoomTypeResource::collection($roomTypes);

        return response()->json([
            'statusCode' => 1,
            'roomTypes' => $ResroomTypes,
        ]);
    }

    //createpostmotel post :
    public function createPostMotel(Request $request)
    {
        $motel = Motel::find($request->motelId);
        $room_types = $motel->room_types;
        DB::transaction(function () use ($motel, $room_types) {
            foreach ($room_types as $type) {
                $type->posts()->create([
                    'title' => $motel->name,
                    'room_id' => null,
                    'conpound_content' => '',
                    'content' => '',
                    'status' => 1,
                    'post_type_id' => 1
                ]);
            }
        });
        return response()->json([
            'statusCode' => 1,
        ]);
    }
    //sp function
    public function toSex($sexIn, $sex)
    {
        $num = 0;
        if ($sex == 3) return 1;
        if (strcmp('male', $sexIn) == 0) {
            $num = ($sex == 0 || $sex == 2) ? 1 : 0;
        } else if (strcmp('female', $sexIn) == 0) {
            $num = ($sex == 1 || $sex == 2) ? 1 : 0;
        }
        return $num;
    }
    //status function
    public static function checkPost($roomTypeId)
    {
        $roomType = RoomType::find($roomTypeId);
        $room = $roomType->none_rooms;
        // $room = $roomType->had_rooms;
        $numRoom = count($room);

        $post = $roomType->posts;
        if ($post != null) {
            if ($numRoom == 0) {
                $post->status = 0;
                $post->updated_at = Carbon::now() ;
                $post->save();
            } else {
                $post->status = 1;
                $post->updated_at = Carbon::now() ;
                $post->save();
            }
        }
    }
}
