<?php

namespace App\Services;

use App\DAOs\TaskDAOImpl;
use App\Models\Task;

class TaskService
{
    private $dao;

    public function __construct()
    {
        $this->dao = new TaskDAOImpl();
    }

    public function getAll()
    {
        return $this->dao->findAll();
    }

    public function getById(int $id)
    {
        return $this->dao->findById($id);
    }

    public function create(array $data)
    {
        $task = new Task();
        $task->title = $data['title'];
        $task->description = $data['description'] ?? null;
        $task->status = $data['status'] ?? 0;

        return $this->dao->create($task);
    }

    public function update(int $id, array $data)
    {
        $task = $this->dao->findById($id);
        if (!$task) return null;

        $task->title = $data['title'];
        $task->description = $data['description'] ?? null;
        $task->status = $data['status'] ?? 0;
        return $this->dao->update($task);
    }

    public function delete(int $id): bool
    {
        return $this->dao->delete($id);
    }
}
