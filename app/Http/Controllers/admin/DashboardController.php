<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Gallery;
use App\Models\Link;
use App\Models\News;
use App\Models\Service;

class DashboardController extends Controller
{
    function index(){
        return view('admin.pages.dashboard',[
            'title' => 'Dashboard',
            'documents' => Document::latest()->limit(4)->get(),
            'galleries' => Gallery::latest()->limit(3)->get(),
            'news' => News::latest()->limit(4)->get(),
            'links' => Link::latest()->limit(4)->get(),
            'services' => Service::latest()->limit(4)->get()
        ]);
    }
}
