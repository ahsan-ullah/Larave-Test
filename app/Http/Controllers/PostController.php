<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\PostLike;
use App\Models\PostUnlike;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\NewPostNotification;
use Illuminate\Support\Facades\Notification;

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
        $posts = Post::with(['user' => function($qu){
            $qu->select('id', 'name as author_name', 'email as author_email');
            $qu->first();
        }])
        ->withCount(['likes', 'unlikes'])
        ->with(['likes.user' => function($query) {
            $query->select('id', 'name as liked_by');
            $query->take(5);
        }])     
        
        ->orderBy('created_at', 'desc')
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
            'title' => 'required|min:2|max:255',
            'image'=> ['required','max:200'] 
        ]);

        if ($validator->fails()) {
            $data['status'] = false;
            $data['error'] = $validator->errors()->first();
            return response()->json($data, 422);
        }

        try {
            $postImage = NULL;
            // Handle file Upload
            if($request->hasFile('image')){

                // Get filename with the extension
                $filenameWithExt = $request->file('image')->getClientOriginalName();
                //Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('image')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore = $filename.'_'.time().'.'.$extension;
                // Upload Image
                $path = $request->file('image')->storeAs('public/post/',$fileNameToStore);

                $postImage = $fileNameToStore ;
            }
            $post = new Post;
            $post->title = $request->title;
            $post->uuid = Str::uuid();
            $post->user_id = $user->id;
            $post->description = $request->description;
            $post->image = $postImage;
            $post->created_at = date("Y-m-d H:i:s");
            $post->save();                     
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }        
        $this->shootNotification(); 
        return response()->json($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $post = Post::where('uuid', $uuid)
        ->with('user')
        ->with(['likes', 'unlikes'])        
        ->withCount(['likes', 'unlikes'])
        ->orderBy('created_at', 'asc')
        ->first();
        return response()->json($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_likes($post_id)
    {
        $post = PostLike::where('post_id', $post_id)        
        ->with(['user' => function($query){
            $query->select('id', 'name as liked_by');
        }])       
        // ->withCount(['likes', 'unlikes'])
        ->orderBy('created_at', 'desc')
        ->get();
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
            $postImage = NULL;
            // Handle file Upload
            if($request->hasFile('image')){

                // Get filename with the extension
                $filenameWithExt = $request->file('image')->getClientOriginalName();
                //Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('image')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore = $filename.'_'.time().'.'.$extension;
                // Upload Image
                $path = $request->file('image')->storeAs('public/post/',$fileNameToStore);

                $postImage = $fileNameToStore ;
            }
            $post = Post::find($id);
            $post->title = $request->title;
            $post->user_id = $user->id;
            $post->description = $request->description;
            $post->image = $postImage;
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
        $post = Post::where(['user_id' => $user->id, 'uuid' => $request->uuid])->first();
        
        if (!empty($post)) {
            try {
                if (file_exists('storage/post/'.$post->image)) {
                    try {
                        unlink('storage/post/'.$post->image);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }                    
                }
                if ($post->delete()) {
                    PostLike::where('post_id', $post->id)->delete();
                    PostUnlike::where('post_id', $post->id)->delete();
                }
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
            $data['message'] = "You are not authorized to delete this post";
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
        $post = Post::findOrFail($request->post_id);
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

    public function shootNotification()
    {
        $notifyData = User::get();
        try {
            Notification::send($notifyData, new NewPostNotification);
        } catch (\Throwable $th) {
            // return $th->getMessage();
        }
    }
}
