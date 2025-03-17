<?php
/*
 * Copyright (c) 2023. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;

class SiteController extends Controller
{
    public function index()
    {
        return view('index');
    }
}
