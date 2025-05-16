<?php

namespace App\Http\Controllers\emails;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CallUserController extends Controller
{
    public function CallVisitor()
    {
        $frontendUrl = config('services.frontend_url') . '/visitor/business-submission';
        return redirect()->away($frontendUrl);
    }
    public function CallEntrepreneur()
    {
        $frontendUrl = config('services.frontend_url') . '/entrepreneur/business-information';
        return redirect()->away($frontendUrl);
    }
    public function Event()
    {
        $frontendUrl = config('services.frontend_url') . '/entrepreneur/event';
        return redirect()->away($frontendUrl);
    }
}
