<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_name'     ,
        'firstname'        ,
        'lastname'         ,
        'dob'              ,
        'address'          ,
        'city'             ,
        'state'            ,
        'phone_number'     ,
        'email'            ,
        'rx_number'        ,
        'rx_numbers_data'  ,
        'tracking_number'  ,
        'ship_by_date'     ,
        'labeled_date'     ,
        'shipped_date'     ,
        'delivered_date'   ,
        'shipping_label'   ,
        'created_at'       ,
        'updated_at'       ,
        'pharmacy_store_id',
        'status'           ,
        'user_id'          ,
        'file_id'          ,
        'import_excel_file_id',
        'is_completed'         
    ];

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
