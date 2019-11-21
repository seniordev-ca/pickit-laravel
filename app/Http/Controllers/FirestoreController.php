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
use Google\Cloud\Firestore\FirestoreClient;
use Google\Cloud\Firestore\Transaction;
use Hamcrest\Util;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\In;
use Intervention\Image\Facades\Image;
use Barryvdh\DomPDF\Facade as PDF;

class FirestoreController
{
    public function testFirestore() {
        // Create the Cloud Firestore client
        $db = new FirestoreClient([
            'projectId' => 'pick-it-apps',
        ]);
        printf('Created Cloud Firestore client with default project ID.' . PHP_EOL);
        $usersRef = $db->collection('Category');
        $snapshot = $usersRef->documents();
        foreach ($snapshot as $user) {
            printf('User: %s' . PHP_EOL, $user->id());
            printf('First: %s' . PHP_EOL, $user['first']);
            if (!empty($user['middle'])) {
                printf('Middle: %s' . PHP_EOL, $user['middle']);
            }
            printf('Last: %s' . PHP_EOL, $user['last']);
            printf('Born: %d' . PHP_EOL, $user['born']);
            printf(PHP_EOL);
        }
        printf('Retrieved and printed out all documents from the users collection.' . PHP_EOL);
    }

    public function showCategoriesPage()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') != 3 || $customer_id == session()->get('user')->id) {

            $categoryRef = Utils::getCategoryDBRef();
            $documents = $categoryRef->documents(['customer_id', '=', $customer_id]);

            $categories = [];
            foreach ($documents as $document) {
                if ($document->exists()) {
                    $categories[] = (object) $document->data();
                } else {
                    printf('Document %s does not exist!' . PHP_EOL, $document->id());
                }
            }

            $customers = Customers::get();
            return view('categories')->with([
                'categories' => $categories,
                'customer_id' => $customer_id,
                'customers' => $customers
            ]);
        }
        return redirect('categories/'.session()->get('user')->id);
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
        $order = request('order');
        $direction = request('rtl-direction');

        request()->validate([
            'category-name' => 'required',
        ]);

        $rtl_direction = 0;
        if (isset($direction) && $direction == 'on')
            $rtl_direction = 1;

        $docRef = Utils::getCategoryDBRef()->add();
        $docRef->set([
            'id' => $docRef->id(),
            'customer_id' => $customer_id,
            'name' => $name,
            'name_second' => $name_ar,
            'tags' => $tags,
            'tags_second' => $tags_ar,
            'order' => $order,
            'is_right_direction' => $rtl_direction,
            'is_active' => false
        ]);

        return back()
            ->with('success', 'You have successfully add new category.');
    }

    public function showCategoryEditPage()
    {
        $id = request('id');

        $categoryRef = Utils::getCategoryDBRef();
        $documents = $categoryRef->documents(['id', '=', $id]);

        foreach ($documents as $document) {
            if ($document->exists()) {
                $category = (object) $document->data();
            } else {
                printf('Document %s does not exist!' . PHP_EOL, $document->id());
            }
        }

        if (isset($category)) {
            return view('category_edit')->with([
                'category' => $category
            ]);
        }
        return redirect('/categories');
    }

    public function editCategory()
    {
        $id = request('id');
        $name = request('category-name');
        $name_ar = request('category-name-ar');
        $tags = request('category-tags');
        $tags_ar = request('category-tags-ar');
        $order = request('order');
        $direction = request('rtl-direction');

        request()->validate([
            'category-name' => 'required',
        ]);

        $rtl_direction = 0;
        if (isset($direction) && $direction == 'on')
            $rtl_direction = 1;

        $docRef = Utils::getCategoryDBRef()->document($id);
        $docRef->set([
            'name' => $name,
            'name_second' => $name_ar,
            'tags' => $tags,
            'tags_second' => $tags_ar,
            'order' => $order,
            'is_right_direction' => $rtl_direction,
        ], ['merge' => true]);

        return back()
            ->with('success', 'You have successfully updated category.');
    }

    public function delCategory()
    {
        $id = request('id');
        Utils::getCategoryDBRef()->document($id)->delete();

        return Utils::makeResponse();
    }

    public function toggleCategoryVisible()
    {
        $id = request('id');
        $docRef = Utils::getCategoryDBRef()->document($id);
        $snapshot = $docRef->snapshot();

        if ($snapshot->exists()) {
            $docRef->set([
                'is_active' => $snapshot->data()['is_active'] ? false : true
            ], ['merge' => true]);
        } else {
            printf('Document %s does not exist!' . PHP_EOL, $snapshot->id());
        }

//        Utils::getFirestoreDB()->runTransaction(function (Transaction $transaction) use ($docRef) {
//            $snapshot = $transaction->snapshot($docRef);
//            $newValue = $snapshot['is_active'] ? false : true;
//            $transaction->update($docRef, [
//                ['path' => 'is_active', 'value' => $newValue]
//            ]);
//        });

        return Utils::makeResponse();
    }

    public function toggleCategoryAllVisible() {
        $user_type = session()->get('user-type');
        if ($user_type === 1 || $user_type === 2) {

            $documents = Utils::getCategoryDBRef()->documents();
            foreach ($documents as $document) {
                if ($document->exists()) {
                    Utils::getCategoryDBRef()->document($document->data()['id'])->set([
                        'is_active' => true
                    ], ['merge' => true]);
                } else {
                    printf('Document %s does not exist!' . PHP_EOL, $document->id());
                }
            }

        } else {
            $customer_id = session()->get('user')->id;
            $documents = Utils::getCategoryDBRef()->documents(['customer_id', '=', $customer_id]);
            foreach ($documents as $document) {
                if ($document->exists()) {
                    Utils::getCategoryDBRef()->document($document->data()['id'])->set([
                        'is_active' => true
                    ], ['merge' => true]);
                } else {
                    printf('Document %s does not exist!' . PHP_EOL, $document->id());
                }
            }
        }
        return back();
    }

    public function toggleCategoryAllInvisible() {
        $user_type = session()->get('user-type');
        if ($user_type === 1 || $user_type === 2) {

            $documents = Utils::getCategoryDBRef()->documents();
            foreach ($documents as $document) {
                if ($document->exists()) {
                    Utils::getCategoryDBRef()->document($document->data()['id'])->set([
                        'is_active' => false
                    ], ['merge' => true]);
                } else {
                    printf('Document %s does not exist!' . PHP_EOL, $document->id());
                }
            }

        } else {
            $customer_id = session()->get('user')->id;
            $documents = Utils::getCategoryDBRef()->documents(['customer_id', '=', $customer_id]);
            foreach ($documents as $document) {
                if ($document->exists()) {
                    Utils::getCategoryDBRef()->document($document->data()['id'])->set([
                        'is_active' => false
                    ], ['merge' => true]);
                } else {
                    printf('Document %s does not exist!' . PHP_EOL, $document->id());
                }
            }
        }
        return back();
    }


    public function showProductAddPage()
    {
        $customer_id = request('customer_id');
        if (session()->get('user-type') == 3) {
            $customer_id = session()->get('user')->id;
        }

        $categoryRef = Utils::getCategoryDBRef();
        $documents = $categoryRef->documents([
            ['customer_id', '=', $customer_id],
            ['is_active', '=', true]
        ]);
        $categories = [];
        foreach ($documents as $document) {
            if ($document->exists()) {
                $categories[] = (object) $document->data();
            } else {
                printf('Document %s does not exist!' . PHP_EOL, $document->id());
            }
        }

        $currency_list = Currency::get();
        return view('product_add')->with([
            'categories' => $categories,
            'customer_id' => $customer_id,
            'currency_list' => $currency_list
        ]);
    }

    public function addProduct() {
        $customer_id = request('customer_id');
        if (session()->get('user-type') == 3) {
            $customer_id = session()->get('user')->id;
        }
        $name = request('product-name');
        $name_ar = request('product-name-ar');
        $category_id = request('category');
        $price = request('product-price');
        $video_url = request('video-url');
        $description = request('product-description');
        $description_ar = request('product-description-ar');
        $currency = request('currency');
        $direction = request('rtl-direction');

        request()->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'product-name' => 'required',
            'product-price' => 'required',
            'category' => 'required',
            'currency' => 'required',
        ]);

