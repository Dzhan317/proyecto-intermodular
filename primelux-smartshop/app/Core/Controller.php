<?php
declare(strict_types=1);

/*
 * Clase base de la que heredan todos los controladores.
 * Proporciona métodos comunes: renderizar vistas, redirigir, responder JSON,
 * proteger rutas por rol y validar tokens CSRF.
 */

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = APP_PATH . '/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vista no encontrada: {$view}");
        }

        require_once $viewFile;
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect(APP_URL . '/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            $this->redirect(APP_URL . '/');
        }
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    protected function validateCsrf(): void
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $token)) {
            http_response_code(403);
            exit('Token CSRF inválido.');
        }
    }
}
