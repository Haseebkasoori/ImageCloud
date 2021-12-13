<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    //Transform the resource into an array.
    //param  \Illuminate\Http\Request  $request
    //return array
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile_image' => $this->profile_image,
            'age' => $this->age,
            'email' => $this->email,
            'password' => $this->password,
            'created_at' => $this->created_at,
            'user_profile_image_path'=>url('storage/users/'.strval($this->id).'/profile'),
            'posts_attachment_path'=>url('storage/users/'.strval($this->id).'/posts'),
            // 'Images'=> $this->when(!empty(PostResource::collection($this->Posts()->get())),PostResource::collection($this->Posts()->get()))
        ];
    }
}
