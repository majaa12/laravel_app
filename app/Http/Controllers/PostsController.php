<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use DB;

class PostsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except'=>['index', 'show']]); //moze da vidi Blog stranu gde su svi postovi al ne moze da ih edituje
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$posts = Post::all(); //da vrati sve postove iz baze
        //$posts = Post::orderBy('title', 'asc')->get(); // sortiranje
        $posts = DB::select('SELECT * FROM posts');
        return view('posts.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //logika za cuvanje podataka sa forme i validacija
        $this -> validate($request, [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image|nullable|max:1999' //moze da je uploaduje i ne mora
        ]);

        //file upload
        if($request->hasFile('cover_image')){
            //get filename with extensions
            $filenameWithExt = $request-> file('cover_image')->getClientOriginalName();
            //get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            //get just ext
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            //filename to store 
            $fileNameToStore = $filename .'_'.time().'.'.$extension; //time() ako se desi da se uploaduje slika sa istim filename
            //upload image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        }else {
            $fileNameToStore = 'noimage.jpg'; //default-na slika ako on sam ne stavi
        }

        $post = new Post;
        $post ->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = auth()->user()->id;
        $post->cover_image = $fileNameToStore;
        $post->save();

        return redirect('/posts')->with('success', 'Post created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return view('posts.show')->with('post', $post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        if(auth()->user()->id !== $post->user_id){ //ako nije vlasnik posta
            return redirect('/posts')->with('error', 'Unauthorized page!');
        }
        return view('posts.edit')->with('post', $post);
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
       
        $this -> validate($request, [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image|nullable|max:1999' //moze da je uploaduje i ne mora
        ]);

        //file upload
        if($request->hasFile('cover_image')){
            //get filename with extensions
            $filenameWithExt = $request-> file('cover_image')->getClientOriginalName();
            //get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            //get just ext
            $extension = $request->file('cover_image')->getClientOriginalExtension();
            //filename to store 
            $fileNameToStore = $filename .'_'.time().'.'.$extension; //time() ako se desi da se uploaduje slika sa istim filename
            //upload image
            $path = $request->file('cover_image')->storeAs('public/cover_images', $fileNameToStore);
        }

        $post = Post::find($id);
        $post ->title = $request->input('title');
        $post->body = $request->input('body');
        if($request->hasFile('cover_image')){
            $post->cover_image = $fileNameToStore;
        }
        $post->save();

        return redirect('/posts')->with('success', 'Post updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if(auth()->user()->id !== $post->user_id){ //ako nije vlasnik posta
            return redirect('/posts')->with('error', 'Unauthorized page!');
        }

        if($post->cover_image != 'noimage.jpg'){
            Storage::delete('public/cover_images/'.$post->cover_image);
        }

        $post->delete();
        return redirect('/posts')->with('success', 'Post removed!');
    }
}
