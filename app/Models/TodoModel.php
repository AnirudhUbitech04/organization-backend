<?php

namespace App\Models;
use CodeIgniter\Model;

class TodoModel extends Model {
    protected $table = 'todo_list';
    protected $primaryKey = 'id';
    protected $allowedFields = ['task', 'start_date', 'end_date', 'status'];

    public function getFiltered($status = null) {
        if ($status) {
            return $this->where('status', $status)->findAll();
        }
        return $this->findAll();
    }
}
