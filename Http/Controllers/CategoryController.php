<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function AllCat()
    {
        // build relationship One to One table with query builder, so you dont need to add function in the models
        // $categories = DB::table('categories')
        //             ->join('users', 'categories.user_id', 'users.id')
        //             ->select('categories.*', 'users.name')
        //             ->latest()->paginate(5);

        $categories = Category::latest()->paginate(5);
        $trashCat = Category::onlyTrashed()->latest()->paginate(3);


        // read data with query builder
        // $categories = DB::table('categories')->latest()->get();
        return view('admin.category.index', compact('categories', 'trashCat'));
    }

    public function AddCat(Request $request)
    {
        // ADD VALIDATION

        $validatedData = $request->validate([
            'category_name' => 'required|unique:categories|max:255'
        ],
        // [
        //      for customize the error message in the index, should be add .required(name in the validation)
        //     'category_name.required' => 'Please Input The Category Name First'
        // ]
        );

        // ADD CATEGORY WITH ORM

        // Category::insert([
        //     'category_name' => $request->category_name,
        //     'user_id' => Auth::user()->id,
        //     'created_at' => Carbon::now()
        // ]);
        $category = new Category;
        $category->category_name = $request->category_name;
        $category->user_id = Auth::user()->id;
        $category->save();
        

        // ADD CATEGORY WITH QUERY BUILDER
        // $data = array();
        // $data['category_name'] = $request->category_name;
        // $data['user_id'] = Auth::user()->id;
        // DB::table('categories')->insert($data);

        return Redirect()->back()->with('success', 'Category Inserted Successfull');
    }

    public function Edit($id)
    {
        $categories = Category::find($id);
        

        // QUERY BUILDER
        // $categories = DB::table('categories')->where('id', $id)->first();
        return view('admin.category.edit', compact('categories'));
    }

    public function Update(Request $request, $id)
    {
        $update = Category::find($id)->Update([
            'category_name' => $request->category_name,
            'user_id' => Auth::user()->id
        ]);


        // QUERY BUILDER
        // $data = array();
        // $data['category_name'] = $request->category_name;
        // $data['user_id'] = Auth::user()->id;
        // $data = DB::table('categories')->where('id', $id)->update($data);

        return Redirect()->route('all.category')->with('success', 'Category Updated Successfull');
    }

    public function SoftDelete($id)
    {
        $delete = Category::find($id)->delete();
        return Redirect()->back()->with('success', 'Category Soft Delete Successfully');
    }

    public function Restore($id)
    {
        $delete = Category::withTrashed()->find($id)->Restore();
        return Redirect()->back()->with('success', 'Category Restore Successfully');
    }

    public function Pdelete($id)
    {
        $delete = Category::onlyTrashed()->find($id)->forceDelete();
        return Redirect()->back()->with('success', 'Category Permanently Deleted');
    }

}
