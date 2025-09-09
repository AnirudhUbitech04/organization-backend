<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Location extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // GET /api/countries
    public function countries()
    {
        $builder = $this->db->table('country_master');
        $data = $builder->select('id, country_name AS name')->get()->getResult();
        return $this->response->setJSON($data);
    }

    // GET /api/states/{country_id}
    public function states($countryId = null)
    {
        if (!$countryId) {
            return $this->response->setJSON([]);
        }

        $builder = $this->db->table('state_master');
        $data = $builder->select('id, state_name AS name')
                        ->where('country_id', $countryId)
                        ->get()->getResult();

        return $this->response->setJSON($data);
    }

    // GET /api/cities/{state_id}
    public function cities($stateId = null)
    {
        if (!$stateId) {
            return $this->response->setJSON([]);
        }

        $builder = $this->db->table('city_master');
        $data = $builder->select('id, city_name AS name')
                        ->where('state_id', $stateId)
                        ->get()->getResult();

        return $this->response->setJSON($data);
    }
}
