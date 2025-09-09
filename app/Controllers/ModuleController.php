<?php

namespace App\Controllers;
use App\Models\ModuleModel;
use CodeIgniter\RESTful\ResourceController;

class ModuleController extends ResourceController
{
    protected $format = 'json';

    // GET /api/modules
    public function index()
    {
        $model = new ModuleModel();
        $data = $model->findAll(); // fetch all records
        return $this->respond($data); // return JSON response
    }
}
