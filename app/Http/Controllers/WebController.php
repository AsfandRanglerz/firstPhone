<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use App\Models\Seo;
use App\Models\TermCondition;
use Illuminate\Http\Request;

class WebController extends Controller
{
    //

    public function homepage()
    {
       $seo = Seo::where('page', 'home')->first();

return view('web.homepage', [
    'seo_title' => $seo->title,
    'seo_description' => $seo->description,
    'seo_keywords' => $seo->keywords,
    'seo_og_title' => $seo->og_title,
    'seo_og_description' => $seo->og_description,
]);

    }

    public function aboutpage()
    {
       $seo = Seo::where('page', 'about')->first();

return view('web.aboutpage', [
    'seo_title' => $seo->title,
    'seo_description' => $seo->description,
    'seo_keywords' => $seo->keywords,
    'seo_og_title' => $seo->og_title,
    'seo_og_description' => $seo->og_description,
]);

    }

    public function contactpage()
    {
       return view('web.contactpage');
    }

 public function termsConditions()
{
    $data = TermCondition::first(); // instead of all()
    return view('web.termscondition', compact('data'));
}


 public function privacypolicy()
{
    $data = PrivacyPolicy::first(); // instead of all()
    return view('web.privacypolicy', compact('data'));
}


}