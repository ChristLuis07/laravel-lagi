<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index()
    {
        $lastData = $this->lastData();
        $data = Post::where('status', 'publish')->where('id', '!=', $lastData->id)->orderBy('id', 'desc')->paginate(5);

        return view('components.front.home-page', compact('data', 'lastData'));
    }

    private function lastData()
    {
        $data = Post::where('status', 'publish')->orderBy('id', 'desc')->latest()->first();
        return $data;
    }
}
