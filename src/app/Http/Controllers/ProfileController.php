<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\Buy;
use App\Models\Sell;
use App\Models\Trade;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;

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
        } elseif ($page === 'trade') {
        $items = Sell::where('user_id', $user->id)
            ->whereHas('activeTrade')
            ->with('activeTrade')
            ->withCount('comments as message_count')
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

        return view('profile_edit', compact('profile'));
    }

    public function editProfile(ProfileRequest $request)
    {
        $profile = Profile::where('user_id', Auth::id())->first();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles', 'public');
        }

        if($profile) {
            $profile->update([
                'name' => $request->name,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building ?? null,
                'image' => $imagePath ?? $profile->image,
            ]);
        }else {
            Profile::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building ?? null,
                'image' => $imagePath,
            ]);
        }

        return redirect('/');
    }

    public function startTrade(Request $request, $sellId)
    {
        $buyerProfile = Profile::where('user_id', Auth::id())->firstOrFail();
        $sell = Sell::with('user.profile')->findOrFail($sellId);

        $trade = Trade::create([
            'sell_id' => $sellId,
            'seller_profile_id' => $sell->user->profile->id,
            'buyer_profile_id' => $buyerProfile->id,
            'status' => 'active',
        ]);

        return redirect()->route('get.buyer', $trade->id);
    }
}



