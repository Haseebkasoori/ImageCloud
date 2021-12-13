<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    //Transform the resource into an array.
    //param  \Illuminate\Http\Request  $request
    //return array
    public function toArray($request)
    {
        return [
            'id' => $this->_id,
            'user_id' => $this->user_id,
            'visibility' => $this->visibility,
            'share_with' => $this->share_with,
            'image_name' => $this->image_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'share_able_link'=>url($this->id)
        ];
    }
}
