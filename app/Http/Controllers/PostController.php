<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostUnlike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $posts = Post::with(['likes' => function($query) {
            $query->take(5);
        }])        
        ->withCount(['likes', 'unlikes'])
        ->orderBy('created_at', 'asc')
        ->paginate(50);
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2|max:255'
        ]);

        if ($validator->fails()) {
            $data['status'] = false;
            $data['error'] = $validator->errors()->first();
            return response()->json($data, 422);
        }

        try {
            $post = new Post;
            $post->title = $request->title;
            $post->user_id = $user->id;
            $post->description = $request->description;
            $post->image = $request->image;
            $post->total_like = 0;
            $post->total_unlike = 0;
            $post->created_at = date("Y-m-d H:i:s");
            $post->save();            
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
        
        return response()->json($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::where('id', $id)
        ->with(['likes', 'unlikes'])        
        ->withCount(['likes', 'unlikes'])
        ->orderBy('created_at', 'asc')
        ->first();
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2|max:255'
        ]);

        if ($validator->fails()) {
            $data['status'] = false;
            $data['error'] = $validator->errors()->first();
            return response()->json($data, 422);
        }
        $user = Auth::user();
        try {
            $post = Post::find($id);
            $post->title = $request->title;
            $post->user_id = $user->id;
            $post->description = $request->description;
            $post->image = $request->image;
            $post->total_like = 0;
            $post->total_unlike = 0;
            $post->created_at = date("Y-m-d H:i:s");
            $post->update();            
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }        
        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $post = Post::where('user_id', $user->id)->first($request->id);
        if (!empty($post)) {
            try {
                // $post = Post::findOrFail($request->id);
                $post->delete();
                $data['status'] = true;
                $data['message'] = "Post has been successfully deleted";
                return response()->json($data, 200);
            } catch (\Throwable $th) {
                $data['status'] = false;
                $data['message'] = $th->getMessage();
                return response()->json($data, 422);
            }  
        }else{
            $data['status'] = false;
            $data['message'] = "You are not authorized to deleted this post";
            return response()->json($data, 422);
        }           

    }

    /**
     * Set Like the specified post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function likeOnPost(Request $request)
    {
        $user = Auth::user();
        $post = Post::findOrFail($request->post_id);
        if (!empty($post)) {
            try {
                $like = new PostLike;
                $data = [
                    'post_id' => $post->id,
                    'user_id' => $user->id
                ];
                $like::create($data);
                $data['status'] = true;
                $data['message'] = "Your Like has been successfully placed";
                return response()->json($data, 200);
            } catch (\Throwable $th) {
                $data['status'] = false;
                $data['message'] = $th->getMessage();
                return response()->json($data, 422);
            }
            
        }
    }

    /**
     * Set UnLike the specified post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function unLikeOnPost(Request $request)
    {
        $user = Auth::user();
        $post = Post::findOrFail($request->id);
        if (!empty($post)) {
            try {
                $like = new PostUnlike;
                $data = [
                    'post_id' => $request->post_id,
                    'user_id' => $user->id
                ];
                $like::create($data);
                $data['status'] = true;
                $data['message'] = "Your Un-Like has been successfully placed";
                return response()->json($data, 200);
            } catch (\Throwable $th) {
                $data['status'] = false;
                $data['message'] = $th->getMessage();
                return response()->json($data, 422);
            }
            
        }
    }
}
