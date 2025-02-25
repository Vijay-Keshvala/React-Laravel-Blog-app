<?php

namespace App\Http\Controllers;

use App\Models\TempImg;
use Illuminate\Http\Request;
use Validator;

class TempImgController extends Controller
{
    // Storing image //

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'image' => 'required|image',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'=>false,
                'message'=>'Please fix errors',
                'error'=>$validator->errors()
            ]);
        } 

        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $imageName = time().'.'.$ext;


        // Store image in database //

        $TempImg = new TempImg();
        $TempImg->name = $imageName;
        $TempImg->save();

        // move img to temp folder //

        $image->move(public_path('uploads/temp'),$imageName);

        return response()->json([
            'status'=>true,
            'message'=>'Image uploaded successfully',
            'image'=>$TempImg
        ]);
    }
}
