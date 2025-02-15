<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->search;
        $data = Post::where('user_id', $user->id)->where(function ($query) use ($search) {
            if ($search) {
                $query->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%");
            }
        })->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('member.blogs.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('member.blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|max:255|min:5',
                'content' => 'required',
                'thumbnail' => 'required|max:4096',
            ],
            [
                'title.required' => 'Judul Wajib Diisi',
                'content.required' => 'Konten Wajib Diisi',
                'thumbnail.image' => 'Hanya gambar yang diperbolehkan',
                'thumbnail.max' => 'ukuran Maksimal untuk thumbnail adalah 4MB',

            ]
        );

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $image_name = time() . "_" . $image->getClientOriginalName();
            $destination_path = public_path(getenv('CUSTOM_THUMBNAIL_STORAGE'));
            $image->move($destination_path, $image_name);
        }

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'status' => $request->status,
            'thumbnail' => isset($image_name) ? $image_name : null,
            'slug' => $this->generateSlug($request->title),
            'user_id' => Auth::user()->id,
        ];

        Post::create($data);
        return redirect()->route('member.blogs.index')->with('success', 'Data Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        Gate::authorize('edit', $post);

        $data = $post;
        return view('member.blogs.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate(
            [
                'title' => 'required',
                'content' => 'required',
                'thumbnail' => 'image|max:4096',
            ],
            [
                'title.required' => 'Judul Wajib Diisi',
                'content.required' => 'Konten Wajib Diisi',
                'thumbnail.image' => 'Hanya gambar yang diperbolehkan',
                'thumbnail.max' => 'ukuran Maksimal untuk thumbnail adalah 4MB',
            ]
        );

        if ($request->hasFile('thumbnail')) {
            if (isset($post->thumbnail) && file_exists(public_path(getenv('CUSTOM_THUMBNAIL_STORAGE')) . "/" . $post->thumbnail)) {
                unlink(public_path(getenv('CUSTOM_THUMBNAIL_STORAGE')) . "/" . $post->thumbnail);
            }
            $image = $request->file('thumbnail');
            $image_name = time() . "_" . $image->getClientOriginalName();
            $destination_path = public_path(getenv('CUSTOM_THUMBNAIL_STORAGE'));
            $image->move($destination_path, $image_name);
        }

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'status' => $request->status,
            'thumbnail' => isset($image_name) ? $image_name : $post->thumbnail,
            'slug' => $this->generateSlug($request->title, $post->id),
        ];

        Post::where('id', $post->id)->update($data);

        return redirect()->route('member.blogs.index')->with('success', 'Data berhasil di-update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        if (isset($post->thumbnail) && file_exists(public_path(getenv('CUSTOM_THUMBNAIL_STORAGE')) . "/" . $post->thumbnail)) {
            unlink(public_path(getenv('CUSTOM_THUMBNAIL_STORAGE')) . "/" . $post->thumbnail);
        }

        Post::where('id', $post->id)->delete();
        return redirect()->route('member.blogs.index')->with('success', 'Data Berhasil Dihapus');
    }

    private function generateSlug($title, $id = null)
    {
        $slug = Str::slug($title);
        $count = Post::where('slug', $slug)->when($id, function ($query, $id) {
            return $query->where($id, '!=', $id);
        })->count();

        if ($count > 0) {
            $slug = $slug . "-" . ($count + 1);
        }
        return $slug;
    }
}
