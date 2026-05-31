<?php
declare(strict_types=1);

/*
 * Gestiona todas las peticiones de autenticación.
 * Sin lógica de negocio: delega en AuthService y TwoFactorService.
 */

require_once APP_PATH . '/Models/UserModel.php';
require_once APP_PATH . '/Services/AuthService.php';
require_once APP_PATH . '/Services/TwoFactorService.php';

class AuthController extends Controller
{
    private AuthService      $authService;
    private TwoFactorService $twoFactorService;

    public function __construct()
    {
        $this->authService      = new AuthService();
        $this->twoFactorService = new TwoFactorService();
    }

    public function loginForm(array $params): void
    {
        if ($this->isLoggedIn()) {
            $redirectTo = ($_SESSION['user_role'] ?? 'customer') === 'admin' ? '/admin' : '/';
            $this->redirect(APP_URL . $redirectTo);
        }

        // Mensaje de confirmación tras restablecer contraseña
        $success = isset($_GET['reset']) ? 'Contraseña actualizada correctamente. Inicia sesión.' : '';

        // Mensaje de sesión expirada por inactividad
        $expired = '';
        if (!empty($_SESSION['session_expired'])) {
            $expired = 'Tu sesión ha expirado por inactividad. Por favor, inicia sesión de nuevo.';
            unset($_SESSION['session_expired']);
        }

        $this->view('auth.step-email', [
            'pageTitle' => 'Iniciar sesión | PrimeLux SmartShop',
            'csrfToken' => $this->csrfToken(),
            'email'     => $_SESSION['auth_email'] ?? '',
            'success'   => $success,
            'expired'   => $expired,
        ]);
    }

