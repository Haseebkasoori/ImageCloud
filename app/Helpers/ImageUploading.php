<?php

namespace App\Helpers;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageUploading
{
    /**
     * Uploading and image converter.
     *
     * @param  string base64Image
     * @param  string belongsTo
     * @return string imagename
     */


    public static function imageUploading($base64Image, $belongsTo,$object_id)
    {
        $pos  = strpos($base64Image, ';');
        $type = explode(':', substr($base64Image, 0, $pos))[1];
        $ext=explode('/',$type);
        $imageName=uniqid(10).'.'.$ext[1];
        $img = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);

        // $file->storeAs(storage_path('app\\public\\'.$belongsTo.'\\'.$object_id).$imageName,$base64Image);
        file_put_contents(storage_path('app\\public\\'.$belongsTo.'\\'.$object_id.'\\profile').'\\'.$imageName,base64_decode($img));

        return $imageName;
    }

    /**
     * Uploading and image converter.
     *
     * @param  string base64Image
     * @param  string Image Name
     * @param  string belongsTo
     * @return string imagename
     */


    public static function PostimageUploading($base64Image, $belongsTo,$object_id,$imageName )
    {
        $img = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        file_put_contents(storage_path('app\\public\\'.$belongsTo.'\\'.$object_id.'\\posts').'\\'.$imageName,base64_decode($img));

        return $imageName;
    }
}
