<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class FileController extends Controller
{
    public function openFile($fileName){
        $name = urldecode($fileName);
        $fullPath = storage_path('app/public/files/'.$name);
        if (!file_exists($fullPath)) {
            abort(404, 'File not found');
        }
        return response()->file($fullPath);
    }
}