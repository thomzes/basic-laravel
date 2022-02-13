<?php

namespace App\Http\Controllers;
use App\Models\Brand;
use App\Models\Multipic;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\AssignOp\Mul;

class BrandController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function AllBrand()
    {
        $brands = Brand::latest()->paginate(5);
        return view('admin.brand.index', compact('brands'));
    }

    public function StoreBrand(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'brand_name' => 'required|unique:brands|min:4',
            'brand_image' => 'required|mimes:jpg,jpeg,png'
        ],
        [
            'brand_image.min' => 'The Brand name must be at least 4 characters.'
        ]);

        $brand_image = $request->file('brand_image');

        // Generate random name for the image 
        // $name_gen = hexdec(uniqid());
        // $img_ext = strtolower($brand_image->getClientOriginalExtension());
        // $img_name = $name_gen.'.'.$img_ext;
        // $up_location = 'image/brand/';
        // $last_img = $up_location.$img_name;
        // $brand_image->move($up_location, $img_name);

        // Using intervention image package laravel
        $name_gen = hexdec(uniqid()).'.'.$brand_image->getClientOriginalExtension();
        Image::make($brand_image)->resize(300,200)->save('image/brand/'.$name_gen);

        $last_img = 'image/brand/'.$name_gen;


        Brand::insert([
            'brand_name' => $request->brand_name,
            'brand_image' => $last_img,
            'created_at' => Carbon::now(),
        ]);

        return Redirect()->back()->with('success', 'Brand Inserted Successfully!');
    }

    public function Edit($id)
    {
        $brands = Brand::find($id);
        return view('admin.brand.edit', compact('brands'));
    }

    public function Update(Request $request, $id)
    {
        // Validation
        $validatedData = $request->validate([
            'brand_name' => 'required|min:4'
        ],
        [
            'brand_image.min' => 'The Brand name must be at least 4 characters.'
        ]);

        $old_image = $request->old_image;

        $brand_image = $request->file('brand_image');

        if($brand_image){
            // Generate random name for the image 
            $name_gen = hexdec(uniqid());
            $img_ext = strtolower($brand_image->getClientOriginalExtension());
            $img_name = $name_gen.'.'.$img_ext;
            $up_location = 'image/brand/';
            $last_img = $up_location.$img_name;
            $brand_image->move($up_location, $img_name);

            unlink($old_image);

            Brand::find($id)->update([
                'brand_name' => $request->brand_name,
                'brand_image' => $last_img,
                'created_at' => Carbon::now(),
            ]);

            return Redirect()->back()->with('success', 'Brand Updated Successfully!');
        }else{
            Brand::find($id)->update([
                'brand_name' => $request->brand_name,
                'created_at' => Carbon::now(),
            ]);

            return Redirect()->back()->with('success', 'Brand Updated Successfully!');
        }
 
    }

    public function Delete($id)
    {
        $image = Brand::find($id);
        $old_image = $image->brand_image;
        unlink($old_image);

        $delete = Brand::find($id)->delete();
        return Redirect()->back()->with('success', 'Brand Deleted Successfully!');

    }


    // Multi Image All Methods
    public function Multipic()
    {
        $images = Multipic::all();
        return view('admin.multipic.index', compact('images'));

    }

    public function StoreImg(Request $request)
    {
        $image = $request->file('image');

        foreach($image as $multi_img) {
            // Using intervention image package laravel
            $name_gen = hexdec(uniqid()).'.'.$multi_img->getClientOriginalExtension();
            Image::make($multi_img)->resize(300,300)->save('image/multi/'.$name_gen);

            $last_img = 'image/multi/'.$name_gen;


            Multipic::insert([
                'image' => $last_img,
                'created_at' => Carbon::now(),
            ]);

        }

        return Redirect()->back()->with('success', 'Image Inserted Successfully!');
    }

}
