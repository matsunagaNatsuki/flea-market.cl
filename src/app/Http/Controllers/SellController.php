<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Sell;
use App\Models\Buy;
use App\Models\Condition;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\CommentRequest;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

class SellController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'recommend');
        $search = $request->query('search');
        $sells = Sell::where('user_id', '!=', auth()->id());

        if ($tab === 'mylist' && auth()->check()) {
            $sells->whereIn('id', function ($sells) {
                $sells->select('sell_id')
                    ->from('likes')
                    ->where('user_id', auth()->id());
            });
        }

        if ($search) {
            $sells->where('name', 'LIKE', "%{$search}%");
        }

        $sells = $sells->get();

        return view('index', compact('sells', 'search','tab'));
    }

    public function item($sell_id) {
        $sell = Sell::with(['user.profile','category', 'condition','comments.user.profile',])->findOrFail($sell_id);

        return view('item', compact('sell'));
    }

    public function sell(Request $request){
        $images = Storage::files('public/images');
        $categories = Category::all();
        $conditions = Condition::all();

        return view('sell', compact('categories', 'conditions', 'images'));
    }

    public function store(ExhibitionRequest $request) {
        $image = $request->file('image');

        if ($image) {
            $fileName = 'profile_' . uniqid() . '.' . $image->extension();
            $path = $image->storeAs('profiles', $fileName, 'public');
            $imagePath = $path;
        } else {
            $imagePath = null;
        }

        $sell = Sell::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'brand' => $request->brand,
            'image' => $imagePath,
            'condition_id' => $request->condition_id,
            'category_id' => $request->category_id,
            'user_id' => auth()->id()
        ]);

        $sell->categories()->attach($request->category_id);

        return redirect('/');
    }

    public function purchase($item_id) {
        $sell = Sell::with('user')->findOrFail($item_id);
        $user = auth()->user()->load('profile');

        return view('purchase', compact('sell','user'));
    }

    public function buy(PurchaseRequest $request, $item_id) {
        $item = Sell::findOrFail($item_id);
        $stripe = new StripeClient(config('stripe.secret_key'));

        [
            $user_id,
            $sell_id,
            $buy_postal_code,
            $buy_address,
            $buy_building
        ] = [
            Auth::id(),
            $item->id,
            $request->buy_postal_code,
            urlencode($request->buy_address),
            $request->buy_building ? urlencode($request->buy_building) : null,
        ];

        $checkout_session = $stripe->checkout->sessions->create([
            'payment_method_types' => [$request->payment_method],
            'payment_method_options' => [
                'konbini' => [
                    'expires_after_days' => 7,
                ],
            ],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $item->name],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => "http://localhost/purchase/{$item_id}/success"
                . "?user_id={$user_id}"
                . "&sell_id={$sell_id}"
                . "&buy_postal_code={$buy_postal_code}"
                . "&buy_address={$buy_address}"
                . "&buy_building={$buy_building}",
        ]);

        return redirect($checkout_session->url);
    }

    public function success($item_id, Request $request){
        $user_id          = $request->query('user_id');
        $sell_id           = $request->query('sell_id');
        $buy_postal_code = $request->query('buy_postal_code');
        $buy_address  = $request->query('buy_address');
        $buy_building = $request->query('buy_building');

        if (is_null($user_id) || is_null($sell_id) || is_null($buy_postal_code) || is_null($buy_address)) {
            return redirect('/')
                ->with('flashError', '決済後の情報取得に失敗しました。申し訳ありませんが、もう一度やり直してください。');
        }

        $buy_address = urldecode($buy_address);
        $buy_building = $buy_building ? urldecode($buy_building) : null;

        Buy::create([
            'user_id' => $user_id,
            'sell_id' => $item_id,
            'buy_postal_code' => $buy_postal_code,
            'buy_address' => $buy_address,
            'buy_building' => $buy_building ?? null,
        ]);

        return redirect('/')->with('flashSuccess', '決済が完了しました！');
    }

    public function address($item_id)
    {
        $sell = Sell::findOrFail($item_id);
        $user = User::with('profile')->findOrFail(Auth::id());

        return view('address', compact('sell', 'user'));
    }

    public function UpdateAddress(AddressRequest $request, $item_id)
    {
        $user = User::find(Auth::id());
        Profile::where('user_id', $user->id)->update([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building
        ]);

        return redirect("/purchase/{$item_id}");
    }

    public function like(Request $request, $item_id)
    {
        $user = $request->user();
        $sell = Sell::findOrFail($item_id);

        $existingLike = $sell->likes()
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            $sell->likes()->create([
                'user_id' => $user->id,
            ]);
        }

        return back();
    }

    public function comment(CommentRequest $request, $item_id)
    {
        $validated = $request->validated();

        Comment::create([
            'user_id' => $request->user()->id,
            'sell_id' => $item_id,
            'content' => $validated['content'],
        ]);

        return redirect('item/' . $item_id);
    }
}
