<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesUser;
use App\Http\Resources\UserProduct;
use App\Http\Resources\UserProducts;
use App\Models\Category;
use App\Models\Product;
use App\Traits\apiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use apiResponse;
    public function allProducts()
    {
        $allProducts = Category::with(['products', 'products.product_images', 'products.product_colors'])->get();
        foreach ($allProducts as $category) {
            foreach ($category->products as $product) {
                if ($product->deadline) {
                    $deadline = Carbon::parse($product->deadline);
                    if (now() > $deadline) {
                        $product->update([
                            'deadline' => null,
                            'discount' => null
                        ]);
                    }
                }
            }
        }
        $allProducts = UserProducts::collection($allProducts);
        return $this->jsonResponse(200, "All products are here", $allProducts);
    }
    public function showProduct($id)
    {
        $product = Product::with(['product_images', 'product_colors', 'category'])->where('id', $id)->first();
        $product = new UserProduct($product);
        return $this->jsonResponse(200, "Here is the product", $product);
    }
    public function allCategories()
    {
        return $this->jsonResponse(200, '', CategoriesUser::collection(Category::all()));
    }
}
