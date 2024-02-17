<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllOrders;
use App\Models\Order;
use App\Traits\apiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  use apiResponse;
  public function orders()
  {
    $orders = Order::all();
    return $this->jsonResponse(200, 'Here are the orders', AllOrders::collection($orders));
  }
  public function orderDetails($id)
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
}
