<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserProduct;
use App\Http\Resources\UserProducts;
use App\Models\Category;
use App\Models\Product;
use App\Traits\apiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use apiResponse;
    public function allProducts()
    {
        $allProducts = Category::with(['products','products.product_images','products.product_colors'])->get();
        $allProducts = UserProducts::collection($allProducts);
        return $this->jsonResponse(200,"All products are here",$allProducts);
    }
    public function showProduct($id)
    {
        $product = Product::with(['product_images','product_colors','category'])->where('id',$id)->first();
        $product = new UserProduct($product);
        return $this->jsonResponse(200,"Here is the product",$product);
    }
}
