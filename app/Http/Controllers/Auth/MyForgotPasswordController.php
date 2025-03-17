<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Http\Controllers\Auth;

class MyForgotPasswordController extends ForgotPasswordController
{
    public function showLinkRequestForm()
    {
        return view('layouts.adminlte.password-recovery');
    }
}
