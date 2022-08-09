<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $page = Page::get('/');
        return view('index', compact('page'));
    }

    public function blog()
    {
        return view('blog');
    }

    public function faq()
    {
        $page = Page::get('faq');
        return view('faq');
    }

    public function page(Request $request, $url)
    {
        // static pages are routed manualy via router
        $page = Page::where('link', $url)->where('status', 'published')->firstOrFail();

        return view('page', compact('page'));
    }
}
