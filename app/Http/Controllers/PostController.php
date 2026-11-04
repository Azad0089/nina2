<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function show($id)
    {
        publish_matured_queued_posts();

        $post = Post::with(['blog.user', 'comments.user'])->findOrFail($id);
        $post->increment('views_count');

        return view('post', ['p' => $post]);
    }

    public function legacyShow(Request $request)
    {
        $id = $request->query('id', 1);
        return $this->show($id);
    }
}
