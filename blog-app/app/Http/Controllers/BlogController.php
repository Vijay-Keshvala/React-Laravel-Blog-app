<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\TempImg;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    // This will return all blogs
    public function index(Request $request)
    {

        $blogs = Blog::orderBy('created_at','DESC');

        if (!empty($request->keyword)) {
            $blogs = $blogs->where('title','like','%'.$request->keyword.'%');
        }

        $blogs = $blogs->get();

        return response()->json([
            'status' => true,
            'data' => $blogs
        ]);
    }

    // This will return single blogs
    public function show($id)
    {
        $blog = Blog::find($id);

        if ($blog == null) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found',
            ]);
        }

        $blog['date'] = \Carbon\Carbon::parse($blog->created_at)->format('d, M, y');

        return response()->json([
            'status' => true,
            'data' => $blog
        ]);
    }

    // This will store a blogs
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:10',
            'author' => 'required|min:3',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix errors',
                'error' => $validator->errors()
            ]);
        }

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->author = $request->author;
        $blog->description = $request->description;
        $blog->shortDesc = $request->shortDesc;
        $blog->save();

        // save image //
        $tempimg = TempImg::find($request->image_id);

        if ($tempimg != null) {
            $imgExtArray = explode('.', $tempimg->name);
            $ext = last($imgExtArray);
            $imgname = time() . '-' . $blog->id . '.' . $ext;

            $blog->image = $imgname;
            $blog->save();

            $sourcePath = public_path('uploads/temp/' . $tempimg->name);
            $desPath = public_path('uploads/blog/' . $imgname);

            // Ensure the blog folder exists
            if (!File::exists(public_path('uploads/blog'))) {
                File::makeDirectory(public_path('uploads/blog'), 0755, true);
            }

            // Copy file and check if successful
            if (File::exists($sourcePath)) {
                File::copy($sourcePath, $desPath);
                \Log::info("File copied successfully from $sourcePath to $desPath");
            } else {
                \Log::error("Source file does not exist: $sourcePath");
            }
        }


        return response()->json([
            'status' => true,
            'message' => 'Blog added successfully',
            'data' => $blog
        ]);
    }

    // This will update a blogs
    public function update($id, Request $request)
    {
        $blog = Blog::find($id);

        if ($blog == null) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:10',
            'author' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the errors',
                'errors' => $validator->errors()
            ]);
        }

        $blog->title = $request->title;
        $blog->author = $request->author;
        $blog->description = $request->description;
        $blog->shortDesc = $request->shortDesc;
        $blog->save();

        $tempimg = TempImg::find($request->image_id);

        if ($tempimg != null) {


            File::delete(public_path('uploads/blog/'.$blog->image));


            $imgExtArray = explode('.', $tempimg->name);
            $ext = last($imgExtArray);
            $imgname = time() . '-' . $blog->id . '.' . $ext;

            $blog->image = $imgname;
            $blog->save();

            $sourcePath = public_path('uploads/temp/' . $tempimg->name);
            $desPath = public_path('uploads/blog/' . $imgname);

            // Ensure the blog folder exists
            if (!File::exists(public_path('uploads/blog'))) {
                File::makeDirectory(public_path('uploads/blog'), 0755, true);
            }

            // Copy file and check if successful
            if (File::exists($sourcePath)) {
                File::copy($sourcePath, $desPath);
                \Log::info("File copied successfully from $sourcePath to $desPath");
            } else {
                \Log::error("Source file does not exist: $sourcePath");
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Blog updated successfully.',
            'data' => $blog
        ]);
    }

    // This will delete a blogs
    public function destroy($id)
    {
        $blog = Blog::find($id);

        if ($blog == null) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found.',
            ]);
        }

        // Delete blog image first
        File::delete(public_path('uploads/blogs/'.$blog->image));

        // Delete blog from DB
        $blog->delete();

        return response()->json([
            'status' => true,
            'message' => 'Blog deleted successfully.'            
        ]);
    }
}
