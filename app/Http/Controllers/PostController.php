<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('user')->where("status", "published")->get();
        return response()->json(PostResource::collection($posts));
    }

    public function indexAdmin()
    {
        $posts = Post::with('user')->get();
        return response()->json(PostResource::collection($posts));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $data = $request->validated();
        
        if ($request->file('image')) {
            $request->validate(["image" => ['image', 'mimes:png,jpeg,gif']]);
            $ext = $request->file('image')->extension();
            $name = Str::random(20);
            $path = $name . "." . $ext;
            $request->file('image')->storeAs('public/images', $path);
            $data['image'] = "images/" . $path;
        }
        $data['user_id'] = auth()->user()->id;

        $post = Post::create($data);
        return response()->json(["message" => "Post created successfully", "post" => new PostResource($post)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json(new PostResource($post));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            "title" => "string",
            "slug" => "string|unique:posts,slug," . $post->id,
            "description" => "string",
            "status" => "in:draft,published",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg",
            "user_id" => "exists:users,id",
        ]);

        if ($request->hasFile('image')) {
            if ($request->file('image')) {

                $request->validate(["image" => ['image', 'mimes:png,jpeg,gif']]);
                $ext = $request->file('image')->extension();
                $name = Str::random(20);
                $path = $name . "." . $ext;
                $request->file('image')->storeAs('public/images', $path);
                $data['image'] = "images/" . $path;
            }

            if ($post->image) {
                Storage::delete('public/' . $post->image);
            }
        }
        $post->update($data);
        return response()->json(["message" => "Post updated successfully", "post" => new PostResource($post->refresh())]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(["message" => "Post deleted successfully"]);
    }
}
