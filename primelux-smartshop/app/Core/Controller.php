<?php
declare(strict_types=1);

/**
 * Controller (Base)
 * All controllers extend this class.
 * Provides view rendering, redirects, JSON responses and auth guards.
 */
abstract class Controller
{
    /**
     * Render a view file.
     * Dot notation maps to subdirectory: 'auth.login' -> Views/auth/login.php
     */
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

    /** Redirect to login if not authenticated. */
    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect(APP_URL . '/login');
        }
    }

    /** Redirect to home if not admin. */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            $this->redirect(APP_URL . '/');
        }
    }

    /**
     * Generate and store a CSRF token in the session.
     * Use in views: <input type="hidden" name="csrf_token" value="<?= $this->csrfToken() ?>">
     */
    protected function csrfToken(): string
    {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /** Validate the CSRF token from a POST request. Exits on failure. */
    protected function validateCsrf(): void
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $token)) {
            http_response_code(403);
            exit('Invalid CSRF token.');
        }
    }
}
