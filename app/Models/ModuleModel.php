<?php

namespace App\Models;
use CodeIgniter\Model;

class ModuleModel extends Model
{
    protected $table = 'module';
    protected $primaryKey = 'mod_id';
    protected $allowedFields = ['module_name', 'status', 'org_id'];
}
