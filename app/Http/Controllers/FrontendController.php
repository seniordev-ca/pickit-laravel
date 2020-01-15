<?php


namespace App\Http\Controllers;

use App\Http\Models\Admin;
use App\Http\Models\Categories;
use App\Http\Models\Currency;
use App\Http\Models\CustomerProducts;
use App\Http\Models\Customers;
use App\Http\Models\Invoices;
use App\Http\Models\Products;
use App\Http\Models\Employees;
use App\Http\Utils\Utils;
use Hamcrest\Util;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\In;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\Facade as PDF;

class FrontendController
{
    public function showLoginPage() {
        return view('frontend.login');
    }

    public function showContactPage() {
        return view('frontend.contact');
    }

    public function showHomePage() {
        return view('frontend.home');
    }

    public function showProductPage()
    {
        $customer_id = request('customer_id');
        $lang = request('lang');
        $category_id = request('category');
        $search = request('search');

        if (!isset($lang))
            $lang = 'en';

        if (!isset($search))
            $search = '';

        $first_category = Categories::where([
            ['customer_id', $customer_id],
            ['show_flag', 1],
        ])->orderBy('show_order')->first();

        if (!isset($category_id)) {
            if ($first_category != null)
                $category_id = $first_category->id;
            else $category_id = 0;
        }

        $category_array = Categories::where([
            ['customer_id', $customer_id],
            ['show_flag', 1],
        ])->orderBy('show_order')->get();

        $theme = Customers::where('id', $customer_id)->first();
        if ($theme == null) {
            return redirect('/');
        }

        $search_clause = [];
        if ($search != "") {
            $search_clause[] = ['name', 'like', "%$search%"];
            $search_clause[] = ['name_second', 'like', "%$search%"];
            $search_clause[] = ['description', 'like', "%$search%"];
            $search_clause[] = ['description_second', 'like', "%$search%"];
        }

        $product_array = [];
        if ($category_id != 0) {
            $product_array = Products::where([
                ['category_id', $category_id],
                ['show_flag', 1]
            ])->where(function ($query) use ($search_clause) {
                if (count($search_clause) > 0) {
                    $query->where([$search_clause[0]]);
                    for ($i = 1; $i < count($search_clause); $i++) {
                        $query->orwhere([$search_clause[$i]]);
                    }
                }
            })->get();
        }

        $theme = $theme->setHidden([
            'password',
            'start_date',
            'expire_date',
            'price'
        ]);

        return view('frontend.sub_home')->with([
            'category_array' => $category_array,
            'product_array' => $product_array,
            'theme' => $theme,
            'category_id' => $category_id,
            'lang' => $lang,
            'search' => $search,
        ]);
    }

    public function getProductDetail() {
        $id = request('id');
        $product = Products::where('id', $id)->with('currency', 'category')->first();
        $product->setHidden([
            'currency_id',
            'category_id',
            'customer_id',
            'show_flag',
            'id'
        ]);
        if ($product != null) {
            return Utils::makeResponse(['product' => $product]);
        }
        return Utils::makeResponse();
    }
}