//        print_r(request()->image->getRealPath());
//        die();


        $rtl_direction = 0;
        if (isset($direction) && $direction == 'on')
            $rtl_direction = 1;

        $imageName = time() . '.' . request()->image->getClientOriginalExtension();

        $original_image_path = public_path('media/images/products/original');
        if (!file_exists($original_image_path)) {
            mkdir($original_image_path);
        }

        $appview_image_path = public_path('media/images/products/appview');
        if (!file_exists($appview_image_path)) {
            mkdir($appview_image_path);
        }

        $thumbnail_image_path = public_path('media/images/products/thumbnail');
        if (!file_exists($thumbnail_image_path)) {
            mkdir($thumbnail_image_path);
        }

        //Save original image
        request()->image->move($original_image_path, $imageName);

        // generate appview image
        Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
            ->resize(1200, 1200, function($constraint) {
                $constraint->aspectRatio();
            })
            ->save($appview_image_path . DIRECTORY_SEPARATOR . $imageName);

        // generate thumbnail image
        Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
            ->fit(320, 320)
            ->save($thumbnail_image_path . DIRECTORY_SEPARATOR . $imageName);

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match))
            $video_id = $match[1];
        else $video_id = $video_url;


        $docRef = Utils::getProductDBRef()->add();
        $docRef->set([
            'id' => $docRef->id(),
            'customer_id' => $customer_id,
            'category_id' => $category_id,
            'name' => $name,
            'name_second' => $name_ar,
            'price' => $price,
            'currency' => $currency,
            'description' => $description,
            'description_second' => $description_ar,
            'image' => $imageName,
            'video_id' => $video_id,
            'video_url' => $video_url,
            'is_right_direction' => $rtl_direction
        ]);

        Utils::getBucket()->upload(
            file_get_contents($original_image_path."/".$imageName),
            [
                'name' => "image/original/".$imageName
            ]
        );

        Utils::getBucket()->upload(
            Image::make($original_image_path . DIRECTORY_SEPARATOR . $imageName)
                ->resize(1200, 1200, function($constraint) {
                    $constraint->aspectRatio();
                }),
            [
                'name' => "image/appview/".$imageName
            ]
        );

        Utils::getBucket()->upload(
            file_get_contents($thumbnail_image_path."/".$imageName),
            [
                'name' => "image/thumbnail/".$imageName
            ]
        );

        return back()
            ->with('success', 'You have successfully add new product.');
    }
}
