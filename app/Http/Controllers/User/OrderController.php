<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\MakeOrderRequest;
use App\Mail\MakeOrderMail;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Traits\apiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    use apiResponse;
    public function makeOrder(MakeOrderRequest $request): JsonResponse
    {
        //Make the order for the user
        $order = Order::create($request->only([
            'name',
            'email',
            'address',
            'governorate',
            'city',
            'postal',
            'phone',
            'promocode',
            'total_price'
        ]));
        if($order)
        {
            $products = json_decode($request->products,true);
            //Set order details
            $this->setOrderDetails($order->id,$products);
            //Get the order details then send email
            $customerOrder = Order::with('orderDetails')->where('id',$order->id)->first();
            $this->sendEmail($customerOrder);
            return $this->jsonResponse(201,'Order has been sent.');

        }
        else
        {
            return $this->jsonResponse(500,'Error has been occurred.');
        }
    }
    private function setOrderDetails($orderId, $products): void
    {
        foreach ($products as $product)
        {
            $productDetails = Product::findOrFail($product['product_id']);
            $productDetails->stock = $productDetails->stock - $product['amount'];
            $productDetails->save();
            OrderDetails::create([
                'order_id' => $orderId,
                'product_name' => $productDetails->title,
                'category' => $productDetails->category->title,
                'amount' => $product['amount'],
                'piece_price' => $productDetails->discount,
                'price' => $productDetails->price * $product['amount']
            ]);
        }
    }
    private function sendEmail($order): void
    {
        $order = json_decode($order,true);
        Mail::to($order['email'])->send(new MakeOrderMail($order));
    }
}
