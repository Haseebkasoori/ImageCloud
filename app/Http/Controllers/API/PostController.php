<?php

namespace App\Http\Controllers\API;
//use Illuminate\Support\Facades\Validator;

use App\Helpers\ImageUploading;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\SavePostRequest;
use App\Http\Requests\SearchPostRequest;
use App\Http\Requests\SharedPostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\User;
use App\Models\Post;
use App\Http\Resources\UserResource;
use App\Http\Resources\PostResource;
use App\Jobs\ShareWithMailJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
    */
    public function SaveImage(SavePostRequest $request)
    {
        try{
            $user=request()->user_data;
            $number_of_images=null;
            foreach (request('attachments') as $key => $value) {
                if(!empty($value['image_name'])){
                    $file_name[$key]=ImageUploading::PostimageUploading($value['base64_image'],'users',strval($user->_id),$value['image_name']);
                }
                $post=new Post;
                $post->_id=new \MongoDB\BSON\ObjectId();
                $post->User()->associate((string)$user->_id);
                $post->visibility=request('visibility');
                $post->image_name=$file_name[$key];
                if(request('visibility')=="Private")
                {
                    $post->share_with=implode("|",request('share_with'));
                }
                $post->save();
                $share_link_data[]=['image_name'=>$value['image_name'],'share_link'=>url($post->id)];
                $number_of_images++;
            }

            // sending mails
            if(!empty(request('share_with'))){
                foreach (request('share_with') as $value) {
                    dispatch(new ShareWithMailJob(['email'=>$value, 'name'=>$user->name]));
                }
            }
            $post->orderBy('updated_at', 'desc')->limit($number_of_images)->get();

            $data['share_link_data']=$share_link_data;
            // $data['recently_uploaded']=new PostResource($post);

            // data creation for response

            $response_data['message']='Susseccfully Uploaded!!';
            $response_data['data']=$data;
            return response()->success($response_data,200);
        }
        catch (\Exception $e) {
            return response()->error(['message'=>$e->getMessage()],500);
        }
    }



    /**
     * Upadate Post Image Status Api
     *
     * @return \Illuminate\Http\Response
    */
    public function UpdatePost(UpdatePostRequest $request, $id){
        try{
            $post=Post::find($id);
            $post->visibility=request('visibility');
            if(request('visibility')=="Private")
            {
                $post->share_with=implode("|",request('share_with'));
            }

            $post->update();
            $response_data['data']=null;
            $response_data['message']='Update successfully!!';
            return response()->success($response_data,200);

        }
        catch (\Exception $e) {
            return response()->json(['error'=>$e->getMessage()], 500);
        }
    }

    /**
     * Delete Post api
     *
     * @return \Illuminate\Http\Response
    */
    public function DeletePost(DeletePostRequest $reqeust,$id){
        try{
            $user=request()->user_data;
            $post=Post::where('user_id',strval($user->_id))->where('_id',$id)->first();
            if(!empty($post)){
                $post->delete();
                return response()->success(['data'=>null,"message" => "Delete Successfully !!"],200);
            }
            else{
                return response()->error(['message'=>"Not Authorized to Delete"], 401);
            }
        }
        catch (\Exception $e) {
            return response()->error(['message'=>$e->getMessage()], 500);

        }
    }


    /**
     * Search Post api
     *
     * @return \Illuminate\Http\Response
    */
    public function GetPost(SearchPostRequest $reqeust,$id){
        try{
            $user=request()->user_data;
            $post=Post::where('user_id',strval($user->_id));
            foreach (request()->all() as $key => $value)          //Take Changes in Array
                if (in_array($key, ['image_name', 'extension', 'visibility']))
                    $updation[$key] = $value;
                    dd($updation);
                    $post->where('visibility','like',"%".request('visibility')."%");
                $data = $image->where($updation)->get();
                if ($data != null)
                    return response()->json($data);
                else
                    return response(["message" => "data not found"], 404);

            // $posts = Post::where('created_at', '=', Carbon::today()->toDateString())->get();
            // dd($posts);
            // if(request()->has('created_at')){
            //     $post->where('created_at','like',date(request('created_at'))."%");
            // }
            // if(request()->has('today')){

            // }

            $post=$post->paginate(10);

            if(!empty($post)){
                return response()->success(['data'=>PostResource::collection($post),"message" => "Posts Data !!"],200);
            }
            else{
                return response()->error(['message'=>"Something Went Wrong!!"], 401);
            }
        }
        catch (\Exception $e) {
            return response()->error(['message'=>$e->getMessage()], 500);
        }
    }

    /**
     * Search Post api
     *
     * @return \Illuminate\Http\Response
    */

    public function SharedPost(SharedPostRequest $reqeust,$id){
        try{
            $user=request()->user_data;
            $post=Post::find($id)->first();
            if(!empty($post)){
                return response()->file(storage_path('app\\public\\users\\'.$post->user_id).'\\posts\\'.$post->image_name);
            }
            else{
                throw new \Exception('Something Went worng please try Again later');
            }
        }
        catch (\Exception $e) {
            return response()->error(['message'=>$e->getMessage()], 500);
        }
    }





}
