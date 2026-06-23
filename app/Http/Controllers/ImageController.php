<?php

namespace App\Http\Controllers;

class ImageController extends Controller
{
    public function mostrar($name){
        $name = urldecode($name);
        $fullPath = storage_path('app/public/products/' . $name);
        if (!file_exists($fullPath)) {
            abort(404, 'Image no found it');
        }
        return response()->file($fullPath);
    }

    public function profilePhoto($name){
        $name = urldecode($name);
        $fullPath = storage_path('app/public/profile-photos/' . $name);
        if (!file_exists($fullPath)) {
            abort(404, 'Image no found it');
        }
        return response()->file($fullPath);
    }
}
