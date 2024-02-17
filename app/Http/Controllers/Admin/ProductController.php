<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductRequest;
use App\Http\Resources\Products;
use App\Models\Product;
use App\Models\Product_Colors;
use App\Models\Product_Images;
use App\Models\Product_Sizes;
use App\Traits\apiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class ProductController extends Controller
{
    use apiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        //Get products with images
        $products = Product::with(['product_images', 'product_colors'])->get();
        foreach ($products as $product) {
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
        //Make the products collection
        $products = Products::collection($products);
        return $this->jsonResponse(200, 'Products are here', $products);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(AddProductRequest $request): JsonResponse
    {
        //Add product data
        $product = Product::create($request->only([
            'title',
            'category_id',
            'description',
            'price',
            'discount',
            'stock',
            'barcode',
            'deadline',
        ]));
        if ($product) {
            //Add images of the product
            $productId = $product->id;
            $this->setProductImages($productId, $request->input('images'));
            //Add product colors
            if ($request->hasAny(['colors'])) {
                $this->setProductColors($productId, $request->colors);
            }
            //Add product sizes
            if ($request->hasAny(['sizes'])) {
                $this->setProductSizes($productId, $request->sizes);
            }
            return $this->jsonResponse(201, 'Product has been created', $product);
        } else
            return $this->jsonResponse(500, 'Error has occurred');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with(['product_images', 'product_colors'])->where('id', $id)->first();
        $product = new Products($product);
        return $this->jsonResponse(200, 'Product is here', $product);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        if (($request->input('title') != $product->title) && ($request->input('barcode') != $product->barcode)) {
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
            $colors = json_decode($request->colors, true);
            $images = $request->file('images');
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
            if ($updated) {
                $imageUpdated = true;
                $colorUpdated = true;
                //Update product images
                $this->setProductImages($product->id, $images, true);
                //Update product colors
                if ($request->hasAny('color')) {
                    $product->product_colors()->delete();
                    foreach ($colors as $color) {
                        $colorUpdated = Product_Colors::create([
                            'color' => $color,
                            'product_id' => $product->id
                        ]);
                    }
                }
                if ($imageUpdated && $colorUpdated) {
                    return $this->jsonResponse(201, 'The product has been updated', $product);
                } else {
                    return $this->jsonResponse(500, 'Error in images or colors update', $product);
                }
            } else {
                return $this->jsonResponse(500, 'Error in product update', $product);
            }
        } else {
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
            if ($updated) {
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
                if ($request->input('color')) {
                    $product->product_colors()->delete();
                    foreach ($colors as $color) {
                        $colorUpdated = Product_Colors::create([
                            'color' => $color,
                            'product_id' => $product->id
                        ]);
                    }
                }
                if ($imageUpdated && $colorUpdated) {
                    return $this->jsonResponse(201, 'The product has been updateي', $product);
                } else {
                    return $this->jsonResponse(500, 'Error in images or colors update', $product);
                }
            } else {
                return $this->jsonResponse(500, 'Error in product update', $product);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Store images for specific product.
     */
    public function setProductImages($productId, $images, $update = false): void
    {
        if ($update) {
            $product = Product::findOrfail($productId);
            $this->deleteImages($product->product_images->pluck('image')->toArray());
            $product->product_images->delete();
        }
        foreach ($images as $image) {
            // Get the base64 image data from the JSON request
            $base64Image = $image;
            // Extract the image data from the base64 string
            $data = explode(',', $base64Image);
            // Decode the base64 image data
            $decodedImage = base64_decode($data[0]);
            // Specify the directory where you want to store the image
            $directory = 'product_images';
            // Generate a unique filename
            $filename = uniqid() . '.jpg'; // You may adjust the extension as per the actual image type
            // Save the decoded image to the specified directory
            Storage::disk('e-commerce')->put($directory . '/' . $filename, $decodedImage);
            $imagePath = $directory . '/' . $filename;
            Product_Images::create([
                'product_id' => $productId,
                'image' => asset('images/' . $imagePath),
            ]);
        }
    }
    /**
     * Set colors for specific product.
     */
    private function setProductColors($productId, $colors, $update = false): void
    {
        $colors = json_decode($colors, true);
        if ($update) {
            $product = Product::findOrFail($productId);
            $product->product_colors->delete();
        }
        foreach ($colors as $color) {
            Product_Colors::create([
                'product_id' => $productId,
                'color' => $color['name'] . "|" . $color['value'],
            ]);
        }
    }
    /**
     * Set sizes for specific product.
     */
    private function setProductSizes($productId, $sizes, $update = false): void
    {
        $sizes = explode(',', $sizes);
        if ($update) {
            $product = Product::findOrFail($productId);
            $product->product_sizes->delete();
        }
        foreach ($sizes as $size) {
            Product_Sizes::create([
                'product_id' => $productId,
                'size' => $size
            ]);
        }
    }
    private function deleteImages($images)
    {
        foreach ($images as $image) {
            $position = strpos($image, 'images');
            $deletedImage = substr($image, $position);
            File::delete($deletedImage);
        }
    }
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $images = $product->product_images->pluck('image')->toArray();
        $this->deleteImages($images);
        $deleted = $product->delete();
        if ($deleted)
            return $this->jsonResponse(200, 'Product deleted successfully');
        return $this->jsonResponse(500, 'Error has been occurred');
    }
}
