@extends('layouts.app')

@section('content')
    <h1>Posts</h1>
        <!--da li ima postova uopste -->
        @if(count($posts) > 1)
            @foreach($posts as $post)
                <div class="well">
                    <h3><a href = "/posts/{{$post->id}}">{{$post->title}}</a></h3>
                    <small>Written on {{$post->created_at}}</small>
                </div>
            @endforeach
        @else


        @endif

@endsection