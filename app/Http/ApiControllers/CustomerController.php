<?php

namespace App\Http\ApiControllers;

use App\Http\Filters\CustomerFilter;
use App\Models\Customer;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index (CustomerFilter $filter)
    {
        $collection = Customer::filter($filter)->collective();

        return CustomerResource::collection($collection);
    }

    public function show ($id)
    {
        $record = Customer::findOrFail($id);

        return new CustomerResource($record);
    }

    public function save (Request $request)
    {
        if (!$request->has('id'))
        {
            $id = Customer::searchKey($request->get('sku'))->first('id')?->id;
            $request->merge(['id' => $id]);
        }

        $request->validate([
            "code" => "nullable|unique:customers,code,". $request->id .",id",
            "name" => "required_if:id,null|string",
            "option" => "required_if:id,null|array",
            "option.tax_no" => "nullable|string",
            "option.tax_inclusive" => "nullable|boolean",
        ]);

        $row = $request->only([
            'name', 'code',
        ]);

        app('db')->beginTransaction();

        /** @var Customer $record*/
        $record = Customer::firstOrNew(['id' => intval($request->id)]);
        $mode = $record->getKey() ? "update" : "create";
        $record->fill($row);

        $requestOption = new Request($request->get('option'));
        $record->setOptions(
            $requestOption->only(['tax_no', 'tax_inclusive'])
        );

        $record->save();

        if ($request->get('contact')) {
            $requestContact = new Request($request->get('contact'));

            $record->setContact($requestContact->only([
                'name', 'email', 'phone', 'mobile', 'street', 'city', 'zipcode', 'option'
            ]));
        }

        if ($request->get('payment_contact')) {
            $requestContact = new Request($request->get('payment_contact'));

            $record->setPaymentContact($requestContact->only([
                'name', 'email', 'phone', 'mobile', 'street', 'city', 'zipcode', 'option'
            ]));
        }

        $message = "The customer has been ". $mode === 'create' ? 'created' : 'updated'.".";

        app('db')->commit();

        return (new CustomerResource($record))
            ->additional(["message" => $message]);
    }

    public function delete ($id)
    {
        $record = Customer::findOrFail($id);

        $record->delete();

        return response()->json([
            "message" => "The record hass been deleted."
        ]);
    }

    public function disabled ($id)
    {
        $record = Customer::findOrFail($id);

        $record->delete();

        return response()->json([
            "message" => "The record hass been deleted."
        ]);
    }
}
