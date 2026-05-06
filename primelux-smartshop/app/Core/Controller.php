<?php
declare(strict_types=1);

/**
 * Controller (Base)
 * All controllers extend this class.
 * Provides view rendering, redirects, JSON responses and auth guards.
 */
abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = APP_PATH . '/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$view}");
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

    /**
     * Valida el token CSRF del formulario.
     * Si la sesión expiró o el token no coincide, guarda un mensaje claro
     * y redirige de vuelta al formulario en lugar de mostrar una pantalla vacía.
     */
    protected function validateCsrf(): void
    {
        $token         = $_POST[CSRF_TOKEN_NAME] ?? '';
        $sessionToken  = $_SESSION[CSRF_TOKEN_NAME] ?? '';

        if (empty($sessionToken) || !hash_equals($sessionToken, $token)) {
            // Regenera el token para que el formulario vuelva a funcionar
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
            $_SESSION['csrf_error']    = 'Tu sesión ha expirado. Por favor, inténtalo de nuevo.';

            // Vuelve al formulario que hizo el POST
            $referer = $_SERVER['HTTP_REFERER'] ?? (APP_URL . '/login');
            $this->redirect($referer);
        }
    }
}
