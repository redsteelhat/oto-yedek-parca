<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Change application locale.
     */
    public function change($locale)
    {
        $supportedLocales = ['tr', 'en'];
        
        if (!in_array($locale, $supportedLocales)) {
            return redirect()->route('home')->with('error', 'Desteklenmeyen dil.');
        }
        
        Session::put('locale', $locale);
        
        // Get the previous URL
        $previousUrl = url()->previous();
        
        // If previous URL is invalid or contains /dil/, redirect to home
        if (!$previousUrl || str_contains($previousUrl, '/dil/') || $previousUrl === url()->current()) {
            // Check if user is in admin panel
            if (request()->is('admin*') || str_contains($previousUrl ?? '', '/admin')) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('home');
        }
        
        // Redirect back to previous page
        return redirect($previousUrl);
    }
}

