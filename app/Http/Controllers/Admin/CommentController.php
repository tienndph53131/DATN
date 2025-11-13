<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function index()
    {
        $query = Comment::with(['product', 'account']);

        // Filters: q (search in content / product name / account name), status, rating
        if ($q = request('q')) {
            $query->where(function($qr) use ($q) {
                $qr->where('content', 'like', "%{$q}%")
                   ->orWhereHas('product', function($p) use ($q) { $p->where('name', 'like', "%{$q}%"); })
                   ->orWhereHas('account', function($a) use ($q) { $a->where('name', 'like', "%{$q}%"); });
            });
        }

        if (null !== ($status = request('status'))) {
            if ($status !== '') $query->where('status', (int)$status);
        }

        if (null !== ($rating = request('rating'))) {
            if ($rating !== '') $query->where('rating', (int)$rating);
        }

        $comments = $query->orderByDesc('date')->paginate(20)->appends(request()->query());
        return view('admin.comments.index', compact('comments'));
    }

    // Show edit form for a comment
    public function edit($id)
    {
        $comment = Comment::with(['product', 'account'])->findOrFail($id);
        return view('admin.comments.edit', compact('comment'));
    }

    // Toggle/update status
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        // Allow updating content, rating and status from admin
        $data = [];
        if ($request->has('content')) $data['content'] = $request->input('content');
        if ($request->has('rating')) $data['rating'] = (int) $request->input('rating');
        if ($request->has('status')) $data['status'] = (int) $request->input('status');

        if (!empty($data)) {
            $comment->update($data);
            // Log admin action
            Log::info('Admin updated comment', ['id' => $comment->id, 'data' => $data, 'user_id' => auth()->id()]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Cập nhật bình luận thành công.', 'comment' => $comment]);
            }

            return redirect()->route('comments.index')->with('success', 'Cập nhật bình luận thành công.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Không có thay đổi nào được gửi.'], 400);
        }

        return redirect()->route('comments.index')->with('info', 'Không có thay đổi nào được gửi.');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        Log::info('Admin deleted comment', ['id' => $id, 'user_id' => auth()->id()]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa bình luận.','id'=>$id]);
        }

        return redirect()->route('comments.index')->with('success', 'Đã xóa bình luận.');
    }

    // Bulk actions: approve/hide/delete
    public function bulk(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Chưa chọn bình luận nào.'], 400);
            }
            return redirect()->route('comments.index')->with('error', 'Chưa chọn bình luận nào.');
        }

        $comments = Comment::whereIn('id', $ids)->get();
        if ($action === 'approve') {
            foreach ($comments as $c) { $c->update(['status' => 1]); }
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Đã duyệt các bình luận đã chọn.', 'action' => 'approve', 'ids' => $ids]);
            }
            return redirect()->route('comments.index')->with('success', 'Đã duyệt các bình luận đã chọn.');
        }

        if ($action === 'hide') {
            foreach ($comments as $c) { $c->update(['status' => 0]); }
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Đã ẩn các bình luận đã chọn.', 'action' => 'hide', 'ids' => $ids]);
            }
            return redirect()->route('comments.index')->with('success', 'Đã ẩn các bình luận đã chọn.');
        }

        if ($action === 'delete') {
            Comment::whereIn('id', $ids)->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Đã xóa các bình luận đã chọn.', 'action' => 'delete', 'ids' => $ids]);
            }
            return redirect()->route('comments.index')->with('success', 'Đã xóa các bình luận đã chọn.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Hành động không hợp lệ.'], 400);
        }
        return redirect()->route('comments.index')->with('error', 'Hành động không hợp lệ.');
    }
}