<?php

namespace App\Controllers;

use App\Models\OrganizationModel;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{
    protected $format = 'json';
    protected $organizationModel;

    public function __construct()
    {
        $this->organizationModel = new OrganizationModel();
    }

    // Login endpoint
    public function login()
    {
        $request = $this->request->getJSON(true);

        if (empty($request['email']) || empty($request['password'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Email and password are required'
            ]);
        }

        $user = $this->organizationModel->where('email', $request['email'])->first();

        if (!$user || $user['password'] !== $request['password']) {
            // If passwords are hashed: use password_verify($request['password'], $user['password'])
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Invalid email or password'
            ]);
        }

        // Successful login
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'name' => $user['org_name'],
                'email' => $user['email']
            ]
        ])->setHeader('Access-Control-Allow-Origin', '*')
          ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
          ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    // Handle OPTIONS preflight
    public function options()
    {
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setStatusCode(200);
    }

    // Optional: GET all organizations
    public function index()
    {
        $response = $this->organizationModel->getAllOrganizations();
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setJSON($response);
    }
}
