<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\SuccessResource;
use App\Services\Orders\StoreOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreOrderRequest $storeOrderRequest,StoreOrderService $storeOrderService)
    {
        return new SuccessResource(['order' => new OrderResource($storeOrderService->execute(collect($storeOrderRequest->validated()))) ]);
    }
}
