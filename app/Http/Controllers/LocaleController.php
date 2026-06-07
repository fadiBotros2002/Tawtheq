<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * @var list<string>
     */
    private const SUPPORTED = ['ar', 'en'];

    /**
     * Switch the application locale and persist it in the session.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, self::SUPPORTED, true)) {
            abort(404);
        }

        $request->session()->put('locale', $locale);

        return redirect()->back(fallback: route('login', absolute: false));
    }
}
