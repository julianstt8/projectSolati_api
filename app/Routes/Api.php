<?php

namespace App\Routes;

use App\Controllers\TaskController;

final class Api
{

    private static array $validApiKeys = [
        'pruebaSeguridad12345',
    ];

    /**
     * Se realizo de esta manera separando por recursos y colecciones
     * para facilitar la lectura y el mantenimiento del codigo
     */
    public static function handleRequest(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        self::checkApiKey();

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path   = self::getPath();

        $controller = new TaskController();

        if ($path === 'tasks') {
            self::handleTasksCollection($method, $controller);
            return;
        }

        if (preg_match('#^tasks/(\d+)$#', $path, $matches)) {
            self::handleTaskResource($method, (int)$matches[1], $controller);
            return;
        }

        self::notFound();
    }

    /**
     * Maneja las solicitudes para la colección de tareas
     */
    private static function handleTasksCollection(string $method, TaskController $controller): void
    {
        switch ($method) {
            case 'GET':
                $controller->index();
                return;

            case 'POST':
                self::validateJson();
                $controller->store();
                return;

            default:
                self::methodNotAllowed(['GET', 'POST']);
        }
    }

    /**
     * Maneja las solicitudes para un recurso de tarea específico
     */
    private static function handleTaskResource(
        string $method,
        int $id,
        TaskController $controller
    ): void {
        if ($id <= 0) {
            self::badRequest('ID de tarea inválido');
            return;
        }

        switch ($method) {
            case 'GET':
                $controller->show($id);
                return;

            case 'PUT':
                self::validateJson();
                $controller->update($id);
                return;

            case 'DELETE':
                $controller->destroy($id);
                return;

            default:
                self::methodNotAllowed(['GET', 'PUT', 'DELETE']);
        }
    }

    /**
     * Valida que el header X-API-KEY esté presente y sea válido
     */
    private static function checkApiKey(): void
    {
        $headers = getallheaders();
        $apiKey = $headers['X-API-KEY'] ?? '';

        if (!in_array($apiKey, self::$validApiKeys, true)) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized: API Key inválida o faltante']);
            exit;
        }
    }

    /**
     * Obtiene la ruta de la solicitud actual
     */
    private static function getPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = preg_replace('#^.*?/public/?#', '', $path);
        return trim($path ?? '', '/');
    }

    /**
     * Valida que el cuerpo de la solicitud sea JSON válido
     */
    private static function validateJson(): void
    {
        if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === false) {
            self::unsupportedMediaType();
        }
        json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            self::badRequest('JSON invalido');
        }
    }

    /**
     * Maneja respuestas de error comunes
     */
    private static function badRequest(string $message): void
    {
        http_response_code(400);
        echo json_encode(['error' => $message]);
        exit;
    }

    private static function notFound(): void
    {
        http_response_code(404);
        echo json_encode(['error' => 'No encontrado']);
        exit;
    }

    private static function methodNotAllowed(array $allowed): void
    {
        http_response_code(405);
        header('Allow: ' . implode(', ', $allowed));
        echo json_encode(['error' => 'Metodo no permitido']);
        exit;
    }

    private static function unsupportedMediaType(): void
    {
        http_response_code(415);
        echo json_encode(['error' => 'Contenido no soportado']);
        exit;
    }
}
