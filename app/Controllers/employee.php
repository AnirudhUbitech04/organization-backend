<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Employee extends ResourceController
{
    protected $format = 'json';
    private $table = 'org_details';

    private function cors()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        if ($this->request->getMethod(true) === 'OPTIONS') exit;
    }

    // Step 1: Insert Personal Info
    public function insert()
    {
        $this->cors();
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        $data = $this->request->getPost();
        $file = $this->request->getFile('file');

        // Handle file upload
        $fileName = '';
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(WRITEPATH.'uploads')) mkdir(WRITEPATH.'uploads', 0777, true);
            $fileName = $file->getRandomName();
            $file->move(WRITEPATH.'uploads/', $fileName);
        }

        // Duplicate check
        $existing = $builder->groupStart()
                            ->where('fullname', $data['fullname'])
                            ->orWhere('email', $data['email'])
                            ->groupEnd()
                            ->get()
                            ->getRow();

        if ($existing) {
            if ($existing->fullname == $data['fullname']) {
                return $this->respond(['success'=>false,'field'=>'fullname','message'=>'Full name already registered']);
            }
            if ($existing->email == $data['email']) {
                return $this->respond(['success'=>false,'field'=>'email','message'=>'Email already registered']);
            }
        }

        $insertData = [
            'fullname' => $data['fullname'] ?? '',
            'email'    => $data['email'] ?? '',
            'phone'    => $data['phone'] ?? '',
            'mod_id'   => $data['mod_id'] ?? '',
            'org_id'   => $data['org_id'] ?? '',
        ];

        if ($fileName) $insertData['file'] = $fileName;

        try {
            $builder->insert($insertData);
            $insertedId = $db->insertID();
            return $this->respond(['success'=>true,'insertedId'=>$insertedId,'message'=>'Step 1 inserted successfully']);
        } catch (\Exception $e) {
            return $this->respond(['success'=>false,'message'=>$e->getMessage()],500);
        }
    }

    // Step 2: Update Additional Info
    public function update($id = null)
    {
        $this->cors();
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        $data = $this->request->getPost();
        $file = $this->request->getFile('file');

        $recordId = $data['id'] ?? $id;
        if (!$recordId) {
            return $this->respond(['success' => false, 'message' => 'Record ID is required for update']);
        }

        $updateData = [
            'address' => $data['address'] ?? '',
            'country' => $data['country_name'] ?? $data['country'] ?? '',
            'state'   => $data['state_name'] ?? $data['state'] ?? '',
            'city'    => $data['city_name'] ?? $data['city'] ?? '',
            'pincode' => $data['pincode'] ?? ''
        ];

        // Optional file upload
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(WRITEPATH.'uploads')) mkdir(WRITEPATH.'uploads', 0777, true);
            $fileName = $file->getName();
            $file->move(WRITEPATH.'uploads/', $fileName);
            $updateData['file'] = $fileName;
        }

        try {
            $builder->where('id', $recordId)->update($updateData);
            return $this->respond(['success' => true, 'message' => 'Step 2 updated successfully']);
        } catch (\Exception $e) {
            log_message('error', 'Employee Update Error: '.$e->getMessage());
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Step 3: Preview single record
    public function preview($id = null)
    {
        $this->cors();
        if (!$id) return $this->respond(['success'=>false,'message'=>'Record ID is required']);

        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        try {
            $data = $builder->where('id', $id)->get()->getRow();
            if (!$data) return $this->respond(['success'=>false,'message'=>'Record not found']);

            // Prepare image URL
            if ($data->file) {
                $data->fileUrl = base_url("employee/getImage/{$data->file}");
            }

            return $this->respond(['success'=>true,'data'=>$data]);
        } catch (\Exception $e) {
            log_message('error', 'Employee Preview Error: '.$e->getMessage());
            return $this->respond(['success'=>false,'message'=>$e->getMessage()], 500);
        }
    }

public function getImage($filename)
{
    $path = WRITEPATH . 'uploads/' . $filename;
    error_log("DEBUG getImage() called with filename: {$filename}");
    error_log("Resolved file path: {$path}");

    if (file_exists($path)) {
        $mime = mime_content_type($path);
        return $this->response->setHeader('Content-Type', $mime)
                              ->setBody(file_get_contents($path));
    } else {
        error_log("DEBUG: File not found at path: {$path}");
        return $this->response->setStatusCode(404, 'Image not found');
    }
}



    // Optional test
    public function test()
    {
        $this->cors();
        return $this->respond(['success'=>true,'message'=>'Test endpoint working']);
    }
}
