<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('SuperAdmin')) {
            $urls = ShortUrl::with('user','company')->latest()->get();

        } elseif ($user->hasRole('Admin')) {
            $urls = ShortUrl::with('user','company')
                ->where('company_id', $user->company_id)
                ->latest()
                ->get();

        } elseif ($user->hasRole('Member')) {
            $urls = ShortUrl::with('user','company')
                ->where('user_id', $user->id)
                ->latest()
                ->get();

        } else {
            abort(403);
        }

        return view('shorturls.index', compact('urls'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('SuperAdmin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'original_url' => 'required|url',
        ]);

        ShortUrl::create([
            'original_url' => $request->original_url,
            'short_code'   => Str::random(6),
            'user_id'      => $user->id,
            'company_id'   => $user->company_id,
        ]);

        return back()->with('success', 'Short URL created successfully');
    }

    public function redirect($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->firstOrFail();
        $shortUrl->increment('clicks');

        return redirect()->away($shortUrl->original_url);
    }
}
