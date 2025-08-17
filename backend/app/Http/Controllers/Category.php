<?php

namespace App\Http\Controllers;

use App\Models\Category as ModelsCategory;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as ValidationRule;

class Category extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = ModelsCategory::query()
            ->whereNull('parent_id')
            ->withCount('products')
            ->withCount('children')
            ->with(['children' => function ($query) {
                $query->withCount('products');
            }])->get();


        return response()->json([
            'categories' => $categories
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
                'name' => 'required|string|max:255|unique:categories,name',
                'slug' => 'required|string|unique:categories,slug',
                'parent_id' => 'nullable|exists:categories,id',
                'children_id' => 'nullable|array',
                'children_id.*' => 'exists:categories,id',
            ],
            [
                'name.required' => 'Vui lòng nhập tên',
                'name.unique' => 'Tên danh mục đã tồn tại',
                'slug.unique' => 'Slug đã tồn tại',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $newcategory = new ModelsCategory();
        $newcategory->name = $request->name;
        $newcategory->slug = $request->slug;
        $newcategory->parent_id = $request->parent_id;
        $newcategory->save();
        if ($request->children_id) {
            foreach ($request->children_id as $key => $value) {
                $category = ModelsCategory::find($value);
                $category->parent_id = $newcategory->id;
                $category->save();
            }
        }

        return response()->json([
            'mess' => 'Thêm danh mục thành công'
        ], 201);
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
        $categoryToUpdate = ModelsCategory::find($id);

        if (!$categoryToUpdate) {
            return response()->json([
                'message' => 'Danh mục không tồn tại'
            ], 404);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    ValidationRule::unique('categories', 'name')->ignore($categoryToUpdate->id),
                ],
                'slug' => [
                    'required',
                    'string',
                    ValidationRule::unique('categories', 'slug')->ignore($categoryToUpdate->id),
                ],
                'parent_id' => 'nullable|exists:categories,id',
                'children_id' => 'nullable|array',
                'children_id.*' => 'exists:categories,id',
            ],
            [
                'name.required' => 'Vui lòng nhập tên',
                'name.unique' => 'Tên danh mục đã tồn tại',
                'slug.unique' => 'Slug đã tồn tại',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $categoryToUpdate->name = $request->name;
        $categoryToUpdate->slug = $request->slug;
        $categoryToUpdate->parent_id = $request->parent_id;
        $categoryToUpdate->save();

        if ($request->filled('children_id')) {
            ModelsCategory::where('parent_id', $categoryToUpdate->id)->update(['parent_id' => null]);
            ModelsCategory::whereIn('id', $request->children_id)->update(['parent_id' => $categoryToUpdate->id]);
        } else {
            ModelsCategory::where('parent_id', $categoryToUpdate->id)->update(['parent_id' => null]);
        }

        return response()->json([
            'mess' => 'Cập nhật danh mục thành công'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = ModelsCategory::find($id);
        $category->delete();
        return response()->json([
            'mess' => 'Xoá danh mục thành công'
        ]);
    }
}
