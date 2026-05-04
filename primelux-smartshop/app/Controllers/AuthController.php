<?php
declare(strict_types=1);

/*
 * Gestiona todas las peticiones de autenticación.
 * Sin lógica de negocio: delega en AuthService y UserModel.
 */

require_once APP_PATH . '/Models/UserModel.php';
require_once APP_PATH . '/Services/AuthService.php';

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function loginForm(array $params): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect(APP_URL . '/');
        }

        $this->view('auth.step-email', [
            'csrfToken' => $this->csrfToken(),
            'email'     => $_SESSION['auth_email'] ?? '',
        ]);
    }

    public function checkEmail(array $params): void
    {
        $this->validateCsrf();

        $email = trim(strtolower($_POST['email'] ?? ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('auth.step-email', [
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
            $this->redirect(APP_URL . '/');
        }

        if (empty($_SESSION['auth_email'])) {
            $this->redirect(APP_URL . '/login');
        }

        $this->view('auth.login', [
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
                'csrfToken' => $this->csrfToken(),
                'email'     => $email,
                'error'     => $result['error'],
            ]);
            return;
        }

        unset($_SESSION['auth_email']);
        $_SESSION['pre_auth_user_id'] = $result['user']['id'];

        $this->redirect(APP_URL . '/verify-2fa');
    }

    public function registerForm(array $params): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect(APP_URL . '/');
        }

        $this->view('auth.register', [
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
                'csrfToken' => $this->csrfToken(),
                'email'     => $data['email'],
                'name'      => $data['name'],
                'lastName'  => $data['last_name'],
                'error'     => $result['error'],
            ]);
            return;
        }

        unset($_SESSION['auth_email']);
        $_SESSION['pre_auth_user_id'] = $result['user_id'];
        $this->redirect(APP_URL . '/verify-2fa');
    }

    public function logout(array $params): void
    {
        session_destroy();
        $this->redirect(APP_URL . '/login');
    }

    public function verify2faForm(array $params): void
    {
        if (empty($_SESSION['pre_auth_user_id'])) {
            $this->redirect(APP_URL . '/login');
        }

        $this->view('auth.verify-2fa', [
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function verify2fa(array $params): void
    {
        $this->redirect(APP_URL . '/verify-2fa');
    }

    public function forgotPasswordForm(array $params): void
    {
        $this->view('auth.forgot-password', [
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function forgotPassword(array $params): void
    {
        $this->validateCsrf();

        $email = trim(strtolower($_POST['email'] ?? ''));

        $this->authService->sendPasswordReset($email);

        $this->view('auth.forgot-password', [
            'csrfToken' => $this->csrfToken(),
            'success'   => 'Si el correo está registrado, recibirás un enlace en breve.',
            'email'     => '',
        ]);
    }

    public function resetPasswordForm(array $params): void
    {
        $token = $params['token'] ?? '';

        $this->view('auth.reset-password', [
            'csrfToken' => $this->csrfToken(),
            'token'     => $token,
        ]);
    }

    public function resetPassword(array $params): void
    {
        $this->validateCsrf();

        $token    = $params['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $result = $this->authService->resetPassword($token, $password, $confirm);

        if (!$result['success']) {
            $this->view('auth.reset-password', [
                'csrfToken' => $this->csrfToken(),
                'token'     => $token,
                'error'     => $result['error'],
            ]);
            return;
        }

        $this->redirect(APP_URL . '/login?reset=1');
    }
}
