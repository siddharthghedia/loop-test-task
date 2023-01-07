<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttachProductToRequest;
use App\Http\Requests\OrderRequest;
use App\Service\OrderService;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'orders' => $this->orderService->list()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\OrderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderRequest $request)
    {
        $this->orderService->create($request->all());

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $order = $this->orderService->get($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order is not found.' 
            ]);
        }

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\OrderRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OrderRequest $request, $id)
    {
        $order = $this->orderService->get($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order is not found.' 
            ]);
        }

        $this->orderService->update($order, $request->all());

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $order = $this->orderService->get($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order is not found.' 
            ]);
        }

        $this->orderService->delete($order);

        return response()->json([
            'success' => true
        ]);
    }

    public function attachProductToOrder(AttachProductToRequest $request, $orderId)
    {
        $order = $this->orderService->get($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order is not found.' 
            ]);
        }

        $this->orderService->attachProductToAnOrder($order, $request->all());

        return response()->json([
            'success' => true
        ]);
    }

    public function submitAnOrder($orderId)
    {
        $order = $this->orderService->get($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order is not found.' 
            ]);
        }

        $order->load('customer');
        $order->loadSum('products', 'price');

        $requestData = [
            'order_id' => (int) $orderId,
            'customer_email' => $order->customer->email,
            'value' => $order->products_sum_price
        ];

        $response = $this->orderService->captureMoneyFromSuperPaymentProvider($requestData);

        if ($response->json()['message'] == 'Payment Successful') {
            $order->payed = true;
            $order->save();

            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
}
