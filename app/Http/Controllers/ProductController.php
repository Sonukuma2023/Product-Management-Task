<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class ProductController extends Controller
{
   public function index()
{
    $products = Product::where('user_id', auth()->id())
    ->get();
    $categories = Category::where('user_id', auth()->id())->get();
     

    return Inertia::render('User/Product', [
        'products' => $products,
        'categories' => $categories,
    ]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'description' => 'nullable|string',
            'sku_code'    => 'required|string|unique:products,sku_code',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        $validated['user_id'] = auth()->id();

       if ($request->hasFile('image')) {

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            $destination = public_path('asset/product/images');

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $file->move($destination, $filename);

            $validated['image'] = 'asset/product/images/' . $filename;

        } else {
            $validated['image'] = null;
        }
         

        Product::create($validated);

        return back()->with('message', 'Product created successfully.');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'description' => 'nullable|string',
            'sku_code'    => 'required|string|unique:products,sku_code,' . $product->id,
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Optional on Edit
        ]);

        if ($request->hasFile('image')) {
            // Delete old file if it exists
            if (File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('asset/product/images'), $filename);
            $validated['image'] = 'asset/product/images/' . $filename;
        } else {
            // Retain old value if file upload is bypassed
            unset($validated['image']);
        }

        $product->update($validated);

        return back()->with('message', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if (File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }

        $product->delete();

        return back()->with('message', 'Product deleted successfully.');
    }
}