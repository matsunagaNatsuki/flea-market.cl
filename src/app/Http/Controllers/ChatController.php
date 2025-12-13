<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sell;
use App\Models\Trade;
use App\Models\Message;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ChatRequest;

class ChatController extends Controller
{
    // 取引チャット（出品者）
    public function getSeller(Request $request, $tradeId)
    {
        $profile = Profile::where('user_id', Auth::id())->firstOrFail();

        $trade = Trade::where('id', $tradeId)
            ->where('seller_profile_id', $profile->id)
            ->with(['sell.user', 'messages.user'])
            ->firstOrFail();

        $sell = Sell::with('user')->findOrFail($trade->sell_id);

        return view('seller', compact('trade', 'sell','profile'));
    }

    public function postSeller(ChatRequest $request, $tradeId)
    {
        $profile = Profile::where('user_id', Auth::id())->firstOrFail();

        $trade = Trade::where('id', $tradeId)
            ->where('seller_profile_id', $profile->id)
            ->firstOrFail();

        $message = new Message();
        $message->trade_id = $trade->id;
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

        $sell = Sell::with('user')->findOrFail($trade->sell_id);

        return view('buyer', compact('trade', 'profile', 'sell'));
    }

    public function postBuyer(ChatRequest $request, $tradeId)
    {
        $profile = Profile::where('user_id', Auth::id())->firstOrFail();

        $trade = Trade::where('id', $tradeId)
            ->where('buyer_profile_id', $profile->id)
            ->with(['messages.user'])
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

    // 削除機能
    public function destroy(Message $message)
    {
        if ($message->user_id !== auth()->id()) {
            abort(403);
        } else {
            $message->delete();
            return redirect()->back();
        }
    }

    // 編集機能
    public function update(ChatRequest $request, Message $message)
    {
        if ($message->user_id !== auth()->id()) {
            abort(403);
        } else {
            $message->body = $request->input('body');

            if ($request->hasFile('image')) {
                $message->image = $request->file('image')->store('trade', 'public');
            }

            $message->save();

            $myProfileId = Profile::where('user_id', auth()->id())->value('id');

            if ($message->trade->seller_profile_id === $myProfileId) {
                return redirect()->route('get.seller', $message->trade_id);
            }

            return redirect()->route('get.buyer', $message->trade_id);
        }
    }
}
