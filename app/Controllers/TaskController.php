<?php

namespace App\Controllers;

use App\Services\TaskService;
use Throwable;

final class TaskController
{
    private TaskService $service;

    public function __construct()
    {
        $this->service = new TaskService();
    }

    /**
     * Lista todas las tareas.
     */
    public function index(): void
    {
        try {
            http_response_code(200);
            echo json_encode($this->service->getAll());
        } catch (Throwable $e) {
            $this->serverError();
        }
    }

    /**
     * Muestra una tarea por ID.
     */
    public function show(int $id): void
    {
        try {
            $task = $this->service->getById($id);

            if ($task === null) {
                $this->notFound();
                return;
            }

            http_response_code(200);
            echo json_encode($task);
        } catch (Throwable $e) {
            $this->serverError();
        }
    }

    /**
     * Crea una nueva tarea.
     */
    public function store(): void
    {
        try {
            $data = $this->getJsonBody();

            $error = $this->validateTask($data);
            if ($error !== null) {
                $this->badRequest($error);
                return;
            }

            $task = $this->service->create([
                'title'       => trim($data['title']),
                'description' => $data['description'] ?? null,
                'status'      => isset($data['status']) ? (int)$data['status'] : 0
            ]);

            http_response_code(201);
            echo json_encode($task);
        } catch (Throwable $e) {
            $this->serverError();
        }
    }

    /**
     * Actualiza una tarea existente.
     */
    public function update(int $id): void
    {
        try {
            $data = $this->getJsonBody();

            $error = $this->validateTask($data);
            if ($error !== null) {
                $this->badRequest($error);
                return;
            }

            $task = $this->service->update($id, [
                'title'       => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'status'      => isset($data['status']) ? (int)$data['status'] : 0
            ]);

            if ($task === null) {
                $this->notFound();
                return;
            }

            http_response_code(200);
            echo json_encode($task);
        } catch (Throwable $e) {
            $this->serverError();
        }
    }

    /**
     * Elimina una tarea por ID.
     */
    public function destroy(int $id): void
    {
        $deleted = $this->service->delete($id);
        if (!$deleted) {
            $this->notFound();
            return;
        }
        http_response_code(200);
        echo json_encode([
            'message' => 'Tarea eliminada correctamente',
            'id' => $id
        ]);
    }

    /**
     * Obtiene y decodifica el cuerpo JSON de la solicitud.
     */
    private function getJsonBody(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data)) {
            $this->badRequest('JSON invalido');
            exit;
        }

        return $data;
    }

    /**
     * Valida el campo "title" en los datos proporcionados.
     */
    private function validateTask(array $data): ?string
    {
        if (
            !isset($data['title']) || trim($data['title']) === '' ||
            !isset($data['description']) || trim($data['description']) === '' ||
            !isset($data['status']) || !is_bool($data['status'])
        ) {
            return 'Todos los campos son obligatorios y deben ser válidos';
        }

        return null;
    }

    /**
     * Manejan las respuestas de error.
     */
    private function badRequest(string $message): void
    {
        http_response_code(400);
        echo json_encode(['error' => $message]);
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo json_encode(['error' => 'Tarea no encontrada']);
    }

    private function serverError(): void
    {
        http_response_code(500);
        echo json_encode(['error' => 'Error interno del servidor']);
    }
}
