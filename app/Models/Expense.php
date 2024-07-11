<?php

namespace App\Models;

use CodeIgniter\Model;

class Expense extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'expenses';
    protected $primaryKey       = 'expense_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "date",
        "voucher_no",
        "name",
        "payment_type",
        "from_account",
        "cheque_no",
        "bank_name",
        "expense_type",
        "expense_amount",
        "remarks",
        "address",
        "mobile_no"
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
