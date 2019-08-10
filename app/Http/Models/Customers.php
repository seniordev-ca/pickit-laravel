<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    //
    protected $table = 't_customers';

    public $timestamps = false;

    public function invoices()
    {
        return $this->hasMany(Invoices::class, 'customer_id', 'id');
    }

}
