<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyService
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

        $query = Company::with($relations)
            ->select($params)
            ->withCount($count);

        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        return $query->paginate($paginate);
    }
    public function find($id, $params = ['*'], $relations = [], $count = [])
    {

        return findByid(Company::class, $id, $relations, $params, $count);
    }
    public function create()
    {
        return view('company.create');
    }

    public function store($request)
    {

        $company = Company::create([
            'name' => $request->name,
            'trn' => $request->trn ?? null,
            'address' => $request->address ?? null,
            'phone' => $request->phone ?? null,
            'discount' => $request->discount,
        ]);

        return $company;
    }

    public function update($id, $request)
    {

        $company = $this->find($id, ['*']);

        $company->update([
            'name' => $request->company_name,
            'trn' => $request->trn ?? null,
            'address' => $request->address ?? null,
            'phone' => $request->phone ?? null,
            'discount' => $request->discount,
        ]);
        return  $company;
    }

    public function delete($id)
    {

        $company = $this->find($id, ['*']);

        $company->delete();

        return  $company;
    }
}
