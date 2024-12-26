<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\BaseModel;

class Medication extends BaseModel
{
    
  
    use HasFactory;

    protected $fillable = [
        'med_id', 
        'name', 
        'ndc', 
        'upc', 
        'item_number', 
        'package_size', 
        'manufacturer'
    ];

}
