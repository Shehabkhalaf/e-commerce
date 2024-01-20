<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddCategoryRequest;
use App\Http\Resources\AllCategories;
use App\Models\Category;
use App\Traits\apiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //apiResponse trait to use jsonResponse
    use apiResponse;
    //Function to get all the categories
    public function index()
    {
        //allCategories collection
        $allCategories = AllCategories::collection(Category::all());
        return $this->jsonResponse(200,'Data delivered',$allCategories);
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
    //Function to Add new category
    public function store(AddCategoryRequest $request): JsonResponse
    {
        $newCategory = Category::create($request->only(['title']));
        if ($newCategory)
            return $this->jsonResponse(201,'Category Added successfully',$newCategory);
        else
            return  $this->jsonResponse(500,'Error has been detected');
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
    public function update(AddCategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        $updated = $category->update([
            'title' => $request->input('title')
        ]);
        if ($updated)
            return $this->jsonResponse(201,'The product updated successfully',$category);
        else
            return $this->jsonResponse(500,'Error has been detected');
    }

    //Function to delete the category
    public function destroy(string $id): JsonResponse
    {
        $deleted = Category::destroy($id);
        if($deleted != 0)
            return $this->jsonResponse(200,'Deleted successfully');
        else
            return  $this->jsonResponse(500,'Error occurred');
    }
}
