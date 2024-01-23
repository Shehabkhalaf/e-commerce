<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductRequest;
use App\Http\Resources\Products;
use App\Models\Product;
use App\Models\Product_Colors;
use App\Models\Product_Images;
use App\Traits\apiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use apiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //Get products with images
        $products = Product::with(['product_images','product_colors'])->get();
        //Make the products collection
        $products = Products::collection($products);
        return $this->jsonResponse(200,'Products are here',$products);
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
    public function store(AddProductRequest $request): JsonResponse
    {
        //return $this->jsonResponse(200,'',$request->images);
        //Add product data
        $product = Product::create($request->only([
            'title',
            'category_id',
            'description',
            'price',
            'discount',
            'stock',
            'barcode'
        ]));
        //Add images of the product
        if($product)
        {
           $productId = $product->id;
            $images = json_decode($request->images,true);
           foreach ($images as $image)
           {
               Product_Images::create([
                   'product_id' => $productId,
                    'image' => $image['img'],
               ]);
           }
            //Add product colors
            if ($request->hasAny(['colors']))
            {
                $colors = json_decode($request->colors,true);
                foreach ($colors as $color)
                {
                    Product_Colors::create([
                        'product_id' => $productId,
                        'color' => $color['name']."|".$color['value'],
                    ]);
                }
            }
            return $this->jsonResponse(201,'Product has been created',$product);
        }
        else
            return $this->jsonResponse(500,'Error has occurred');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with(['product_images','product_colors'])->where('id',$id)->first();
        $product = new Products($product);
        return $this->jsonResponse(200,'Product is here',$product);
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
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        if(($request->input('title') != $product->title) && ($request->input('barcode') != $product->barcode))
        {
            $request->validate([
                'title' => 'unique:products,title',
                'category_id' => 'required',
                'description' => 'required',
                'price' => 'required',
                'discount' => 'required',
                'stock' => 'required',
                'barcode' => 'required|unique:products,barcode',
                'images' => 'required',
            ]);
            $colors = json_decode($request->colors);
            $images = json_decode($request->images);
            //Update product
            $updated = $product->update([
               'title' => $request->input('title'),
               'category_id' => $request->input('category_id'),
               'description' => $request->input('description'),
               'price' => $request->input('price'),
               'discount' => $request->input('discount'),
               'stock' => $request->input('stock'),
               'barcode' => $request->input('barcode'),
            ]);
            if($updated)
            {
                $imageUpdated = true;
                $colorUpdated = true;
                //Update product images
                $product->product_images()->delete();
                foreach ($images as $image) {
                   $imageUpdated = Product_Images::create([
                        'image' => $image,
                        'product_id' => $product->id
                    ]);
                }
                //Update product colors
                if($request->input('color'))
                {
                    $product->product_colors()->delete();
                    foreach ($colors as $color) {
                        $colorUpdated = Product_Colors::create([
                            'color' => $color,
                            'product_id' => $product->id
                        ]);
                    }
                }
                if($imageUpdated && $colorUpdated)
                {
                    return $this->jsonResponse(201,'The product has been updated',$product);
                }
                else
                {
                    return $this->jsonResponse(500,'Error in images or colors update',$product);
                }
            }
            else
            {
                return $this->jsonResponse(500,'Error in product update',$product);
            }

        }
        else
        {
            $requestValidated = $request->validate([
                'category_id' => 'required',
                'description' => 'required',
                'price' => 'required',
                'discount' => 'required',
                'stock' => 'required',
                'barcode' => 'required',
                'images' => 'required',
            ]);
            $colors = json_decode($request->colors);
            $images = json_decode($request->images);
            //Update product
            $updated = $product->update([
                'category_id' => $request->input('category_id'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'discount' => $request->input('discount'),
                'stock' => $request->input('stock'),
            ]);
            if($updated)
            {
                $imageUpdated = true;
                $colorUpdated = true;
                //Update product images
                $product->product_images()->delete();
                foreach ($images as $image) {
                    $imageUpdated = Product_Images::create([
                        'image' => $image,
                        'product_id' => $product->id
                    ]);
                }
                //Update product colors
                if($request->input('color'))
                {
                    $product->product_colors()->delete();
                    foreach ($colors as $color) {
                        $colorUpdated = Product_Colors::create([
                            'color' => $color,
                            'product_id' => $product->id
                        ]);
                    }
                }
                if($imageUpdated && $colorUpdated)
                {
                    return $this->jsonResponse(201,'The product has been updateي',$product);
                }
                else
                {
                    return $this->jsonResponse(500,'Error in images or colors update',$product);
                }
            }
            else
            {
                return $this->jsonResponse(500,'Error in product update',$product);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = Product::destroy($id);
        if($deleted != 0)
            return $this->jsonResponse(200,'Product deleted successfully');
        return $this->jsonResponse(500,'Error has been occurred');
    }

}