    public function checkEmail(array $params): void
    {
        $this->validateCsrf();

        $email = trim(strtolower($_POST['email'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('auth.step-email', [
                'pageTitle' => 'Iniciar sesión | PrimeLux SmartShop',
                'csrfToken' => $this->csrfToken(),
                'error'     => 'Introduce un correo electrónico válido.',
                'email'     => $email,
            ]);
            return;
        }

        $_SESSION['auth_email'] = $email;

        $userModel = new UserModel();
        if ($userModel->findByEmail($email)) {
            $this->redirect(APP_URL . '/login/password');
        } else {
            $this->redirect(APP_URL . '/register');
        }
    }

    public function passwordForm(array $params): void
    {
        if ($this->isLoggedIn()) {
            $redirectTo = ($_SESSION['user_role'] ?? 'customer') === 'admin' ? '/admin' : '/';
            $this->redirect(APP_URL . $redirectTo);
        }
        if (empty($_SESSION['auth_email'])) $this->redirect(APP_URL . '/login');

        $this->view('auth.login', [
            'pageTitle' => 'Introduce tu contraseña | PrimeLux SmartShop',
            'csrfToken' => $this->csrfToken(),
            'email'     => $_SESSION['auth_email'],
        ]);
    }

    public function login(array $params): void
    {
        $this->validateCsrf();

        $email    = trim(strtolower($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $ip       = $_SERVER['REMOTE_ADDR'] ?? '';

        $result = $this->authService->login($email, $password, $ip);

        if (!$result['success']) {
            $this->view('auth.login', [
                'pageTitle' => 'Introduce tu contraseña | PrimeLux SmartShop',
                'csrfToken' => $this->csrfToken(),
                'email'     => $email,
                'error'     => $result['error'],
            ]);
            return;
        }

        $user = $result['user'];

        unset($_SESSION['auth_email']);
        $_SESSION['pre_auth_user_id']    = $user['id'];
        $_SESSION['pre_auth_user_email'] = $user['email'];
        $_SESSION['pre_auth_user_name']  = $user['name'];

        $sent = $this->twoFactorService->generateAndSend(
            $user['id'],
            $user['email'],
            $user['name']
        );

        if (!$sent['success']) {
            $_SESSION['twofa_error'] = $sent['error'];
        }

        $this->redirect(APP_URL . '/verify-2fa');
    }

    public function verify2faForm(array $params): void
    {
        if (empty($_SESSION['pre_auth_user_id'])) {
            $this->redirect(APP_URL . '/login');
        }

        $this->view('auth.verify-2fa', [
            'pageTitle'   => 'Verificación en dos pasos | PrimeLux SmartShop',
            'csrfToken'   => $this->csrfToken(),
            'maskedEmail' => $this->maskEmail($_SESSION['pre_auth_user_email'] ?? ''),
            'error'       => $_SESSION['twofa_error'] ?? '',
        ]);

        unset($_SESSION['twofa_error']);
    }

    public function verify2fa(array $params): void
    {
        $this->validateCsrf();

        if (empty($_SESSION['pre_auth_user_id'])) {
            $this->redirect(APP_URL . '/login');
        }

        $userId = (int) $_SESSION['pre_auth_user_id'];
        $code   = preg_replace('/\D/', '', trim($_POST['code'] ?? ''));

        $result = $this->twoFactorService->verify($userId, $code);

        if (!$result['success']) {
            $_SESSION['twofa_error'] = $result['error'];
            $this->redirect(APP_URL . '/verify-2fa');
        }

        // Carga el usuario completo para guardar todos los datos en sesión
        $user = (new UserModel())->findById($userId);

        $_SESSION['user_id']        = $userId;
        $_SESSION['user_role']      = $user['role']      ?? 'customer';
        $_SESSION['user_name']      = $user['name']      ?? '';
        $_SESSION['user_last_name'] = $user['last_name'] ?? '';
        $_SESSION['last_activity']  = time();

        unset(
            $_SESSION['pre_auth_user_id'],
            $_SESSION['pre_auth_user_email'],
            $_SESSION['pre_auth_user_name']
        );

        // Redirige al panel si es admin, a la tienda si es cliente
        $redirectTo = ($user['role'] ?? 'customer') === 'admin' ? '/admin' : '/';
        $this->redirect(APP_URL . $redirectTo);
    }

    public function resend2fa(array $params): void
    {
        if (empty($_SESSION['pre_auth_user_id'])) {
            $this->redirect(APP_URL . '/login');
        }

        $sent = $this->twoFactorService->generateAndSend(
            (int) $_SESSION['pre_auth_user_id'],
            $_SESSION['pre_auth_user_email'] ?? '',
            $_SESSION['pre_auth_user_name']  ?? ''
        );

        if (!$sent['success']) {
            $_SESSION['twofa_error'] = $sent['error'];
        } else {
            $_SESSION['twofa_success'] = 'Se ha enviado un nuevo código a tu correo.';
        }

        $this->redirect(APP_URL . '/verify-2fa');
    }

    public function registerForm(array $params): void
    {
        if ($this->isLoggedIn()) {
            $redirectTo = ($_SESSION['user_role'] ?? 'customer') === 'admin' ? '/admin' : '/';
            $this->redirect(APP_URL . $redirectTo);
        }

        $this->view('auth.register', [
            'pageTitle' => 'Crear cuenta | PrimeLux SmartShop',
            'csrfToken' => $this->csrfToken(),
            'email'     => $_SESSION['auth_email'] ?? '',
        ]);
    }

    public function register(array $params): void
    {
        $this->validateCsrf();

        $data = [
            'email'            => trim(strtolower($_POST['email'] ?? '')),
            'name'             => trim($_POST['name'] ?? ''),
            'last_name'        => trim($_POST['last_name'] ?? ''),
            'password'         => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'terms'            => isset($_POST['terms']),
        ];

        $result = $this->authService->register($data);

        if (!$result['success']) {
            $this->view('auth.register', [
                'pageTitle' => 'Crear cuenta | PrimeLux SmartShop',
                'csrfToken' => $this->csrfToken(),
                'email'     => $data['email'],
                'name'      => $data['name'],
                'lastName'  => $data['last_name'],
                'error'     => $result['error'],
            ]);
            return;
        }

        unset($_SESSION['auth_email']);
        $_SESSION['pre_auth_user_id']    = $result['user_id'];
        $_SESSION['pre_auth_user_email'] = $data['email'];
        $_SESSION['pre_auth_user_name']  = $data['name'];

        $sent = $this->twoFactorService->generateAndSend(
            $result['user_id'],
            $data['email'],
            $data['name']
        );

        if (!$sent['success']) {
            $_SESSION['twofa_error'] = $sent['error'];
        }

        $this->redirect(APP_URL . '/verify-2fa');
    }

    public function logout(array $params): void
    {
        session_destroy();
        $this->redirect(APP_URL . '/login');
    }

    public function forgotPasswordForm(array $params): void
    {
        $this->view('auth.forgot-password', [
            'pageTitle' => 'Recuperar contraseña | PrimeLux SmartShop',
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function forgotPassword(array $params): void
    {
        $this->validateCsrf();

        $email = trim(strtolower($_POST['email'] ?? ''));
        $this->authService->sendPasswordReset($email);

        $this->view('auth.forgot-password', [
            'pageTitle' => 'Recuperar contraseña | PrimeLux SmartShop',
            'csrfToken' => $this->csrfToken(),
            'success'   => 'Si el correo está registrado, recibirás un enlace en breve.',
            'email'     => '',
        ]);
    }

    public function resetPasswordForm(array $params): void
    {
        $token   = $params['token'] ?? '';
        // Valida el token antes de renderizar — si expiró, la vista muestra el aviso
        $expired = !$this->authService->isValidResetToken($token);

        $this->view('auth.reset-password', [
            'pageTitle' => 'Restablecer contraseña | PrimeLux SmartShop',
            'csrfToken' => $this->csrfToken(),
            'token'     => $token,
            'expired'   => $expired,
        ]);
    }

    public function resetPassword(array $params): void
    {
        $this->validateCsrf();

        $token  = $params['token'] ?? '';
        $result = $this->authService->resetPassword(
            $token,
            $_POST['password'] ?? '',
            $_POST['password_confirm'] ?? ''
        );

        if (!$result['success']) {
            $this->view('auth.reset-password', [
                'pageTitle' => 'Restablecer contraseña | PrimeLux SmartShop',
                'csrfToken' => $this->csrfToken(),
                'token'     => $token,
                'error'     => $result['error'],
            ]);
            return;
        }

        $this->redirect(APP_URL . '/login?reset=1');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) return $email;
        [$local, $domain] = explode('@', $email, 2);
        $visible = substr($local, 0, min(2, strlen($local)));
        return $visible . str_repeat('*', max(0, strlen($local) - 2)) . '@' . $domain;
    }
}
