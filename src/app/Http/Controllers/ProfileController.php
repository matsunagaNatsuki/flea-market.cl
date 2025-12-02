<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Buy;
use App\Models\Sell;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function mypage(Request $request)
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        $page = $request->query('page', 'sell');
        if ($page === 'buy'){
            $items = Buy::with('sell')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        } else {
            $page = 'sell';

            $items = Sell::where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return view('profile', compact('profile', 'user', 'items', 'page'));
    }

    public function showProfile()
    {
        $profile = Profile::where('user_id', Auth::id())->first();

        return view('profile_edit');
    }

    public function editProfile(Request $request)
    {
        $profile = Profile::where('user_id', Auth::id())->first();

        if($profile) {
            $profile->update([
                'name' => $request->name,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building ?? null,
                'image' => $request->hasFile('image') ? $request->file('image')->store('profiles', 'public') : ($profile->image ?? 'default-avatar.png'),
            ]);
        }

        else {
            Profile::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building ?? null,
                'image' => $request->hasFile('image') ? $request->file('image')->store('profiles', 'public') : '—Pngtree—cat default avatar_5416936.png',
            ]);
        }

        return redirect('/mypage');
    }

}



