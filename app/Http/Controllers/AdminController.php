<?php


namespace App\Http\Controllers;

use App\Http\Models\Admin;
use App\Http\Models\Categories;
use App\Http\Models\CustomerProducts;
use App\Http\Models\Customers;
use App\Http\Models\Invoices;
use App\Http\Models\Products;
use App\Http\Models\Employees;
use App\Http\Utils\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\In;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\Facade as PDF;

class AdminController
{

    public function index()
    {
        $user = session()->get('user');
        $user_type = session()->get('user-type');

        if (isset($user) && isset($user_type)) {
            if ($user_type == 3) {
                return redirect('/my-page');
            } else {
                return redirect('/dashboard');
            }
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

            $user = Employees::where('email', $email)->first();
            if (!isset($user)) {

                $customer = Customers::where('email', $email)->first();
                if (!isset($customer)) {
                    session()->flash('error-msg', 'User not found.');
                    return redirect()->back();
                }
                if (!hash::check($password, $customer->password)) {
                    session()->flash('error-msg', 'Invalid password.');
                    return redirect()->back();
                }
                session()->put('user', $customer);
                session()->put('user-type', 3);
                return redirect('/my-page');
            }

            if (!hash::check($password, $user->password)) {
                session()->flash('error-msg', 'Invalid password.');
                return redirect()->back();
            }

            session()->put('user', $user);
            session()->put('user-type', 2);
            return redirect('/dashboard');
        }

        if (!hash::check($password, $admin->password)) {
            session()->flash('error-msg', 'Invalid password.');
            return redirect()->back();
        }

        session()->put('user', $admin);
        session()->put('user-type', 1);
        return redirect('/dashboard');
    }

    public function logout()
    {
        session()->remove('user');
        return redirect('/login');
    }

    public function dashboard()
    {
        $employees = Employees::count();
        $customers = Customers::count();
        $products = Products::count();
        $categories = Categories::count();
        return view('dashboard')->with([
            'employees' => $employees,
            'customers' => $customers,
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function showProfilePage() {
        $user = session()->get('user');
        return view('profile')->with('user', $user);
    }

    public function showMyPage() {
        $user = session()->get('user');
        $user_type = session()->get('user-type');

        if ($user_type == 3) {
            return redirect("/customers/detail/$user->id");
        } else {
            return view('profile')->with('user', $user);
        }
    }

    public function editProfile() {
        $id = request('id');
        $first_name = request('first-name');
        $last_name = request('last-name');
        $email = request('email');
        $password = request('password');

        request()->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'first-name' => 'required',
            'last-name' => 'required',
            'email' => 'required|email',
        ]);

        $update_array = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
        );

        if ($password != '') {
            $update_array['password'] = hash::make($password);
        }
        if (isset(request()->image)) {
            $imageName = time() . '.' . request()->image->getClientOriginalExtension();

            $original_image_path = public_path('media/avatars');
            if (!file_exists($original_image_path)) {
                mkdir($original_image_path);
            }

            request()->image->move($original_image_path, $imageName);
            $update_array['avatar'] = $imageName;
        }

        $isAdmin = session()->get('user-type');
        if($isAdmin === 1) {
            Admin::where('id', $id)->update($update_array);
            session()->put('user', Admin::where('id', $id)->first());
        } else {
            Employees::where('id', $id)->update($update_array);
            session()->put('user', Employees::where('id', $id)->first());
        }

        return back()
            ->with('success', 'You have successfully updated your profile.');
    }

    public function showEmployeesPage()
    {
        $employees = Employees::get();
        return view('employees')->with('employees', $employees);
    }

    public function showEmployeeAddPage()
    {
        return view('employee_add');
    }

    public function showEmployeeEditPage()
    {
        $id = request('id');
        $employee = Employees::where('id', $id)->first();
        return view('employee_edit')->with('employee', $employee);
    }

