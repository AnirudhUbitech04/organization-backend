<?php

namespace App\Models;

use CodeIgniter\Model;

class OrganizationModel extends Model
{
    protected $table      = 'organization';
    protected $primaryKey = 'id';

    protected $allowedFields = ['org_name', 'email', 'password', 'created_date'];

    public function getAllOrganizations()
    {
        return $this->findAll();
    }
}
