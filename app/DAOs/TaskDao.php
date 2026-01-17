<?php

namespace App\DAOs;

use App\Models\Task;

interface TaskDAO
{
    public function findAll();
    public function findById(int $id);
    public function create(Task $task);
    public function update(Task $task);
    public function delete(int $id);
}
