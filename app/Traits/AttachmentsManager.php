<?php

namespace App\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;


trait AttachmentsManager
{

//    public abstract function getFolderName(): string;


    private string $disk;

    public function setDisk($disk){
        $this->disk = $disk;
    }

    public function getDisk(){
        if(is_null($this->disk)){
             
             return 'public';
        }

        return $this->disk;
    }
    protected function directory()
    {

        return 'uploads/' . $this->getFolderName() . '/';
    }


    function getAttachment($image = null)
    {
        if (empty($image)){
            return null;
        }
        if (Storage::disk($this->getDisk())->exists($this->directory() . $image)) {
            return url("store/" . $this->directory() . $image);
        } else {

            return null;
        }
    }

    private function upload($format,  $image = null)
    {

        if ($image != null) {
            $format = $image?->extension() ?? null;


            $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
            if (!Storage::disk($this->getDisk())->exists($this->directory())) {
                Storage::disk($this->getDisk())->makeDirectory($this->directory());

            }
            Storage::disk($this->getDisk())->put($this->directory() . $imageName, file_get_contents($image));
//            debug($this->directory() . $imageName);
        } else {
            $imageName = null;
        }

        return $imageName;
    }
    public  function uploadAttachment(
        $image = null,
        $old_image = null,
    ) {
        if (!empty($old_image) && Storage::disk($this->getDisk())->exists($this->directory() . $old_image)) {
            Storage::disk($this->getDisk())->delete($this->directory() . $old_image);
        }
        $format = $image->extension() ?? null;
        $imageName = $this->upload($format, $image);
        return $imageName;
    }

    public function deleteAttachment($file_name){
        if ($file_name!=null && Storage::disk($this->getDisk())->exists($this->directory() . $file_name)) {
            Storage::disk($this->disk)->delete($this->directory() . $file_name);
        }
    }

}