    public function addEmployee()
    {
        $first_name = request('first-name');
        $last_name = request('last-name');
        $email = request('email');
        $password = request('password');

        request()->validate([
            'first-name' => 'required',
            'last-name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $employee = new Employees();
        $employee->first_name = $first_name;
        $employee->last_name = $last_name;
        $employee->email = $email;
        $employee->password = hash::make($password);

        $employee->save();

        return back()
            ->with('success', "You have successfully add new employee's account.");
    }

    public function editEmployee()
    {
        $id = request('id');
        $first_name = request('first-name');
        $last_name = request('last-name');
        $email = request('email');
        $password = request('password');

        request()->validate([
            'first-name' => 'required',
            'last-name' => 'required',
            'email' => 'required|email',
        ]);

        if ($password != '') {
            Employees::where('id', $id)->update([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password' => hash::make($password),
            ]);
        } else {
            Employees::where('id', $id)->update([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
            ]);
        }

        return back()
            ->with('success', 'You have successfully updated employee\'s account.');
    }

    public function delEmployee()
    {
        $id = request('id');
        Employees::where('id', $id)->delete();

        return Utils::makeResponse();
    }

    public function toggleEmployeeEnable()
    {
        $id = request('id');
        $enable_flag = Employees::where('id', $id)->first()->enable_flag;

        Employees::where('id', $id)->update([
            'enable_flag' => 1 - $enable_flag,
        ]);

        return Utils::makeResponse();
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

    public function showCustomerEditPage()
    {
        $id = request('id');
        $customer = Customers::where('id', $id)->first();
        if ($customer != null) {
            return view('customer_edit')->with([
                'customer' => $customer
            ]);
        }
        return redirect('/customers');
    }

    public function showCustomerDetailPage()
    {
        $id = request('id');
        $customer = Customers::where('id', $id)->first();

        // Permission check
        $user_type = session()->get('user-type');
        $user = session()->get('user');
        if($user_type === 3 && $user->id != $id) {
            return redirect('/my-page');
        }

        $products = DB::select("
        SELECT
            t1.*,
        IF
            ( t2.id, 1, 0 ) has_flag 
        FROM
            t_products t1
            LEFT JOIN ( SELECT * FROM t_customer_products WHERE customer_id = $id ) t2 ON t1.id = t2.product_id 
        WHERE
            t1.show_flag = 1 
        ORDER BY
            has_flag
        ");
        if ($customer != null) {
            return view('customer_detail')->with([
                'customer' => $customer,
                'products' => $products
            ]);
        }
        return redirect('/customers');
    }

    public function addCustomer()
    {
        $first_name = request('first-name');
        $last_name = request('last-name');
        $email = request('email');
        $password = request('password');
        $birthday = request('birthday');
        $gender = request('gender');
        $phonenumber = request('phone-number');
        $company = request('company');
        $address = request('address');
        $city = request('city');
        $state = request('state');
        $zipcode = request('zip-code');
        $start_date = request('start-date');
        $expire_date = request('expire-date');
        $price = request('price');

        request()->validate([
            'first-name' => 'required',
            'last-name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'birthday' => 'required|date',
            'phone-number' => 'required',
            'start-date' => 'required|date',
            'expire-date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        $birthday = strtotime($birthday);
        $birthday = date('Y-m-d', $birthday);

        $start_date = strtotime($start_date);
        $start_date = date('Y-m-d', $start_date);

        $expire_date = strtotime($expire_date);
        $expire_date = date('Y-m-d', $expire_date);

        $customer = new Customers();
        $customer->first_name = $first_name;
        $customer->last_name = $last_name;
        $customer->email = $email;
        $customer->password = hash::make($password);
        $customer->birthday = $birthday;
        $customer->gender = $gender;
        $customer->phonenumber = $phonenumber;
        $customer->company = $company;
        $customer->address = $address;
        $customer->city = $city;
        $customer->state = $state;
        $customer->zipcode = $zipcode;
        $customer->start_date = $start_date;
        $customer->expire_date = $expire_date;
        $customer->price = $price;

        $customer->save();

        $invoice = new Invoices();
        $invoice->customer_id = $customer->id;
        $invoice->start_date = $start_date;
        $invoice->expire_date = $expire_date;
        $invoice->price = $price;

        $invoice->save();

        Customers::where('id', $customer->id)->update([
            'current_invoice_id' => $invoice->id
        ]);

        return back()
            ->with('success', 'You have successfully add new customer.');
    }

    public function editCustomer()
    {
        $id = request('id');
        $first_name = request('first-name');
        $last_name = request('last-name');
        $email = request('email');
        $password = request('password');
        $birthday = request('birthday');
        $gender = request('gender');
        $phonenumber = request('phone-number');
        $company = request('company');
        $address = request('address');
        $city = request('city');
        $state = request('state');
        $zipcode = request('zip-code');
        $start_date = request('start-date');
        $expire_date = request('expire-date');
        $price = request('price');

        request()->validate([
            'first-name' => 'required',
            'last-name' => 'required',
            'email' => 'required|email',
            'birthday' => 'required|date',
            'phone-number' => 'required',
            'start-date' => 'required|date',
            'expire-date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        $birthday = strtotime($birthday);
        $birthday = date('Y-m-d', $birthday);

        $start_date = strtotime($start_date);
        $start_date = date('Y-m-d', $start_date);

        $expire_date = strtotime($expire_date);
        $expire_date = date('Y-m-d', $expire_date);

        if ($password != '') {
            Customers::where('id', $id)->update([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password' => hash::make($password),
                'birthday' => $birthday,
                'gender' => $gender,
                'phonenumber' => $phonenumber,
                'company' => $company,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zipcode' => $zipcode,
                'start_date' => $start_date,
                'expire_date' => $expire_date,
                'price' => $price,
            ]);
        } else {
            Customers::where('id', $id)->update([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'birthday' => $birthday,
                'gender' => $gender,
                'phonenumber' => $phonenumber,
                'company' => $company,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zipcode' => $zipcode,
                'start_date' => $start_date,
                'expire_date' => $expire_date,
                'price' => $price,
            ]);
        }

        return back()
            ->with('success', 'You have successfully updated the customer\'s account');
    }

    public function resuscitateCustomer()
    {
        $id = request('id');
        $start_date = request('start-date');
        $expire_date = request('expire-date');
        $price = request('price');
        $add_flag = request('add_flag');

        request()->validate([
            'start-date' => 'required|date',
            'expire-date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        $start_date = date('Y-m-d', strtotime($start_date));
        $expire_date = date('Y-m-d', strtotime($expire_date));

        if ($add_flag == 1) {
            $invoice = new Invoices();
            $invoice->customer_id = $id;
            $invoice->start_date = $start_date;
            $invoice->expire_date = $expire_date;
            $invoice->price = $price;

            $invoice->save();

            Customers::where('id', $id)->update([
                'start_date' => $start_date,
                'expire_date' => $expire_date,
                'price' => $price,
                'current_invoice_id' => $invoice->id,
            ]);
        } else {
            Invoices::where('id', Customers::where('id', $id)->first()->current_invoice_id)->update([
                'start_date' => $start_date,
                'expire_date' => $expire_date,
                'price' => $price,
            ]);

            Customers::where('id', $id)->update([
                'start_date' => $start_date,
                'expire_date' => $expire_date,
                'price' => $price,
            ]);
        }



        return Utils::makeResponse();
    }

    public function delCustomer()
    {
        $id = request('id');
        Customers::where('id', $id)->delete();

        return Utils::makeResponse();
    }

    public function toggleCustomerEnable()
    {
        $id = request('id');
        $enable_flag = Customers::where('id', $id)->first()->enable_flag;

        Customers::where('id', $id)->update([
            'enable_flag' => 1 - $enable_flag,
        ]);

        return Utils::makeResponse();
    }

    public function toggleCustomerAddProduct()
    {
        $customer_id = request('customer_id');
        $product_id = request('product_id');
        $exist_flag = CustomerProducts::where([
            'customer_id' => $customer_id,
            'product_id' => $product_id,
        ])->exists();

        if ($exist_flag) {
            CustomerProducts::where([
                'customer_id' => $customer_id,
                'product_id' => $product_id,
            ])->delete();
        } else {
            $customer_product = new CustomerProducts();
            $customer_product->customer_id = $customer_id;
            $customer_product->product_id = $product_id;
            $customer_product->save();
        }

        return Utils::makeResponse();
    }

    public function printCustomerInvoice()
    {
        $id = request('id');
        $customer = Customers::where('id', $id)->first();
        $invoices = Invoices::where('customer_id', $id)->get();
        $total = Invoices::where('customer_id', $id)->sum('price');

        $pdf = PDF::loadView('customer_invoice_pdf', [
            'customer' => $customer,
            'invoices' => $invoices,
            'total' => $total,
            ]);
        //$pdf->save(storage_path('app/public') . '_filename.pdf');
        return $pdf->download('customer_invoice.pdf');

    }

    public function showCustomerInvoicePrintPreviewPage() {

        $id = request('id');
        $customer = Customers::where('id', $id)->first();
        $invoices = Invoices::where('customer_id', $id)->get();
        $total = Invoices::where('customer_id', $id)->sum('price');

        return view('customer_invoice_print_preview')->with([
            'customer' => $customer,
            'invoices' => $invoices,
            'total' => $total,
        ]);
    }

    public function showProductFirstPage()
    {
        if (session()->get('user-type') != 3) {
            $customers_cnt = Customers::count();
            if ($customers_cnt > 0) {
                $customers = Customers::get();
                return redirect('products/' . $customers[0]->id);
            }

            return view('customer_add')->with([
                'warning' => 'Warning'
            ]);
        }
        return redirect('products/'.session()->get('user')->id);
    }

    public function showProductsPage()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') != 3 || $customer_id == session()->get('user')->id) {
            $products = Products::where('customer_id', $customer_id)->with('category')->get();
            $customers = Customers::get();
            return view('products')->with([
                'products' => $products,
                'customer_id' => $customer_id,
                'customers' => $customers
            ]);
        }
        return redirect('products/'.session()->get('user')->id);
    }

    public function showProductAddPage()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') == 3) {
            $customer_id = session()->get('user')->id;
        }
        $categories = Categories::where('customer_id', $customer_id)->get();
        return view('product_add')->with([
            'categories' => $categories,
            'customer_id' => $customer_id,
        ]);
    }

    public function showProductEditPage()
    {
        $id = request('id');
        $product = Products::where('id', $id)->first();
        $categories = Categories::where('customer_id', $product->customer_id)->get();
        if ($product != null) {
            return view('product_edit')->with([
                'product' => $product,
                'categories' => $categories
            ]);
        }
        return redirect('/products');
    }

    public function showProductDetailPage()
    {
        $id = request('id');
        $product = Products::where('id', $id)->first();
        $categories = Categories::get();
        if ($product != null) {
            return view('product_detail')->with([
                'product' => $product,
                'categories' => $categories
            ]);
        }
        return redirect('/products');
    }

    public function addProduct()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') == 3) {
            $customer_id = session()->get('user')->id;
        }
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
            'category' => 'required',
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
        $product->customer_id = $customer_id;
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
            ->with('success', 'You have successfully updated the product.');
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

    public function showCategoryFirstPage()
    {
        if (session()->get('user-type') != 3) {
            $customers_cnt = Customers::count();
            if ($customers_cnt > 0) {
                $customers = Customers::get();
                return redirect('categories/' . $customers[0]->id);
            }

            return view('customer_add')->with([
                'warning' => 'Warning'
            ]);
        }
        return redirect('categories/'.session()->get('user')->id);
    }

    public function showCategoriesPage()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') != 3 || $customer_id == session()->get('user')->id) {
            $categories = Categories::where('customer_id', $customer_id)->get();
            $customers = Customers::get();
            return view('categories')->with([
                'categories' => $categories,
                'customer_id' => $customer_id,
                'customers' => $customers
            ]);
        }
        return redirect('categories/'.session()->get('user')->id);
    }

    public function showCategoryAddPage()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') == 3) {
            $customer_id = session()->get('user')->id;
        }
        return view('category_add')->with('customer_id', $customer_id);
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

    public function showCategoryDetailPage()
    {
        $id = request('id');
        $category = Categories::where('id', $id)->first();
        $products = Products::where('category_id', $id)->get();
        if ($category != null) {
            return view('category_detail')->with([
                'category' => $category,
                'products' => $products
            ]);
        }
        return redirect('/categories');
    }

    public function addCategory()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') == 3) {
            $customer_id = session()->get('user')->id;
        }
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
        $category->customer_id = $customer_id;
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
