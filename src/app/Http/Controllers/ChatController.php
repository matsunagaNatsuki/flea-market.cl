<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sell;
use App\Models\Trade;
use App\Models\Message;
use App\Models\Review;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ChatRequest;
use Illuminate\Support\Facades\Mail;

class ChatController extends Controller
{
    // 取引チャット（出品者）
    public function getSeller(Request $request, $tradeId)
    {
        $profile = Profile::where('user_id', Auth::id())->firstOrFail();

        $trade = Trade::where('id', $tradeId)
            ->where('seller_profile_id', $profile->id)
            ->with(['sell.user', 'messages.user'])
            ->withMax('messages', 'created_at')
            ->firstOrFail();

        Message::join('profiles', 'profiles.user_id', '=', 'trade_messages.user_id')
            ->where('trade_messages.trade_id', $trade->id)
            ->where('profiles.id', $trade->buyer_profile_id)
            ->where('trade_messages.read_by_seller', false)
            ->update(['trade_messages.read_by_seller' => true]);


        $sidebarTrades = Trade::whereIn('status', ['active', 'completed'])
            ->where(function ($query) use ($profile) {
                $query->where('buyer_profile_id', $profile->id)
                    ->orWhere('seller_profile_id', $profile->id);
            })
            ->with('sell')
            ->get();

        $sell = Sell::with('user')->findOrFail($trade->sell_id);

        $sellerHasReviewed = Review::where('trade_id', $trade->id)
            ->where('from_user_id', $profile->id)
            ->exists();

        $shouldOpenCompleteModal = ($trade->status === 'completed') && (!$sellerHasReviewed);


        return view('seller', compact('trade', 'sell','profile', 'sidebarTrades', 'sellerHasReviewed', 'shouldOpenCompleteModal'));
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

        Message::join('profiles', 'profiles.user_id', '=', 'trade_messages.user_id')
            ->where('trade_messages.trade_id', $trade->id)
            ->where('profiles.id', $trade->seller_profile_id)
            ->where('trade_messages.read_by_buyer', false)
            ->update(['trade_messages.read_by_buyer' => true]);

        $sidebarTrades = Trade::whereIn('status', ['active', 'completed'])
            ->where(function ($query) use ($profile) {
                $query->where('buyer_profile_id', $profile->id)
                    ->orWhere('seller_profile_id', $profile->id);
            })
            ->with('sell')
            ->get();

        $sell = Sell::with('user')->findOrFail($trade->sell_id);

        return view('buyer', compact('trade', 'profile', 'sell', 'sidebarTrades'));
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

    // 出品者用の評価レビュー
    public function sellerReview(Request $request, $tradeId)
    {
        $sellerProfile = Profile::where('user_id', Auth::id())->firstOrFail();

        $trade = Trade::where('id', $tradeId)
            ->where('seller_profile_id', $sellerProfile->id)
            ->firstOrFail();

        Review::updateOrCreate(
            ['trade_id' => $trade->id, 'from_user_id' => $sellerProfile->id],
            ['to_user_id' => $trade->buyer_profile_id, 'score' => (int)$request->score]
        );

        return redirect('/');
    }

    //購入者用の評価レビュー
    public function buyerReview(Request $request, $tradeId)
    {
        $buyerProfile = Profile::where('user_id', Auth::id())->firstOrFail();

        $trade = Trade::where('id', $tradeId)
            ->where('buyer_profile_id', $buyerProfile->id)
            ->with(['sell.user'])
            ->firstOrFail();

        Review::updateOrCreate(
            [
                'trade_id' => $trade->id,
                'from_user_id' => $buyerProfile->id,
            ],
            [
                'to_user_id' => $trade->seller_profile_id,
                'score' => (int)$request->input('score'),
            ]
        );

        $trade->update(['status' => 'completed']);
        $sellerEmail = $trade->sell->user->email;

        Mail::raw(
            "購入者が取引を完了しました。\n商品: {$trade->sell->name}\n価格: ¥" . number_format($trade->sell->price),
            function ($message) use ($sellerEmail) {
                $message->to($sellerEmail)
                    ->subject('【取引完了】購入者が取引を完了しました');
            }
        );

        return redirect('/');
    }
}
