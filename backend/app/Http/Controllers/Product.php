<?php

namespace App\Http\Controllers;

use App\Models\Category as ModelsCategory;
use App\Models\Product as ModelsProduct;
use App\Models\Variant as ModelsVariant;
use Illuminate\Http\Request;

class Product extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortBy = $request->query('sort');
        $categoryIds = $request->query('categories');
        $colors = $request->query('colors');
        $sizes = $request->query('sizes');
        $rating = $request->query('rating');
        $price = $request->query('price');

        // Tạo một query cơ sở và áp dụng tất cả các bộ lọc vào nó.
        $baseQuery = ModelsProduct::with('variants.attributes', 'reviews');

        // Áp dụng bộ lọc giá và sắp xếp giá
        if ($price || in_array($sortBy, ['price_asc', 'price_desc'])) {
            $baseQuery->join('variants', 'products.id', '=', 'variants.product_id')
                ->select('products.*', 'variants.price');
        }

        // Áp dụng bộ lọc sắp xếp (tạm thời không sắp xếp mặc định ở đây)
        switch ($sortBy) {
            case 'name_asc':
                $baseQuery->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $baseQuery->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $baseQuery->orderBy('variants.price', 'asc');
                break;
            case 'price_desc':
                $baseQuery->orderBy('variants.price', 'desc');
                break;
            // Loại bỏ sắp xếp latest() ở đây để mỗi danh sách có thể tự sắp xếp.
            default:
                break;
        }

        // Áp dụng bộ lọc danh mục
        // Thêm điều kiện $categoryIds !== 'all' để không lọc khi chọn "Tất cả"
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

        // Áp dụng bộ lọc màu sắc, kích thước, đánh giá và giá (giữ nguyên logic của bạn)
        if ($colors || $sizes) {
            $baseQuery->whereHas('variants', function ($q) use ($colors, $sizes) {
                $q->whereHas('attributes', function ($attValQuery) use ($colors, $sizes) {
                    $attValQuery->where(function ($query) use ($colors) {
                        if ($colors) {
                            $colorValues = explode(',', $colors);
                            $query->whereIn('value_name', $colorValues)
                                ->whereHas('attribute', function ($attQuery) {
                                    $attQuery->where('id', 2);
                                });
                        }
                    })->where(function ($query) use ($sizes) {
                        if ($sizes) {
                            $sizeValues = explode(',', $sizes);
                            $query->whereIn('value_name', $sizeValues)
                                ->whereHas('attribute', function ($attQuery) {
                                    $attQuery->where('id', 1);
                                });
                        }
                    });
                });
            });
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
            $baseQuery->where(function ($q) use ($priceFilters) {
                foreach ($priceFilters as $filter) {
                    switch ($filter) {
                        case '1':
                            $q->orWhere('variants.price', '<', 1000000);
                            break;
                        case '1_2':
                            $q->orWhereBetween('variants.price', [1000000, 2000000]);
                            break;
                        case '3_4':
                            $q->orWhereBetween('variants.price', [3000000, 4000000]);
                            break;
                        case 'tren_4':
                            $q->orWhere('variants.price', '>', 4000000);
                            break;
                    }
                }
            });
        }
        $newArrivals = (clone $baseQuery)->latest('created_at')->take(4)->get();

        $variantsWithSales = (clone $baseQuery)
            ->withSum('orderItems', 'quantity')
            ->orderByDesc('order_items_sum_quantity')
            ->take(8)
            ->get();

        $topRatedProducts = (clone $baseQuery)
            ->withAvg('reviews as average_rating', 'rating')
            ->orderByDesc('average_rating')
            ->take(4)
            ->get();

        $products = (clone $baseQuery)->get();


        $transformedProducts = $this->transformProducts($products);
        $transformedNewArrivals = $this->transformProducts($newArrivals);
        $transformedVariantsWithSales = $this->transformProducts($variantsWithSales);
        $transformedTopRated = $this->transformProducts($topRatedProducts);

        return response()->json([
            'products' => $transformedProducts,
            'new_arrivals' => $transformedNewArrivals,
            'best_sellers' => $transformedVariantsWithSales,
            'top_rated' => $transformedTopRated
        ]);
    }

    private function transformProducts($products)
    {
        return $products->map(function ($item) {
            $reviews = $item->reviews;
            $reviewsData = $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'user_name' => optional($review->user)->name,
                    'created_at' => $review->created_at,
                ];
            });
            $rating = $item->reviews_avg_rating ?? round($reviews->avg('rating'), 1);

            return [
                'id' => $item->id,
                'name' => $item->name,
                'rating' => $rating,
                'reviews' => $reviewsData,
                'description' => $item->description,
                'variants' => $item->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'price' => $variant->price,
                        'image' => $variant->main_image_url,
                        'images' => $variant->variantImages,
                        'slug' => $variant->slug,
                        'attributes' => $variant->attributes->map(function ($attr) {
                            return [
                                'attribute_id' => $attr->attribute_id,
                                'attribute_name' => $attr->value_name,
                                'attribute_value_id' => $attr->pivot->attribute_value_id,
                            ];
                        }),
                    ];
                }),
                'category_name' => optional($item->category)->name
            ];
        })->filter()->values();
    }



    function getProductById(string $id)
    {
        $product = ModelsProduct::with('variants.attributes', 'reviews')
            ->withAvg('reviews', 'rating')
            ->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Sản phẩm không tồn tại'
            ], 404);
        }

        // Nếu muốn dùng format giống index()
        $transformed = $this->transformProducts(collect([$product]))->first();

        return response()->json([
            'product' => $transformed
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
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
