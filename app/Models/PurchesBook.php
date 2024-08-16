<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchesBook extends Model
{
    use HasFactory;

    protected $table = 'purches_books';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date', 'invoice_number', 'vendor_id', 'transport', 'total_tax', 'other_expense', 'discount', 'round_off', 'grand_total' , 'status' // Add all the attributes you want to be mass assignable
    ];
}
