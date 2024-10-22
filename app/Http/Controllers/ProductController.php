<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::search(
            $request->input('name'),
            $request->input('minPrice'),
            $request->input('maxPrice')
        )->paginate(10);
    
        return response()->json($products);
    }

   
    public function store(Request $request)
    {
    $validated = $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
        'stock' => 'required|integer|min:0',
    ]);

    return Product::create($validated);
    }

}
