<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllOrders;
use App\Models\Order;
use App\Models\Order_Status;
use App\Traits\apiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  use apiResponse;
  public function orders(): JsonResponse
  {
    $orders = Order::with('orderStatus')->get();
    return $this->jsonResponse(200, 'Here are the orders', AllOrders::collection($orders));
  }
  public function orderDetails($id): JsonResponse
  {
    $order = Order::with(['orderDetails:id,order_id,product_name,category,amount,piece_price,price', 'payment'])->select('id', 'name', 'email', 'address', 'governorate', 'city', 'postal', 'phone')
      ->findOrFail($id);
    $ordersCount = Order::where('email', $order->email)
      ->orWhere('phone', $order->phone)->count();
    $order['orders_count'] = $ordersCount;
    return $this->jsonResponse(200, 'The order details are here', $order);
  }
  public function allCustomers(): JsonResponse
  {
    $customers = Order::select('id', 'name', 'email', 'address', 'governorate', 'city', 'postal', 'phone')->get();
    $customers = $customers->unique('email');
    $allCustomers = [];
    foreach ($customers as $customer) {
      $ordersCount = Order::where('email', $customer->email)->count();
      $ordersTotalPrice = Order::where('email', $customer->email)->sum('total_price');
      $formattedCustomer = [
        'id' => $customer['id'],
        'name' => $customer['name'],
        'email' => $customer['email'],
        'address' => $customer['address'],
        'governorate' => $customer['governorate'],
        'city' => $customer['city'],
        'postal' => $customer['postal'],
        'phone' => $customer['phone'],
        'orders_count' => $ordersCount,
        'total_paid' => $ordersTotalPrice,
      ];
      $allCustomers[] = $formattedCustomer;
    }
    return $this->jsonResponse(200, 'Here all the customers', $allCustomers);
  }
  public function customerOrders(Request $request): JsonResponse
  {
      $orders = Order::where('phone',$request->phone)->get();
      $customerDetails = [
          'name' => $orders[0]->name,
          'city' => $orders[0]->city,
          'governorate' => $orders[0]->governorate,
          'street' => $orders[0]->address,
          'total_orders' => Order::where('phone',$request->phone)->count(),
          'total_spent' => Order::where('phone',$request->phone)->sum('total_price'),
      ];
      foreach ($orders as $order){
          $customerDetails['orders'][] = [
              'order_id' => $order->id,
              'status' => $order->status,
              'spent' => $order->total_price,
              'date' => $order->created_at->format('Y-m-d - H:i'),
          ];
      }
      return $this->jsonResponse(200,'Customer Details',$customerDetails);
  }
  public function changeStatus(Request $request)
  {
      $validator = Validator::make($request->all(),[
            'status' => 'required|in:processing,delivered',
      ]);
      if($validator->fails()){
          return $this->jsonResponse(422,'Validation errors');
      }
      $order = Order::findOrFail($request->order_id);
      $orderStatus = Order_Status::where('order_id',$order->id)->first();
      if($request->status == 'processing'){
          $order->status = 'processing';
          $orderStatus->processing = now();
      }else{
          $order->status = 'completed';
          $orderStatus->delivered = now();
      }
      $order->save();
      $orderStatus->save();
      return $this->jsonResponse(200,'Order status updated');
  }
}
