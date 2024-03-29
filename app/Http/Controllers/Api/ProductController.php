<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('indxe','show');
    }
    public function index(Request $request)
    {
        $product= Product::filter($request->query())
        ->with('category:id,name','store','tags')
        ->paginate();

        return ProductResource::collection($product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'status' => 'in:active,inactive',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|gt:price',
        ]);

        $usre=$request->user();
        if(!$usre->tokenCan('products.create'))
        {
            return response([
                'message' => 'Not allowed',
            ],403);
        }
        $product = Product::create($request->all());

        return Response::json($product, 201, [
            'Location' => route('products.show', $product->id),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return  new ProductResource($product);
        return $product
        ->load('category:id,name','store:id,name','tags:id,name');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'status' => 'in:active,inactive',
            'price' => 'sometimes|required|numeric|min:0',
            'compare_price' => 'nullable|numeric|gt:price',
        ]);

        $usre = $request->user();
        if (!$usre->tokenCan('products.update')) {
            abort(404, 'not allowed');
        }
        $product->update($request->all());

        return Response::json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usre = Auth::guard('sanctum')->user();
        if (!$usre->tokenCan('products.delete')) {
            return response([
                'message' => 'Not allowed',
            ], 403); 
        }
        Product::destroy($id);
        return response()->json([
            'message'=>'product deleted successfuly',
        ],200);
    }
}
