<?php


namespace App\Http\Controllers;

use App\Http\Models\Admin;
use App\Http\Models\Categories;
use App\Http\Models\Customers;
use App\Http\Models\Products;
use App\Http\Models\Users;
use App\Http\Utils\Utils;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class AdminController
{

    public function index()
    {

        $admin = session()->get('admin');

        if (isset($admin)) {
            return redirect('/dashboard');
        } else {
            return view('login');
        }
    }

    public function doLogin()
    {

        $email = request('login-username');
        $password = request('login-password');

        if (!isset($email)) {
            session()->flash('error-msg', 'Please enter valid email.');
            return redirect()->back();
        }
        if (!isset($password)) {
            session()->flash('error-msg', 'Please enter valid password.');
            return redirect()->back();
        }

        $admin = Admin::where('email', $email)->first();

        if (!isset($admin)) {
            session()->flash('error-msg', 'User not found.');
            return redirect()->back();
        }

        if (!hash::check($password, $admin->password)) {
            session()->flash('error-msg', 'Invalid password.');
            return redirect()->back();
        }

        session()->put('admin', $admin);
        return redirect('/dashboard');
    }

    public function logout()
    {
        session()->remove('admin');
        return redirect('/login');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function showUsersPage()
    {
        $users = Users::get();
        return view('users')->with('users', $users);
    }

    public function showUserAddPage()
    {
        return view('user_add');
    }

    public function showUserEditPage()
    {
        return view('user_edit');
    }

    public function showCustomersPage()
    {
        $customers = Customers::get();
        return view('customers')->with('customers', $customers);
    }

    public function showCustomerAddPage()
    {
        return view('customer_add');
    }

    public function showProductsPage()
    {
        $products = Products::get();
        return view('products')->with('products', $products);
    }

    public function showProductAddPage()
    {
        $categories = Categories::get();
        return view('product_add')->with('categories', $categories);
    }

    public function showProductEditPage()
    {
        $id = request('id');
        $product = Products::where('id', $id)->first();
        $categories = Categories::get();
        if ($product != null) {
            return view('product_edit')->with([
                'product' => $product,
                'categories' => $categories
            ]);
        }
        return redirect('/products');
    }

    public function addProduct()
    {
        $name = request('product-name');
        $name_ar = request('product-name-ar');
        $category_id = request('category');
        $price = request('product-price');
        $description = request('product-description');
        $description_ar = request('product-description-ar');

        request()->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'product-name' => 'required',
            'product-name-ar' => 'required',
            'product-price' => 'required',
            'category' => 'required'
        ]);

        $imageName = time() . '.' . request()->image->getClientOriginalExtension();

        $original_image_path = public_path('media/images/products/original');
        if (!file_exists($original_image_path)) {
            mkdir($original_image_path);
        }

        $thumbnail_image_path = public_path('media/images/products/thumbnail');
        if (!file_exists($thumbnail_image_path)) {
            mkdir($thumbnail_image_path);
        }

        //Save original image
        request()->image->move($original_image_path, $imageName);

        // generate thumbnail image
        Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
            ->fit(320, 320)
            ->save($thumbnail_image_path . DIRECTORY_SEPARATOR . $imageName);

        $product = new Products();
        $product->name = $name;
        $product->name_ar = $name_ar;
        $product->price = $price;
        $product->category_id = $category_id;
        $product->description = $description;
        $product->description_ar = $description_ar;
        $product->picture = $imageName;

        $product->save();

        return back()
            ->with('success', 'You have successfully add new product.');
    }

    public function editProduct()
    {
        $id = request('id');
        $name = request('product-name');
        $name_ar = request('product-name-ar');
        $category_id = request('category');
        $price = request('product-price');
        $description = request('product-description');
        $description_ar = request('product-description-ar');

        request()->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'product-name' => 'required',
            'product-name-ar' => 'required',
            'product-price' => 'required',
            'category' => 'required'
        ]);

        if (isset(request()->image)) {
            $imageName = time() . '.' . request()->image->getClientOriginalExtension();

            $original_image_path = public_path('media/images/products/original');
            if (!file_exists($original_image_path)) {
                mkdir($original_image_path);
            }

            $thumbnail_image_path = public_path('media/images/products/thumbnail');
            if (!file_exists($thumbnail_image_path)) {
                mkdir($thumbnail_image_path);
            }

            //Save original image
            request()->image->move($original_image_path, $imageName);

            // generate thumbnail image
            Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
                ->fit(320, 320)
                ->save($thumbnail_image_path . DIRECTORY_SEPARATOR . $imageName);

            Products::where('id', $id)->update([
                'name' => $name,
                'name_ar' => $name_ar,
                'price' => $price,
                'category_id' => $category_id,
                'description' => $description,
                'description_ar' => $description_ar,
                'picture' => $imageName
            ]);
        } else {
            Products::where('id', $id)->update([
                'name' => $name,
                'name_ar' => $name_ar,
                'price' => $price,
                'category_id' => $category_id,
                'description' => $description,
                'description_ar' => $description_ar,
            ]);
        }
        return back()
            ->with('success', 'You have successfully updated product.');
    }

    public function delProduct()
    {
        $id = request('id');
        Products::where('id', $id)->delete();

        return Utils::makeResponse();
    }

    public function toggleProductVisible()
    {
        $id = request('id');
        $show_flag = Products::where('id', $id)->first()->show_flag;

        Products::where('id', $id)->update([
            'show_flag' => 1 - $show_flag,
        ]);

        return Utils::makeResponse();
    }

    public function showCategoriesPage()
    {
        $categories = Categories::get();
        return view('categories')->with('categories', $categories);
    }

    public function showCategoryAddPage()
    {
        return view('category_add');
    }

    public function showCategoryEditPage()
    {
        $id = request('id');
        $category = Categories::where('id', $id)->first();
        if ($category != null) {
            return view('category_edit')->with([
                'category' => $category
            ]);
        }
        return redirect('/categories');
    }

    public function addCategory()
    {
        $name = request('category-name');
        $name_ar = request('category-name-ar');
        $tags = request('category-tags');
        $tags_ar = request('category-tags-ar');

        request()->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'category-name' => 'required',
            'category-name-ar' => 'required',
            'category-tags' => 'required',
            'category-tags-ar' => 'required',
        ]);

        $imageName = time() . '.' . request()->image->getClientOriginalExtension();

        $original_image_path = public_path('media/images/categories/original');
        if (!file_exists($original_image_path)) {
            mkdir($original_image_path);
        }

        $thumbnail_image_path = public_path('media/images/categories/thumbnail');
        if (!file_exists($thumbnail_image_path)) {
            mkdir($thumbnail_image_path);
        }

        //Save original image
        request()->image->move($original_image_path, $imageName);

        // generate thumbnail image
        Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
            ->fit(320, 320)
            ->save($thumbnail_image_path . DIRECTORY_SEPARATOR . $imageName);

        $category = new Categories();
        $category->name = $name;
        $category->tags = $tags;
        $category->name_ar = $name_ar;
        $category->tags_ar = $tags_ar;
        $category->picture = $imageName;

        $category->save();

        return back()
            ->with('success', 'You have successfully add new category.')
            ->with('image', $imageName);
    }

    public function editCategory()
    {
        $id = request('id');
        $name = request('category-name');
        $name_ar = request('category-name-ar');
        $tags = request('category-tags');
        $tags_ar = request('category-tags-ar');

        request()->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'category-name' => 'required',
            'category-name-ar' => 'required',
            'category-tags' => 'required',
            'category-tags-ar' => 'required',
        ]);

        if (isset(request()->image)) {
            $imageName = time() . '.' . request()->image->getClientOriginalExtension();

            $original_image_path = public_path('media/images/categories/original');
            if (!file_exists($original_image_path)) {
                mkdir($original_image_path);
            }

            $thumbnail_image_path = public_path('media/images/categories/thumbnail');
            if (!file_exists($thumbnail_image_path)) {
                mkdir($thumbnail_image_path);
            }

            //Save original image
            request()->image->move($original_image_path, $imageName);

            // generate thumbnail image
            Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
                ->fit(320, 320)
                ->save($thumbnail_image_path . DIRECTORY_SEPARATOR . $imageName);
            Categories::where('id', $id)->update([
                'name' => $name,
                'tags' => $tags,
                'name_ar' => $name_ar,
                'tags_ar' => $tags_ar,
                'picture' => $imageName
            ]);
        } else {
            Categories::where('id', $id)->update([
                'name' => $name,
                'tags' => $tags,
                'name_ar' => $name_ar,
                'tags_ar' => $tags_ar
            ]);
        }
        return back()
            ->with('success', 'You have successfully updated category.');
    }

    public function delCategory()
    {
        $id = request('id');
        Categories::where('id', $id)->delete();
        Products::where('category_id', $id)->delete();

        return Utils::makeResponse();
    }

    public function toggleCategoryVisible()
    {
        $id = request('id');
        $show_flag = Categories::where('id', $id)->first()->show_flag;

        Categories::where('id', $id)->update([
            'show_flag' => 1 - $show_flag,
        ]);

        return Utils::makeResponse();
    }
}
