<?php

namespace App\Http\ApiControllers;

use App\Http\Resources\Resource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{

    public function index ()
    {
        $collection = ProductCategory::filter()->collective();

        return Resource::collection($collection);
    }

    public function show ($id)
    {
        $record = ProductCategory::findOrFail($id);

        return new Resource($record);
    }

    public function save (Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:product_categories,id',
            'name' => 'required',
        ]);

        $record = ProductCategory::firstOrNew(['id' => $request->get('id')]);

        $row = $request->only(['name']);

        $record->fill($row);

        $record->save();

        return response()->json([
            "data" => new Resource($record),
            "message" => "Product Category has been saved."
        ]);
    }

    public function delete ($id)
    {
        $record = ProductCategory::findOrFail($id);

        $record->delete();

        return response()->json([
            "message" => "The record hass been deleted."
        ]);
    }
}
