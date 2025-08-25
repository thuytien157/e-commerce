<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Review as ModelsReview;
use App\Models\Review_image;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

SupportCarbon::setLocale('vi');
date_default_timezone_set('Asia/Ho_Chi_Minh');
class Review extends Controller
{
    function createReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_detail_id' => 'required|exists:order_items,id',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'rating.required' => 'Điểm đánh giá là bắt buộc.',
            'rating.numeric' => 'Điểm đánh giá phải là số.',
            'rating.min' => 'Điểm đánh giá phải từ :min sao trở lên.',
            'rating.max' => 'Điểm đánh giá không được quá :max sao.',
            'comment.required' => 'Vui lòng ghi nội dung đánh giá.',
            'comment.max' => 'Nội dung đánh giá không được quá :max ký tự.',
            'images.*.image' => 'File phải là hình ảnh.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg hoặc gif.',
            'images.*.max' => 'Hình ảnh không được vượt quá 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi xác thực dữ liệu.',
                'errors' => $validator->errors()
            ], 422);
        }

        $orderItem = OrderItem::with('variant.product')->find($request->order_detail_id);

        if (!$orderItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Chi tiết đơn hàng không tồn tại.'
            ], 404);
        }

        if ($orderItem->reviewed) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phẩm này đã được đánh giá.'
            ], 409);
        }

        DB::beginTransaction();

        try {
            $review = new ModelsReview();
            $review->customer_id = Auth::id();
            $review->product_id = $orderItem->variant->product->id;
            $review->order_item_id = $orderItem->id;
            $review->rating = (float)$request->rating;
            $review->comment = $request->comment;
            $review->created_at = SupportCarbon::now();
            $review->save();

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $tempPath = $image->getRealPath();

                    $uploadedFileUrl = Cloudinary::upload($tempPath, [
                        'folder' => 'reviews'
                    ])->getSecurePath();

                    $review_image = new Review_image();
                    $review_image->review_id = $review->id;
                    $review_image->image_url = $uploadedFileUrl;
                    $review_image->save();
                }
            }

            $orderItem->update(['reviewed' => true]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Đánh giá của bạn đã được gửi thành công!',
                'data' => $review
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xử lý đánh giá. Vui lòng thử lại.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    function getAllReview()
    {
        $reviews = ModelsReview::with('customer', 'admin', 'images', 'product.variants')->get();

        return response()->json([
            'reviews' => $reviews
        ]);
    }

    function hiddenReview($id, Request $request)
    {
        $review = ModelsReview::find($id);
        if (!$review) {
            return response()->json([
                'mess' => 'Đánh giá không tồn tại'
            ], 404);
        }

        $review->status = 'published';
        if ($review->save()) {
            return response()->json([
                'mess' => 'Cập nhật thành công'
            ]);
        };
    }

    function replyReview($id, Request $request)
    {
        $review = ModelsReview::find($id);
        if (!$review) {
            return response()->json([
                'mess' => 'Đánh giá không tồn tại'
            ], 404);
        }

        $user = Auth::id();
        if (!$user) {
            return response()->json([
                'mess' => 'User ch đăng nhập'
            ], 404);
        }

        $review->reply_text = $request->reply_text;
        $review->reply_customer_id = $user;
        if ($review->save()) {
            return response()->json([
                'mess' => 'Cập nhật thành công'
            ]);
        };
    }
}
