<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\Buy;
use App\Models\Sell;
use App\Models\Trade;
use App\Models\Review;
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

            $profile = Profile::where('user_id', $user->id)->firstOrFail();

            $items = Trade::where('status', 'active')
                ->where(function ($query) use ($profile) {
                    $query->where('seller_profile_id', $profile->id)
                        ->orWhere('buyer_profile_id', $profile->id);
                })
                ->with('sell')
                ->withCount('messages')
                ->withCount([
                    'messages as unread_count' => function ($query) use ($profile) {
                        $query->where(function ($query) use ($profile) {
                            $query ->whereColumn('user_id', '!=', 'trades.seller_profile_id');
                        })
                            ->orWhere(function ($query) use ($profile) {
                                $query->where('read_by_buyer', false)
                                    ->whereColumn('user_id', '!=', 'trades.buyer_profile_id');
                            });
                    }
                ])
                ->withMax('messages', 'created_at')
                ->orderByDesc('messages_max_created_at')
                ->get();
        } else {
            $page = 'sell';
            $items = Sell::where('user_id', $user->id)
                ->latest()
                ->get();
        }

        $tradeMessageCount = Trade::where('status', 'active')
            ->whereHas('sell', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->withCount([
            'messages as unread_count' => function ($query) {
                $query->where('read_by_seller', false)
                      ->whereColumn('user_id', '!=', 'trades.seller_profile_id');
                }
            ])
            ->get()
            ->sum('unread_count');

        $reviewCount = Review::where('to_user_id', $profile->id)->count();

        $reviewAvg = null;
        if ($reviewCount > 0) {
            $avg = Review::where('to_user_id', $profile->id)->avg('score');
            $reviewAvg = (int) round($avg);
        }

        return view('profile', compact('profile', 'user', 'items', 'page', 'tradeMessageCount', 'reviewCount', 'reviewAvg'));
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

    public function startTrade($sellId)
    {
        $sell = Sell::with('user.profile')->findOrFail($sellId);

        $sellerProfile = $sell->user->profile;
        $buyerProfile = Profile::where('user_id', Auth::id())->firstOrFail();

        $trade = Trade::where('sell_id', $sell->id)
            ->where('buyer_profile_id', $buyerProfile->id)
            ->where('status', 'active')
            ->first();

        if (!$trade) {
            $trade = Trade::create([
                'sell_id'           => $sell->id,
                'seller_profile_id' => $sellerProfile->id,
                'buyer_profile_id'  => $buyerProfile->id,
                'status'            => 'active',
            ]);
        }

        return redirect()->route('get.buyer', $trade->id);
    }

    // tradeタブの未読件数のカウント
    public function unreadCount()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->firstOrFail();

        $sellerSum = Trade::where('status', 'active')
            ->where('seller_profile_id', $profile->id)
            ->withCount([
                'messages as unread_count' => function ($q) {
                    $q->where('read_by_seller', false)
                        ->whereColumn('trade_messages.user_id', '!=', 'trades.seller_profile_id');
                }
            ])
            ->get()
            ->sum('unread_count');

        $buyerSum = Trade::where('status', 'active')
            ->where('buyer_profile_id', $profile->id)
            ->withCount([
                'messages as unread_count' => function ($q) {
                    $q->where('read_by_buyer', false)
                        ->whereColumn('trade_messages.user_id', '!=', 'trades.buyer_profile_id');
                }
            ])
            ->get()
            ->sum('unread_count');

        return response()->json([
            'count' => $sellerSum + $buyerSum
        ]);
    }


    // 商品画像の未読件数のカウント
    public function unreadCountTrades()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->firstOrFail();

        $seller = Trade::where('status', 'active')
            ->where('seller_profile_id', $profile->id)
            ->withCount([
                'messages as unread_count' => function ($q) {
                    $q->where('read_by_seller', false)
                        ->whereColumn('trade_messages.user_id', '!=', 'trades.seller_profile_id');
                }
            ])
            ->get(['id'])
            ->mapWithKeys(fn($t) => [$t->id => (int)$t->unread_count]);

        $buyer = Trade::where('status', 'active')
            ->where('buyer_profile_id', $profile->id)
            ->withCount([
                'messages as unread_count' => function ($q) {
                    $q->where('read_by_buyer', false)
                        ->whereColumn('trade_messages.user_id', '!=', 'trades.buyer_profile_id');
                }
            ])
            ->get(['id'])
            ->mapWithKeys(fn($t) => [$t->id => (int)$t->unread_count]);

        $counts = $seller->union($buyer);

        return response()->json([
            'counts' => $counts
        ]);
    }
}



