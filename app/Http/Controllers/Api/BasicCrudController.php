<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{
    protected abstract function model();
    protected abstract function rulesStore(): array;

    public function index()
    {
        // if($request->has("only_trashed")){

        //     return $this->model()::withTrashed()->get();
        // }
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $this->validate($request,$this->rulesStore());
        $category = Category::create($request->all());
        $category->refresh();

        return $category;

    }

    public function show(Category $category)
    {
        return $category;
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request,$this->rules);
        $category->update($request->all());
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->noContent();
    }
}