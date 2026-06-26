<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Product;
class UserControlller extends Controller
{
   public function index(){
        $products = Product::latest()->count();
        $categories = Category::latest()->count();
    
        return Inertia::render('User/UserDashboard',compact('products','categories'));
    }

    public function create(){
         
    $categories = Category::latest()->get();
    return Inertia::render('User/Usercategory',compact('categories'));

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
         
        Category::create($validated);

        return back()->with('message', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update($validated);

        return back()->with('message', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return back()->with('message', 'Category deleted successfully.');
    }

}
