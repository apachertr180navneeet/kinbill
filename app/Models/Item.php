<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Item extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'company_id', 'variation_id', 'name', 'description', 'tax_id' // Add all the attributes you want to be mass assignable
    ];

    // Define the relationship
    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }
}
