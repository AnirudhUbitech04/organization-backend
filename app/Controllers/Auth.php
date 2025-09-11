<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OrganizationModel;

class Auth extends ResourceController
{
    protected $format = 'json';
    protected $organizationModel;

    public function __construct()
    {
        $this->organizationModel = new OrganizationModel();
    }

    // Fetch all organizations from DB
    public function index()
    {
        $data = $this->organizationModel->getAllOrganizations();

        return $this->respond([
            'success' => true,
            'message' => 'Organizations fetched successfully',
            'data'    => $data
        ]);
    }

    // Handle CORS Preflight
    public function options()
    {
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setStatusCode(200);
    }

    // Login
    public function login()
    {
        // Always set CORS headers
        $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        $request = $this->request->getJSON(true);

        if (empty($request['email']) || empty($request['password'])) {
            return $this->respond([
                'success' => false,
                'message' => 'Email and password required'
            ], 400);
        }

        $user = $this->organizationModel->where('email', $request['email'])->first();

        if (!$user) {
            return $this->respond([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // ✅ Plain-text password check
        if ($request['password'] !== $user['password']) {
            return $this->respond([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Save login time
        $this->organizationModel->update($user['id'], [
            'login_time' => date('Y-m-d H:i:s')
        ]);

        return $this->respond([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id'    => $user['id'],
                'name'  => $user['org_name'],
                'email' => $user['email']
            ]
        ]);
    }

    // Logout
    public function logout($id = null)
    {
        return $this->respond([
            'success' => true,
            'message' => "User {$id} logged out successfully"
        ]);
    }
}












// namespace App\Controllers;
// use CodeIgniter\RESTful\ResourceController;
// use App\Models\OrganizationModel;

// class Auth extends ResourceController
// {
//     protected $format = 'json';
//     protected $organizationModel;

//     public function __construct()
//     {
//         $this->organizationModel = new OrganizationModel();
//     }

//     public function options()
//     {
//         return $this->response
//             ->setHeader('Access-Control-Allow-Origin', '*')
//             ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE')
//             ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
//             ->setStatusCode(200);
//     }
//     public function login()
//     {
//          $this->response
//             ->setHeader('Access-Control-Allow-Origin', '*')
//             ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
//             ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

//         $request = $this->request->getJSON(true);

//         if (empty($request['email']) || empty($request['password'])) {
//             return $this->respond(['success' => false, 'message' => 'Email and password required'], 400);
//         }

//         $user = $this->organizationModel->where('email', $request['email'])->first();

//         if (!$user || $user['password'] !== $request['password']) {
//             return $this->respond(['success' => false, 'message' => 'Invalid email or password'], 401);
//         }

//         // Save login time
//         $this->organizationModel->update($user['id'], ['login_time' => date('Y-m-d H:i:s')]);

//         return $this->respond([
//             'success' => true,
//             'message' => 'Login successful',
//             'user' => [
//                 'id' => $user['id'],
//                 'name' => $user['org_name'],
//                 'email' => $user['email']
//             ]
//         ]);
//     }
    
// }
