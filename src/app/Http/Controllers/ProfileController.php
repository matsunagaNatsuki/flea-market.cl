<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\Buy;
use App\Models\Sell;
use App\Models\Trade;
use App\Models\Message;
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
            ->where('status', 'trading')
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
        // 購入者が「取引を開始する」ボタンを押したとき
        $profile = Profile::where('user_id', Auth::id())->firstOrFail();

        $trade = Trade::create([
            'sell_id' => $sellId,
            'buyer_profile_id' => $profile->id,
            'status' => 'active',
        ]);

        return redirect()->route('get.buyer', $trade->id);
    }

    // 取引チャット（出品者）
    public function getSeller(Request $request, $tradeId)
    {
        $trade = Trade::where('id', $tradeId)
            ->with(['sell', 'buyerProfile.user', 'messages.user'])
            ->firstOrFail();

        return view('seller', compact('trade'));
    }

    public function postSeller(Request $request)
    {
        $message = new Message();
        $message->sell_id = $request->sell_id;
        $message->user_id = Auth::id();
        $message->body = $request->body;

        if ($request->hasFile('image')) {
            $message->image = $request->file('image')->store('trade', 'public');
        }

        $message->save();

        return redirect()->back();

    }

    // 取引チャット（購入者）
    public function getBuyer(Request $request, $tradeId)
    {
        $profile = Profile::where('user_id', Auth::id())->firstOrFail();

        $trade = Trade::where('id', $tradeId)
            ->where('buyer_profile_id', $profile->id)
            ->with(['sell.user', 'messages.user'])
            ->firstOrFail();

        return view('buyer', compact('trade', 'profile'));
    }

    public function postBuyer(Request $request, $tradeId)
    {
        $profile = Profile::where('user_id', Auth::id())->firstOrFail();

        $trade = Trade::where('id', $tradeId)
            ->where('buyer_profile_id', $profile->id)
            ->firstOrFail();

        $message = new Message();
        $message->trade_id = $trade->id;
        $message->user_id = Auth::id();
        $message->body = $request->input('body');

        if ($request->hasFile('image')) {
            $message->image = $request->file('image')->store('trade', 'public');
        }

        $message->save();

        return redirect()->back();
    }
}



