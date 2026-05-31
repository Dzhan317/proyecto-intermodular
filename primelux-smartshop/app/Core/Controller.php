<?php
declare(strict_types=1);

/**
 * Controller (Base)
 * All controllers extend this class.
 * Provides view rendering, redirects, JSON responses and auth guards.
 *
 * Gestión de sesión por inactividad:
 *   - Usuario normal  → 7 días  (INACTIVITY_USER)
 *   - Administrador   → 2 horas (INACTIVITY_ADMIN)
 *
 * El 2FA no interfiere aquí — solo actúa durante el proceso de login.
 * Una vez completado el 2FA, verify2fa() inicializa last_activity en sesión.
 *
 * Páginas públicas (home, categorías, productos) no llaman a requireAuth()
 * por lo que last_activity no se actualiza en ellas — correcto, ya que
 * el tiempo de inactividad solo cuenta cuando el usuario está en áreas
 * autenticadas (perfil, carrito, checkout, admin).
 */
abstract class Controller
{
    // ─── Constantes ──────────────────────────────────────────────────────────

    // 7 días en segundos — usuarios normales
    private const INACTIVITY_USER  = 604800;

    // 2 horas en segundos — administradores
    private const INACTIVITY_ADMIN = 7200;

    // ─── Rendering ───────────────────────────────────────────────────────────

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

    // ─── Autenticación ────────────────────────────────────────────────────────

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Verifica que el usuario está autenticado.
     * Si no lo está → redirige al login.
     * Si lo está → comprueba inactividad y actualiza last_activity.
     *
     * Si last_activity no existe (sesión iniciada antes de esta versión
     * o primera petición autenticada), lo inicializa con el tiempo actual
     * en lugar de destruir la sesión.
     */
    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect(APP_URL . '/login');
        }

        // Inicializa last_activity si no existe — evita destruir sesiones
        // válidas que no tengan este campo (compatibilidad con sesiones antiguas)
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return;
        }

        $this->checkInactivity(self::INACTIVITY_USER);
    }

    /**
     * Verifica que el usuario es administrador.
     * Comprobaciones en orden:
     * 1. ¿Está logueado? Si no → login
     * 2. ¿Tiene rol admin? Si no → home
     * 3. ¿Ha superado la inactividad de admin (2h)? Si sí → logout
     *
     * No llama a requireAuth() para evitar ejecutar checkInactivity dos veces
     * con límites distintos — cada rol tiene su propio límite.
     */
    protected function requireAdmin(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect(APP_URL . '/login');
        }

        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            $this->redirect(APP_URL . '/');
        }

        // Inicializa last_activity si no existe
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return;
        }

        $this->checkInactivity(self::INACTIVITY_ADMIN);
    }

    // ─── CSRF ─────────────────────────────────────────────────────────────────

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
        $token        = $_POST[CSRF_TOKEN_NAME] ?? '';
        $sessionToken = $_SESSION[CSRF_TOKEN_NAME] ?? '';

        if (empty($sessionToken) || !hash_equals($sessionToken, $token)) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
            $_SESSION['csrf_error']    = 'Tu sesión ha expirado. Por favor, inténtalo de nuevo.';

            $referer = $_SERVER['HTTP_REFERER'] ?? (APP_URL . '/login');
            $this->redirect($referer);
        }
    }

    // ─── Helper privado: control de inactividad ───────────────────────────────

    /**
     * Comprueba si el usuario lleva más de $maxInactivity segundos sin
     * actividad en áreas autenticadas.
     *
     * Si ha superado el límite:
     *   1. Destruye la sesión completamente
     *   2. Inicia una sesión limpia para poder mostrar el aviso
     *   3. Redirige al login con el flag session_expired
     *
     * Si no ha superado el límite:
     *   - Actualiza last_activity con el timestamp actual
     *
     * @param int $maxInactivity Segundos máximos de inactividad permitidos
     */
    private function checkInactivity(int $maxInactivity): void
    {
        $now          = time();
        $lastActivity = (int) $_SESSION['last_activity'];

        if (($now - $lastActivity) > $maxInactivity) {
            // Limpieza completa de sesión
            session_unset();
            session_destroy();

            // Inicia nueva sesión respetando los parámetros del index.php
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => (APP_ENV === 'production'),
                'httponly' => true,
                'samesite' => 'Lax',    // Lax permite redirecciones GET externas (necesario para Stripe)
            ]);
            session_start();
            $_SESSION['session_expired'] = true;

            $this->redirect(APP_URL . '/login');
        }

        // Actualiza el timestamp en cada petición autenticada
        $_SESSION['last_activity'] = $now;
    }
}
