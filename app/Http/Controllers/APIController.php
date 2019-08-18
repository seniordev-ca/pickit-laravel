<?php


namespace App\Http\Controllers;

use App\Http\Models\Categories;
use App\Http\Models\Customers;
use App\Http\Models\Products;
use App\Http\Utils\Utils;
use Illuminate\Support\Facades\Hash;
use Validator;

class APIController
{
    public function doLogin()
    {
        $validation = Validator::make(request()->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validation->fails()) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $credentials = request(['email', 'password']);

        $user = Customers::where([
            ['email', $credentials['email']],
            ['enable_flag', 1],
        ])->first();

        if ($user == null) {
            return Utils::makeResponse([], config('constants.response-message.invalid-credentials'));
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-credentials'));
        }

        if (!$token = auth()->attempt($credentials)) {
            return Utils::makeResponse([], config('constants.response-message.error-generate-api-token'));
        }

        $user = $user->setHidden([
            'password'
        ]);

        return Utils::makeResponse([
            'api_token' => $token,
            'user' => $user,
        ]);
    }

    public function getCategoriesByClient()
    {
        $email = request('email');
        $client = Customers::where('email', $email)->first();

        if (!isset($client)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $categories =Categories::where([
            ['customer_id', $client->id],
            ['show_flag', 1]
        ])->get();

        return Utils::makeResponse([
            'category_array' => $categories
        ]);

    }

    public function getProductsByCategory()
    {
        $category_id = request('category_id');
        if (!isset($category_id)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $products = Products::where([
            ['category_id', $category_id],
            ['show_flag', 1]
        ])->with('category')->get();

        return Utils::makeResponse([
            'product_array' => $products
        ]);

    }

    public function getProductDetail()
    {
        $product_id = request('product_id');
        if (!isset($product_id)) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        $product = Products::where([
            ['id', $product_id],
            ['show_flag', 1]
        ])->with('category')->first();

        if ($product == null) {
            return Utils::makeResponse([], config('constants.response-message.invalid-params'));
        }

        return Utils::makeResponse([
            'product' => $product
        ]);

    }



}
