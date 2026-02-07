<?php

namespace App\Services;

use App\Models\Company;

use App\Models\Product;

use Ramsey\Uuid\Rfc4122\UuidV4;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{


    public static function make()
    {
        return new self();
    }
    public function index($relations = [], $count = [], $params = ['*'], $paginate = 10, $search = null): LengthAwarePaginator
    {
        if ($paginate > 50) {
            $paginate = 50;
        }

        $query = Product::with($relations)
            ->select($params)
            ->withCount($count);

        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        return $query->paginate($paginate);
    }
    public function find($id, $params = ['*'], $relations = [], $count = [])
    {

        return findByid(Product::class, $id, $relations, $params, $count);
    }
    public function create()
    {

        return view('product.create');
    }

    public function store($request)
    {

        $product = Product::create([
            'company_id' => $request->company_id,
            'name' => $request->product_name,
            'quantity' => $request->quantity,
            'free_items' => (int) ($request->free_items ?? 0),
            'vat' => $request->vat,
            'price' => $request->price,
            'date_of_create' => $request->date_of_create,
        ]);

        return $product;
    }

    public function update($id, $request)
    {

        $product = $this->find($id, ['*']);

        $product->update([
            'company_id' => $request->company_id,
            'name' => $request->product_name,
            'quantity' => $request->quantity,
            'free_items' => (int) ($request->free_items ?? 0),
            'vat' => $request->vat,
            'price' => $request->price,
            'date_of_create' => $request->date_of_create,
        ]);

        return $product;
    }

    public function delete($id)
    {
        $product = $this->find($id, ['*']);
        $product->delete();
        return $product;
    }
}
