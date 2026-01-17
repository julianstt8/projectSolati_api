<?php

namespace App\DAOs;

use App\Database\Connection;
use App\Models\Task;
use PDO;

class TaskDAOImpl implements TaskDAO
{

    private $db;

    public function __construct()
    {
        $this->db = Connection::get();
    }

    /**
     * Obtiene todas las tareas.
     */
    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM tasks");
        return $stmt->fetchAll(PDO::FETCH_CLASS, Task::class);
    }

    /**
     * Obtiene una tarea por ID.
     */
    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Task::class);
        return $stmt->fetch();
    }

    /**
     * Crea una nueva tarea.
     */
    public function create(Task $task)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO tasks (title, description, status)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([
            $task->title,
            $task->description,
            $task->status
        ]);

        $task->id = $this->db->lastInsertId();
        return $task;
    }

    /**
     * Actualiza una tarea existente.
     */
    public function update(Task $task)
    {
        $stmt = $this->db->prepare(
            "UPDATE tasks SET title=?, description=?, status=? WHERE id=?"
        );

        $stmt->execute([
            $task->title,
            $task->description,
            $task->status,
            $task->id
        ]);

        return $task;
    }

    /**
     * Elimina una tarea por ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
