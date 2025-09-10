<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;

class Todo extends ResourceController {
    protected $modelName = 'App\Models\TodoModel';
    protected $format = 'json';

    public function index() {
        $status = $this->request->getGet('status');
        $data = $this->model->getFiltered($status);
        return $this->respond(['success' => true, 'data' => $data]);
    }

    public function create() {
        // Read JSON body instead of getPost()
        $input = json_decode($this->request->getBody(), true);

        if (empty($input['task']) || empty($input['start_date']) || empty($input['end_date']) || empty($input['status'])) {
            return $this->respond(['success' => false, 'message' => 'All fields are required'], 400);
        }

        try {
            $this->model->insert([
                'task'       => $input['task'],
                'start_date' => $input['start_date'],
                'end_date'   => $input['end_date'],
                'status'     => $input['status']
            ]);

            return $this->respond(['success' => true, 'message' => 'Task added']);
        } catch (\Exception $e) {
            return $this->respond(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update($id = null) {
        $input = json_decode($this->request->getBody(), true);

        if (empty($input['status'])) {
            return $this->respond(['success' => false, 'message' => 'Status is required'], 400);
        }

        $this->model->update($id, ['status' => $input['status']]);
        return $this->respond(['success' => true, 'message' => 'Status updated']);
    }

   public function statusSummary() {
    $db = \Config\Database::connect();
    $builder = $db->table('todo_list');
    $builder->select('status, COUNT(*) as count');
    $builder->groupBy('status');
    $data = $builder->get()->getResult();

    return $this->respond(['success' => true, 'data' => $data]);
}

}


