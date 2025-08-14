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

        $baseQuery = ModelsProduct::with('variants.attributes', 'reviews');

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

        $products = (clone $baseQuery)->groupBy('products.id')->get();
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
                'price' => $item->price,
                'rating' => $rating,
                'reviews' => $reviewsData,
                'description' => $item->description,
                'variants' => $item->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
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
