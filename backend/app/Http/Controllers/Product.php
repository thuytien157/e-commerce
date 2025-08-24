<?php

namespace App\Http\Controllers;

use App\Models\Category as ModelsCategory;
use App\Models\Product as ModelsProduct;
use App\Models\Variant as ModelsVariant;
use App\Models\VariantAttribute;
use App\Models\VariantImage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Product extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortBy = $request->query('sort');
        $categoryIds = $request->query('categories');
        $rating = $request->query('rating');
        $price = $request->query('price');
        $perPage = $request->query('per_page', 9);
        $baseQuery = ModelsProduct::with('variants.attributes', 'reviews');

        if ($categoryIds && $categoryIds !== 'all') {
            $selectedCategoryIds = explode(',', $categoryIds);
            $allCategoryIdsToFilter = collect($selectedCategoryIds);
            foreach ($selectedCategoryIds as $id) {
                $category = ModelsCategory::find($id);
                if ($category) {
                    $childIds = $category->getAllChildIds();
                    $allCategoryIdsToFilter = $allCategoryIdsToFilter->merge($childIds);
                }
            }
            $baseQuery->whereIn('category_id', $allCategoryIdsToFilter->unique()->toArray());
        }

        foreach ($request->query() as $key => $value) {
            if (str_starts_with($key, 'attribute_')) {
                $attributeId = (int) str_replace('attribute_', '', $key);
                $selectedValues = explode(',', $value);

                $baseQuery->whereHas('variants.attributes', function ($attQuery) use ($attributeId, $selectedValues) {
                    $attQuery->where('attribute_id', $attributeId)
                        ->whereIn('value_name', $selectedValues);
                });
            }
        }

        if ($rating) {
            $baseQuery->withAvg('reviews', 'rating');
            switch ($rating) {
                case '5':
                    $baseQuery->having('reviews_avg_rating', '=', 5);
                    break;
                case '4_5':
                    $baseQuery->having('reviews_avg_rating', '>=', 4)->having('reviews_avg_rating', '<', 5);
                    break;
                case '3_4':
                    $baseQuery->having('reviews_avg_rating', '>=', 3)->having('reviews_avg_rating', '<', 4);
                    break;
                case 'duoi_3':
                    $baseQuery->having('reviews_avg_rating', '<', 3);
                    break;
            }
        }

        if ($price) {
            $priceFilters = explode(',', $price);
            $baseQuery->whereHas('variants', function ($q) use ($priceFilters) {
                $q->where(function ($query) use ($priceFilters) {
                    foreach ($priceFilters as $filter) {
                        switch ($filter) {
                            case '1':
                                $query->orWhere('price', '<', 1000000);
                                break;
                            case '1_2':
                                $query->orWhereBetween('price', [1000000, 2000000]);
                                break;
                            case '2_3':
                                $query->orWhereBetween('price', [2000000, 3000000]);
                                break;
                            case '3_4':
                                $query->orWhereBetween('price', [3000000, 4000000]);
                                break;
                            case 'tren_4':
                                $query->orWhere('price', '>', 4000000);
                                break;
                        }
                    }
                });
            });
        }

        switch ($sortBy) {
            case 'name_asc':
                $baseQuery->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $baseQuery->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $baseQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $baseQuery->orderBy('price', 'desc');
                break;
            default:
                break;
        }

        $products = $baseQuery
        ->select('products.*')
        ->groupBy('products.id')
        ->paginate($perPage);

        $transformedProducts = $this->transformProducts($products->items());

        $newArrivals = ModelsProduct::with('variants.attributes')
        ->latest('created_at')
        ->take(4)
        ->get();
    $transformedNewArrivals = $this->transformProducts($newArrivals);

    // Sửa lỗi: Tạo một truy vấn mới cho Best Sellers
    $bestSellers = ModelsProduct::with('variants.attributes', 'orderItems')
        ->withSum('orderItems', 'quantity')
        ->orderByDesc('order_items_sum_quantity')
        ->take(8)
        ->get();
    $transformedBestSellers = $this->transformProducts($bestSellers);

    // Sửa lỗi: Tạo một truy vấn mới cho Top Rated
    $topRatedProducts = ModelsProduct::with('variants.attributes', 'reviews')
        ->withAvg('reviews as average_rating', 'rating')
        ->orderByDesc('average_rating')
        ->take(4)
        ->get();
        $transformedTopRated = $this->transformProducts($topRatedProducts);

        return response()->json([
            'products' => $transformedProducts,
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
            'new_arrivals' => $transformedNewArrivals,
            'best_sellers' => $transformedBestSellers,
            'top_rated' => $transformedTopRated
        ]);
    }

    private function transformProducts($products)
    {
        if ($products instanceof PaginationLengthAwarePaginator) {
            $products = $products->getCollection();
        } elseif (is_array($products)) {
            $products = collect($products);
        }
        return $products->map(function ($item) {
            $reviewData = $item->reviews->map(function ($value) {
                return [
                    'customer_name' => $value->customer->username,
                    'customer_avatar' => $value->customer->avatar,
                    'comment' => $value->comment,
                    'reply_text' => $value->reply_text,
                    'image' => $value->images,
                    'rating' => $value->rating,
                    'status' => $value->status,
                ];
            });

            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'rating' => round($item->reviews->avg('rating'), 1),
                'reviews' => $reviewData,
                'description' => $item->description,
                'status' => $item->status,
                'category_id' => $item->category_id,
                'category_name' => optional($item->category)->name,
                'variants' => $item->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'image' => $variant->main_image_url,
                        'images' => $variant->variantImages,
                        'slug' => $variant->slug,
                        'sku' => $variant->sku,
                        'order_count' => $variant->orderItems()->count(),
                        'stock_quantity' => $variant->stock_quantity,
                        'attributes' => $variant->attributes->map(function ($attr) {
                            return [
                                'attribute_id' => $attr->attribute_id,
                                'attribute_name' => $attr->value_name,
                                'attribute_name_display' => $attr->attribute->name,
                                'attribute_value_id' => $attr->pivot->attribute_value_id,
                            ];
                        }),
                    ];
                }),

            ];
        })->filter()->values();
    }


    function getProductById(Request $request, string $id)
    {
        $rating = $request->query('rating');

        $product = ModelsProduct::with(['variants.attributes'])
            ->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Sản phẩm không tồn tại.'
            ], 404);
        }

        $reviewsQuery = $product->reviews()->with(['customer', 'admin']);

        if ($rating) {
            switch ($rating) {
                case '5':
                    $reviewsQuery->where('rating', '=', 5);
                    break;
                case '4_5':
                    $reviewsQuery->where('rating', '>=', 4)->where('rating', '<', 5);
                    break;
                case '3_4':
                    $reviewsQuery->where('rating', '>=', 3)->where('rating', '<', 4);
                    break;
                case 'duoi_3':
                    $reviewsQuery->where('rating', '<', 3);
                    break;
            }
        }

        $filteredReviews = $reviewsQuery->get();
        $product->setRelation('reviews', $filteredReviews);
        $transformedProduct = $this->transformProducts(collect([$product]))->first();

        return response()->json([
            'product' => $transformedProduct
        ]);
    }

    function getVariantById($id){
        $variant = ModelsVariant::find($id);
        if(!$variant){
            return response()->json([
                'mess' => 'Biến thể không tồn tại'
            ], 404);
        }

        return response()->json([
            'variant' => $variant
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:1',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required',
                'variants' => 'required|array',
                'variants.*.sku' => 'required|string|max:255|unique:variants,sku',
                'variants.*.stock_quantity' => 'required|numeric|min:1',
                'variants.*.slug' => 'required|string|max:255|unique:variants,slug',
                'variants.*.image' => 'required|image|max:2048',
                'variants.*.attributes' => 'required|array',
                'variants.*.attributes.*' => 'required|numeric|exists:attribute_values,id',
                'variants.*.images' => 'nullable|array',
                'variants.*.images.*' => 'nullable|image|max:2048',
            ],
            [
                'name.required' => 'Vui lòng nhập tên sản phẩm.',
                'description.required' => 'Vui lòng nhập mô tả sản phẩm.',
                'price.required' => 'Vui lòng nhập giá sản phẩm.',
                'price.min' => 'Giá không hợp lệ.',
                'price.numeric' => 'Giá phải là một số.',
                'category_id.required' => 'Vui lòng chọn danh mục.',
                'category_id.exists' => 'Danh mục không tồn tại.',
                'status.required' => 'Vui lòng chọn trạng thái.',
                'variants.required' => 'Vui lòng thêm ít nhất một biến thể.',
                'variants.array' => 'Dữ liệu biến thể không hợp lệ.',
                'variants.*.stock_quantity.required' => 'Vui lòng nhập tồn kho.',
                'variants.*.stock_quantity.min' => 'Tồn kho phải lớn hơn 1.',
                'variants.*.sku.required' => 'Vui lòng nhập SKU.',
                'variants.*.sku.unique' => 'SKU đã tồn tại.',
                'variants.*.slug.required' => 'Vui lòng nhập slug.',
                'variants.*.slug.unique' => 'Slug đã tồn tại.',
                'variants.*.image.required' => 'Vui lòng chọn ảnh chính cho biến thể.',
                'variants.*.image.image' => 'File ảnh chính không hợp lệ.',
                'variants.*.image.max' => 'Ảnh chính quá lớn (tối đa 2MB).',
                'variants.*.images.*.image' => 'File ảnh chi tiết không hợp lệ.',
                'variants.*.images.*.max' => 'Một trong các ảnh bổ sung quá lớn (tối đa 2MB).',
                'variants.*.attributes.required' => 'Vui lòng chọn ít nhất một thuộc tính cho biến thể.',
                'variants.*.attributes.*.exists' => 'Một trong các giá trị thuộc tính không tồn tại.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $product = new ModelsProduct();
            $product->name = $request->name;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->status = $request->status;

            $product->save();

            $files = $request->file('variants');

            if ($request->has('variants')) {
                foreach ($request->variants as $index => $variantData) {
                    $variant = new ModelsVariant();
                    $variant->product_id = $product->id;
                    $variant->sku = $variantData['sku'];
                    $variant->stock_quantity = $variantData['stock_quantity'];
                    $variant->slug = $variantData['slug'];

                    if (isset($files[$index]['image'])) {
                        $uploadedMainImageUrl = Cloudinary::upload($files[$index]['image'])->getSecurePath();
                        $variant->main_image_url = $uploadedMainImageUrl;
                    }

                    $variant->save();

                    if (isset($variantData['attributes'])) {
                        foreach ($variantData['attributes'] as $attributeValueId) {
                            $variantAttribute = new VariantAttribute();
                            $variantAttribute->variant_id = $variant->id;
                            $variantAttribute->attribute_value_id = $attributeValueId;
                            $variantAttribute->save();
                        }
                    }

                    if (isset($files[$index]['images'])) {
                        foreach ($files[$index]['images'] as $imgFile) {
                            $uploadedImageUrl = Cloudinary::upload($imgFile)->getSecurePath();
                            $variantImage = new VariantImage();
                            $variantImage->variant_id = $variant->id;
                            $variantImage->image_url = $uploadedImageUrl;
                            $variantImage->save();
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'mess' => 'Thêm sản phẩm thành công',
                'product' => $product,
            ], 201);
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                'mess' => 'Đã xảy ra lỗi: ' . $th->getMessage()
            ], 500);
        }
    }

    function hiddenProduct($id, Request $request)
    {
        $product = ModelsProduct::find($id);
        if (!$product) {
            return response()->json([
                'mess' => 'Sản phẩm không tồn tại'
            ]);
        }

        $product->status = $request->status;
        if ($product->save()) {
            return response()->json([
                'mess' => 'Cập nhật thành công'
            ]);
        };
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = ModelsProduct::find($id);
        if (!$product) {
            return response()->json(['mess' => 'Sản phẩm không tồn tại'], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:1',
                'description' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required',
                'variants' => 'required|array',
                'variants.*.sku' => 'required|string|max:255|unique:variants,sku,' . $id . ',product_id',
                'variants.*.stock_quantity' => 'required|numeric|min:1',
                'variants.*.slug' => 'required|string|max:255|unique:variants,slug,' . $id . ',product_id',
                'variants.*.image' => 'required_without:variants.*.image_url|nullable|image|max:2048',
                'variants.*.image_url' => 'nullable|string',
                'variants.*.attributes' => 'required|array',
                'variants.*.attributes.*' => 'required|numeric|exists:attribute_values,id',
                'variants.*.images' => 'nullable|array',
                'variants.*.images.*' => 'nullable|image|max:2048',
                'variants.*.image_urls' => 'nullable|array',
                'variants.*.image_urls.*' => 'nullable|string',
            ],
            [
                'name.required' => 'Vui lòng nhập tên sản phẩm.',
                'description.required' => 'Vui lòng nhập mô tả sản phẩm.',
                'price.required' => 'Vui lòng nhập giá sản phẩm.',
                'price.min' => 'Giá không hợp lệ.',
                'price.numeric' => 'Giá phải là một số.',
                'category_id.required' => 'Vui lòng chọn danh mục.',
                'category_id.exists' => 'Danh mục không tồn tại.',
                'status.required' => 'Vui lòng chọn trạng thái.',
                'variants.required' => 'Vui lòng thêm ít nhất một biến thể.',
                'variants.array' => 'Dữ liệu biến thể không hợp lệ.',
                'variants.*.sku.required' => 'Vui lòng nhập SKU.',
                'variants.*.stock_quantity.required' => 'Vui lòng nhập tồn kho.',
                'variants.*.stock_quantity.min' => 'Tồn kho phải lớn hơn 1.',
                'variants.*.sku.unique' => 'SKU đã tồn tại.',
                'variants.*.slug.required' => 'Vui lòng nhập slug.',
                'variants.*.slug.unique' => 'Slug đã tồn tại.',
                'variants.*.image.required_without' => 'Vui lòng chọn ảnh chính cho biến thể.',
                'variants.*.image.image' => 'File ảnh chính không hợp lệ.',
                'variants.*.image.max' => 'Ảnh chính quá lớn (tối đa 2MB).',
                'variants.*.images.*.image' => 'File ảnh chi tiết không hợp lệ.',
                'variants.*.images.*.max' => 'Một trong các ảnh bổ sung quá lớn (tối đa 2MB).',
                'variants.*.attributes.required' => 'Vui lòng chọn ít nhất một thuộc tính cho biến thể.',
                'variants.*.attributes.*.exists' => 'Một trong các giá trị thuộc tính không tồn tại.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $product->name = $request->name;
            $product->price = $request->price;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->status = $request->status;
            $product->save();

            $existingVariantIds = $product->variants()->pluck('id')->toArray();
            $sentVariantIds = collect($request->variants)->pluck('id')->filter()->toArray();
            $variantsToDelete = array_diff($existingVariantIds, $sentVariantIds);
            $variantsCanDelete = ModelsVariant::whereIn('id', $variantsToDelete)
                ->whereDoesntHave('orderItems')
                ->with('variantImages')
                ->get();

            foreach ($variantsCanDelete as $variant) {
                if ($variant->main_image_url) {
                    Cloudinary::destroy(pathinfo($variant->main_image_url, PATHINFO_FILENAME));
                }
                foreach ($variant->variantImages as $image) {
                    Cloudinary::destroy(pathinfo($image->image_url, PATHINFO_FILENAME));
                }
                $variant->delete();
            }

            foreach ($request->variants as $index => $variantData) {
                if (isset($variantData['id']) && $variantData['id']) {
                    $variant = ModelsVariant::find($variantData['id']);
                } else {
                    $variant = new ModelsVariant();
                    $variant->product_id = $product->id;
                }

                if (!$variant->exists || $variant->order_count == 0) {
                    $variant->sku = $variantData['sku'];
                    $variant->slug = $variantData['slug'];
                    $variant->stock_quantity = $variantData['stock_quantity'];
                }

                if ($request->hasFile("variants.$index.image")) {
                    if ($variant->main_image_url) {
                        Cloudinary::destroy(pathinfo($variant->main_image_url, PATHINFO_FILENAME));
                    }
                    $variant->main_image_url = Cloudinary::upload($request->file("variants.$index.image"))->getSecurePath();
                } elseif (isset($variantData['image_url'])) {
                    $variant->main_image_url = $variantData['image_url'];
                } else {
                    $variant->main_image_url = null;
                }

                $variant->save();

                $variant->attributes()->delete();
                foreach ($variantData['attributes'] as $attributeValueId) {
                    $variantAttribute = new VariantAttribute();
                    $variantAttribute->variant_id = $variant->id;
                    $variantAttribute->attribute_value_id = $attributeValueId;
                    $variantAttribute->save();
                }

                $sentImageUrls = $request->input("variants.$index.image_urls", []);

                if ($request->hasFile("variants.$index.images")) {
                    foreach ($request->file("variants.$index.images") as $imgFile) {
                        $imageUrl = Cloudinary::upload($imgFile)->getSecurePath();
                        $sentImageUrls[] = $imageUrl;
                    }
                }

                $existingImages = $variant->variantImages;
                $existingImageUrls = $existingImages->pluck('image_url')->toArray();

                $imagesToDeleteFromDb = array_diff($existingImageUrls, $sentImageUrls);
                if (!empty($imagesToDeleteFromDb)) {
                    $images = $existingImages->whereIn('image_url', $imagesToDeleteFromDb);
                    foreach ($images as $image) {
                        Cloudinary::destroy(pathinfo($image->image_url, PATHINFO_FILENAME));
                        $image->delete();
                    }
                }

                $imagesToCreate = array_diff($sentImageUrls, $existingImageUrls);
                if (!empty($imagesToCreate)) {
                    foreach ($imagesToCreate as $imageUrl) {
                        $variant->variantImages()->create(['image_url' => $imageUrl]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'mess' => 'Cập nhật sản phẩm thành công',
                'product' => $product,
            ], 200);
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                'mess' => 'Đã xảy ra lỗi: ' . $th->getMessage()
            ], 500);
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
