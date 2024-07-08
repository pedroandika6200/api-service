<?php

namespace App\Http\ApiControllers;

use App\Http\Filters\ProductFilter;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index (ProductFilter $filter)
    {
        $collection = Product::filter($filter)->collective();

        return ProductResource::collection($collection);
    }

    public function show ($id)
    {
        $record = Product::findOrFail($id);

        return new ProductResource($record);
    }

    public function save (Request $request)
    {
        if (!$request->has('id'))
        {
            $id = Product::searchKey($request->get('sku'))->first('id')?->id;
            $request->merge(['id' => $id]);
        }

        $types = collect(\App\Enums\ProductType::cases())->pluck('value')->join(',');
        $request->validate([
            "id" => "nullable|exists:products,id",
            "type" => "required_if:id,null|in:". $types,
            "sku" => "required_if:id,null|unique:products,sku,". $request->get('id', null) .",id",
            "name" => "required_if:id,null|string",
            "sale_price" => "required_if:id,null|numeric",
            "purchase_price" => "required_if:id,null|numeric",
            "category_id" => "nullable|exists:product_categories,id",
            "convertable" => "sometimes|array",
            "partials" => "sometimes|array",
        ]);

        $row = $request->only([
            'name', 'sku', 'unit', 'purchase_price', 'sale_price', 'description', 'category_id',
        ]);

        app('db')->beginTransaction();

        /** @var Product $record*/
        $record = Product::firstOrNew(['id' => intval($request->id)]);

        $record->fill($row);

        $record->save();

        $requestOption = new Request($request->get('option', []));
        $record->setOptions(
            $requestOption->only(['taxsen_income', 'taxsen_service'])
        );

        $record->save();

        if ($request->has('convertable'))
        {
            $record->converts()->sync([]);
            $convertable = collect($request->get('convertable'))
                ->whereNotNull('point_id')
                ->mapWithKeys(function ($e) {
                    $point = \App\Models\Product::searchKey($id = $e['point_id'])->first();
                    if (!$point) abort(422, "The unit [$id] undefined");
                    return [ $point->id => ['rate' => $e['rate']]];
                });

            $record->converts()->sync($convertable->toArray());


            $record->converts(true)->sync([]);
            $convertable = collect($request->get('convertable'))
                ->whereNotNull('base_id')
                ->mapWithKeys(function ($e) {
                    $point = \App\Models\Product::searchKey($id = $e['base_id'])->first();
                    if (!$point) abort(422, "The unit [$id] undefined");
                    return [ $point->id => ['rate' => $e['rate']]];
                });

            $record->converts(true)->sync($convertable->toArray());
        }

        if ($request->has('partials'))
        {
            $record->partials()->delete();

            $partials = collect($request->get('partials'))
            ->map(fn ($e) => ([
                "part_id" => $e['part_id'],
                "count" => $e['count'],
            ]));

            $record->partials()->createMany($partials->toArray());
        }

        app('db')->commit();


        $message = "The record has been saved.";

        return (new ProductResource($record))->additional([
            "message" => $message,
        ]);
    }

    public function delete ($id)
    {
        $record = Product::findOrFail($id);

        $record->delete();

        return response()->json([
            "message" => "The record has been deleted."
        ]);
    }
}
