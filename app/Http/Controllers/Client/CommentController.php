<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // store comment for a product (requires client auth)
    public function store(Request $request, $productId)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $product = Product::findOrFail($productId);

        $accountId = Auth::guard('client')->id();
        if (!$accountId) {
            return redirect()->route('client.login')->with('error', 'Bạn cần đăng nhập để bình luận.');
        }

        Comment::create([
            'product_id' => $product->id,
            'account_id' => $accountId,
            'content' => $request->input('content'),
            'rating' => (int) $request->input('rating', 0),
            'date' => now(),
            'status' => 1,
        ]);

        return redirect()->back()->with('success', 'Bình luận đã được gửi.');
    }
}