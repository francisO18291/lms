<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PolicyController extends Controller
{
    // Privacy Policy Page
    public function privacyPolicy()
    {
        return view('policies.privacy');
    }

    // Terms of Service Page
    public function termsOfService()
    {
        return view('policies.terms');
    }

    // Cookie Policy Page
    public function cookiePolicy()
    {
        return view('policies.cookie');
    }
}
