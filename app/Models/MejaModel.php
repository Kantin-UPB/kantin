<?php

namespace App\Models;

use CodeIgniter\Model;

class MejaModel extends Model
{
  
    protected $table            = 'meja';
    protected $primaryKey       = 'id_meja';
    protected $useAutoIncrement = false;
    protected $allowedFields    = ['status'];
    
}