<?php

namespace App\Models;

use CodeIgniter\Model;

class SevaBooking extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'seva_bookings';
    protected $primaryKey       = 'booking_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "date",
        "seva_category_id",
        "seva_id",
        "user_id",
        "user_name",
        "seva_type",
        "time",
        "amount",
        "gotra",
        "pan_no",
        "nakshatra",
        "rashi",
        "no_of_persons",
        "email_id",
        "mobile_no",
        "payment_type",
        "transaction_id"
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
