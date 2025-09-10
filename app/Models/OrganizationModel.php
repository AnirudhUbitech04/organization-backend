<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationModel extends Model
{
    protected $table      = 'organization';
    protected $primaryKey = 'id';

    protected $allowedFields = ['org_name', 'email', 'password', 'created_date',  'login_time', 'logout_time'];

    public function getAllOrganizations()
    {
        return $this->findAll();
    }
}
