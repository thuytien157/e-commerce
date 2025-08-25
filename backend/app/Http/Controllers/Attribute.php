<?php

namespace App\Http\Controllers;

use App\Models\Attribute as ModelsAttribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Attribute extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = ModelsAttribute::with(['attributeValues' => function ($query) {
            $query->withCount('variants');
        }])->get();

        return response()->json([
            'attributes' => $attributes, // Trả về tất cả thuộc tính
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:attributes,name',
            'type' => 'required|string',
            'values' => 'required|array|min:1',
        ], [
            'name.required' => 'Vui lòng nhập tên!',
            'type.required' => 'Vui lòng nhập loại thuộc tính!',
            'values.required' => 'Vui lòng nhập giá trị thuộc tính!',
            'values.min' => 'Vui lòng thêm ít nhất một giá trị thuộc tính.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $attribute = new ModelsAttribute();
        $attribute->name = $request->name;
        $attribute->type = $request->type;

        if ($attribute->save()) {
            foreach ($request->values as $key => $value) {
                $attribute_value = new AttributeValue();
                $attribute_value->attribute_id = $attribute->id;
                $attribute_value->value_name = $value;
                $attribute_value->save();
            }

            return response()->json([
                'mess' => 'Thêm thành công'
            ], 201);
        }
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
        $attribute = ModelsAttribute::with(['attributeValues' => function ($query) {
            $query->withCount('variants');
        }])->find($id);

        return response()->json([
            'attribute' => $attribute
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:attributes,name,' . $id,
            'type' => 'required|string',
            'values' => 'required|array|min:1',
        ], [
            'name.required' => 'Vui lòng nhập tên!',
            'type.required' => 'Vui lòng nhập loại thuộc tính!',
            'values.required' => 'Vui lòng nhập giá trị thuộc tính!',
            'values.min' => 'Vui lòng thêm ít nhất một giá trị thuộc tính.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $attribute = ModelsAttribute::find($id);
        $attribute->name = $request->name;
        $attribute->type = $request->type;
        $attribute->save();

        $newValues = $request->values;

        $existingValues = $attribute->attributeValues()->pluck('value_name', 'id')->toArray();

        foreach ($newValues as $valueName) {
            $attributeValue = $attribute->attributeValues()->firstOrCreate(
                ['value_name' => $valueName],
                ['attribute_id' => $attribute->id]
            );
        }

        foreach ($attribute->attributeValues as $attrValue) {
            if (!in_array($attrValue->value_name, $newValues)) {
                if ($attrValue->variants()->count() == 0) {
                    $attrValue->delete();
                }
            }
        }

        return response()->json([
            'mess' => 'Sửa thành công'
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attribute = ModelsAttribute::find($id);
        $attribute->delete();
        return response()->json([
            'mess' => 'Xoá thuộc tính thành công'
        ]);
    }
    public function deleteAttributeValue(string $id)
    {
        $attribute = AttributeValue::find($id);
        $attribute->delete();
        return response()->json([
            'mess' => 'Xoá thuộc tính thành công'
        ]);
    }
}
