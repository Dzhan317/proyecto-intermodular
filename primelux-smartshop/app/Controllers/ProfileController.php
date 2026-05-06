<?php
declare(strict_types=1);

/*
 * Gestiona las páginas del perfil de usuario.
 * Datos personales (solo nombre y apellidos), cambio de contraseña.
 */

require_once APP_PATH . '/Models/UserModel.php';

class ProfileController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // GET /profile — datos personales
    public function index(array $params): void
    {
        $this->requireAuth();

        $user = $this->userModel->findById((int) $_SESSION['user_id']);

        $this->view('profile.index', [
            'pageTitle'  => 'Mi perfil — PrimeLux SmartShop',
            'user'       => $user,
            'activeTab'  => 'profile',
            'csrfToken'  => $this->csrfToken(),
            'success'    => $_SESSION['profile_success'] ?? '',
            'error'      => $_SESSION['profile_error']   ?? '',
        ]);

        unset($_SESSION['profile_success'], $_SESSION['profile_error']);
    }

    // POST /profile — actualiza nombre y apellidos
    public function update(array $params): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $name     = trim($_POST['name']      ?? '');
        $lastName = trim($_POST['last_name'] ?? '');

        if (empty($name) || empty($lastName)) {
            $_SESSION['profile_error'] = 'El nombre y los apellidos son obligatorios.';
            $this->redirect(APP_URL . '/profile');
        }

        if (strlen($name) > 100 || strlen($lastName) > 150) {
            $_SESSION['profile_error'] = 'Los datos introducidos son demasiado largos.';
            $this->redirect(APP_URL . '/profile');
        }

        $this->userModel->updateProfile(
            (int) $_SESSION['user_id'],
            $name,
            $lastName
        );

        $_SESSION['profile_success'] = 'Datos actualizados correctamente.';
        $this->redirect(APP_URL . '/profile');
    }

    // GET /profile/security — cambio de contraseña y sesión activa
    public function security(array $params): void
    {
        $this->requireAuth();

        $user = $this->userModel->findById((int) $_SESSION['user_id']);

        $this->view('profile.security', [
            'pageTitle'  => 'Seguridad — PrimeLux SmartShop',
            'user'       => $user,
            'activeTab'  => 'security',
            'csrfToken'  => $this->csrfToken(),
            'device'     => $this->detectDevice(),
            'success'    => $_SESSION['profile_success'] ?? '',
            'error'      => $_SESSION['profile_error']   ?? '',
        ]);

        unset($_SESSION['profile_success'], $_SESSION['profile_error']);
    }

    // POST /profile/password — actualiza la contraseña
    public function changePassword(array $params): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            $_SESSION['profile_error'] = 'Todos los campos son obligatorios.';
            $this->redirect(APP_URL . '/profile/security');
        }

        if ($new !== $confirm) {
            $_SESSION['profile_error'] = 'Las nuevas contraseñas no coinciden.';
            $this->redirect(APP_URL . '/profile/security');
        }

        $strengthError = $this->validatePasswordStrength($new);
        if ($strengthError) {
            $_SESSION['profile_error'] = $strengthError;
            $this->redirect(APP_URL . '/profile/security');
        }

        $user = $this->userModel->findById((int) $_SESSION['user_id']);

        if (!password_verify($current, $user['password'])) {
            $_SESSION['profile_error'] = 'La contraseña actual no es correcta.';
            $this->redirect(APP_URL . '/profile/security');
        }

        $this->userModel->updatePassword(
            (int) $_SESSION['user_id'],
            password_hash($new, PASSWORD_BCRYPT, ['cost' => 12])
        );

        $_SESSION['profile_success'] = 'Contraseña actualizada correctamente.';
        $this->redirect(APP_URL . '/profile/security');
    }

    // ─── Helpers privados ────────────────────────────────────────────────────

    private function detectDevice(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $os = match (true) {
            str_contains($ua, 'Windows')                            => 'Windows',
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Android')                            => 'Android',
            str_contains($ua, 'Mac')                                => 'Mac',
            str_contains($ua, 'Linux')                              => 'Linux',
            default                                                 => 'Dispositivo desconocido',
        };

        $browser = match (true) {
            str_contains($ua, 'Edg')                                     => 'Edge',
            str_contains($ua, 'Chrome') && !str_contains($ua, 'Edg')    => 'Chrome',
            str_contains($ua, 'Firefox')                                 => 'Firefox',
            str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome') => 'Safari',
            default                                                      => 'Navegador desconocido',
        };

        return $browser . ' · ' . $os;
    }

    private function validatePasswordStrength(string $password): string
    {
        if (strlen($password) < 10)
            return 'La contraseña debe tener al menos 10 caracteres.';
        if (strlen(preg_replace('/[^A-Z]/', '', $password)) < 2)
            return 'La contraseña debe contener al menos 2 mayúsculas.';
        if (strlen(preg_replace('/[^a-z]/', '', $password)) < 2)
            return 'La contraseña debe contener al menos 2 minúsculas.';
        if (strlen(preg_replace('/[^0-9]/', '', $password)) < 2)
            return 'La contraseña debe contener al menos 2 números.';
        if (!preg_match('/[^A-Za-z0-9]/', $password))
            return 'La contraseña debe contener al menos 1 carácter especial.';
        return '';
    }
}
