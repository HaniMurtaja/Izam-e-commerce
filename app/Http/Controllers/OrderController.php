<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::create(['user_id' => Auth::id()]);

        foreach ($validated['products'] as $productData) {
            $product = Product::find($productData['id']);
            
            if ($product->stock < $productData['quantity']) {
                return response()->json(['error' => 'Insufficient stock for ' . $product->name], 400);
            }

            $product->stock -= $productData['quantity'];
            $product->save();

            $order->products()->attach($product->id, ['quantity' => $productData['quantity']]);
        }

        return response()->json(['message' => 'Order placed successfully'], 201);
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        
        $this->authorize('view', $order);

        return response()->json($order);
    }
}
