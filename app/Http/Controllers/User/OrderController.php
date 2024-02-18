<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\MakeOrderRequest;
use App\Http\Requests\PaymentRequest;
use App\Mail\MakeOrderMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promocode;
use App\Traits\apiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    use apiResponse;
    //Make Order function
    public function makeOrder(MakeOrderRequest $request)
    {
        //Call cash order function if the payment cash
        if ($request->payment_method == 'cash') {
            return $this->cashOrder($request);
        }
        //Call cash order function if the payment card
        else {
            $checkout_url = $this->cardOrder($request);
            return $this->jsonResponse(200,'',$checkout_url);
        }
    }
    private function cashOrder($request): JsonResponse
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
            'payment_method',
        ]));
        if ($order) {
            $products = json_decode($request->products, true);
            //Set order details
            $this->setOrderDetails($order, $products);
            //Get the order details then send email
            $customerOrder = Order::with('orderDetails')->where('id', $order->id)->first();
            $this->sendEmail($customerOrder);
            return $this->jsonResponse(201, 'Order has been sent.');
        } else {
            return $this->jsonResponse(500, 'Error has been occurred.');
        }
    }
    private function cardOrder($request)
    {
        //Store the order in database
        $order = Order::create($request->only([
            'name',
            'email',
            'address',
            'governorate',
            'city',
            'postal',
            'phone',
            'promocode',
            'payment_method',
        ]));
        $products = json_decode($request->products, true);
        //Set order details
        $this->setOrderDetails($order, $products);
        return $this->fatora($order);
    }
    private function fatora($order)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fatora.io/v1/payments/checkout',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'api_key:  ee9fbbff-8ee1-42b9-9c86-2681512753d2'
            ),
            CURLOPT_POSTFIELDS => '{
                                "amount":"' . $order->total_price . '",
                                "currency": "EGP",
                                "order_id":"' . $order->id . '",
                                "client" : {
                                    "name" : "' . $order->name . '",
                                    "phone" : "' . $order->phone . '",
                                    "email" : "' . $order->email . '"
                                },
                                "language":"en",
                                "success_url" : "http://127.0.0.1:8000/api/user/success",
                                "failure_url" : "http://127.0.0.1:8000/api/user/failure",
                                "fcm_token" : "XXXXXXXXX",
                                "save_token" : true,
                                "note": "some additional info"
                            }'

        ));
        $response = curl_exec($curl);
        $response = json_decode($response,true);
        curl_close($curl);
        if ($response['status'] === 'SUCCESS'){
            return $response['result']['checkout_url'];
        }
        else{
            $order->delete();
            return $this->jsonResponse(500,'Error occurred');
        }
    }
    public function successPayment(PaymentRequest $request)
    {
        if ($request->failed()){
            abort(422,"You can't use this link back to the site: http://localhost:5173/");
        } else{
            $payment = Payment::create([
                'transaction_id' => $request->transaction_id,
                'order_id' => $request->order_id,
                'description' => $request->description,
                'status' => 'paid',
                'mode' => $request->mode
            ]);
            $customerOrder = Order::with('orderDetails')->where('id', $request->order_id)->first();
            $this->sendEmail($customerOrder);
            return redirect('http://localhost:5173/');
        }
    }
    public function failedPayment(PaymentRequest $request)
    {
        $payment = Payment::create([
            'transaction_id' => $request->transaction_id,
            'order_id' => $request->order_id,
            'description' => $request->description,
            'status' => 'failed',
            'mode' => $request->mode
        ]);
        return redirect('http://localhost:5173/');
    }
    private function setOrderDetails($order, $products): void
    {
        $totalPrice = 0;
        foreach ($products as $product) {
            $productDetails = Product::findOrFail($product['product_id']);
            if ($productDetails->deadline) {
                $price = $productDetails->price - $productDetails->discount;
                $price = $price * $product['amount'];
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_name' => $productDetails->title,
                    'category' => $productDetails->category->title,
                    'amount' => $product['amount'],
                    'piece_price' => $productDetails->discount,
                    'price' => $price
                ]);
            } else {
                $price = $productDetails->price * $product['amount'];
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_name' => $productDetails->title,
                    'category' => $productDetails->category->title,
                    'amount' => $product['amount'],
                    'piece_price' => $productDetails->price,
                    'price' => $price
                ]);
            }
            $productDetails->update([
                'sold' => $productDetails->sold + $product['amount']
            ]);
            if ($productDetails->sold == $productDetails->stock) {
                $productDetails->update([
                    'stock' => 0
                ]);
            }
            $this->updateEarningCategory($productDetails->category_id, $price);
            $totalPrice += $price;
        }
        if ($order->promocode) {
            $promocode = Promocode::where('promocode',$order->promocode)->first();
            $totalPrice = $totalPrice - ($totalPrice * ($promocode->discount / 100));
        }
        $order->update([
            'total_price' => $totalPrice
        ]);
    }
    private function updateEarningCategory($categoryId, $price): void
    {
        $category = Category::findOrFail($categoryId);
        $category->earning = $category->earning + $price;
        $category->save();
    }
    private function sendEmail($order): void
    {
        $order = json_decode($order, true);
        Mail::to($order['email'])->send(new MakeOrderMail($order));
    }
}
