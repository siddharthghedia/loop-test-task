<?php

namespace App\Service;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderService
{
    public function list()
    {
        return Order::all();
    }

    public function get($id)
    {
        return Order::find($id);
    }

    public function create($data)
    {
        Order::create([
            'customer_id' => $data['customer_id']
        ]);
    }

    public function update($order, $data)
    {
        $order->update([
            'customer_id' => $data['customer_id']
        ]);
    }

    public function delete($order)
    {
        $order->delete();
    }

    public function attachProductToAnOrder($order, $data)
    {
        if (!$order->payed) {
            $order->products()->attach($data['product_id']);
        }
    }

    public function captureMoneyFromSuperPaymentProvider($data)
    {
        $response = Http::withHeaders(
            [
                'Content-Type' => 'application/json'
            ])
            ->post(env("PAYMENT_PROVIDER"), $data);

        return $response;
    }
}
