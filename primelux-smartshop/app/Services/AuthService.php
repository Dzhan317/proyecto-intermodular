<?php
declare(strict_types=1);

/*
 * Lógica de negocio de autenticación.
 * Centraliza: validación, hashing, rate limiting y envío de email.
 */

require_once APP_PATH . '/Models/UserModel.php';

class AuthService
{
    private UserModel $userModel;
    private PDO       $db;

    private const MAX_ATTEMPTS  = 5;
    private const BLOCK_MINUTES = 15;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->db        = Database::getInstance();
    }

    public function login(string $email, string $password, string $ip): array
    {
        if ($this->isBlocked($email, $ip)) {
            return [
                'success' => false,
                'error'   => 'Demasiados intentos fallidos. Espera unos minutos e inténtalo de nuevo.',
            ];
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->recordAttempt($email, $ip, false);
            return [
                'success' => false,
                'error'   => 'Credenciales incorrectas. Comprueba tu correo y contraseña.',
            ];
        }

        if ($user['status'] !== 'active') {
            return [
                'success' => false,
                'error'   => 'Tu cuenta está inactiva. Contacta con soporte.',
            ];
        }

        $this->recordAttempt($email, $ip, true);
        return ['success' => true, 'user' => $user];
    }

    public function register(array $data): array
    {
        $error = $this->validateRegistration($data);
        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        if ($this->userModel->emailExists($data['email'])) {
            return [
                'success' => false,
                'error'   => 'Hay un problema con los datos introducidos. Revísalos e inténtalo de nuevo.',
            ];
        }

        $userId = $this->userModel->create([
            'name'      => $data['name'],
            'last_name' => $data['last_name'],
            'email'     => $data['email'],
            'password'  => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
        ]);

        return ['success' => true, 'user_id' => $userId];
    }

    public function sendPasswordReset(string $email): void
    {
        $this->db->prepare('DELETE FROM password_resets WHERE expires_at < NOW()')->execute();

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return;
        }

        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->db->prepare('
            INSERT INTO password_resets (email, token_hash, expires_at)
            VALUES (?, ?, ?)
        ')->execute([$email, $tokenHash, $expiresAt]);

        $resetUrl = APP_URL . '/reset-password/' . $token;
        $this->sendEmail(
            $email,
            'Restablecer contraseña — PrimeLux SmartShop',
            "Hola {$user['name']},\n\nHaz clic en el enlace para restablecer tu contraseña:\n{$resetUrl}\n\nExpira en 1 hora. Si no lo solicitaste, ignora este mensaje."
        );
    }

    public function resetPassword(string $token, string $password, string $confirm): array
    {
        if ($password !== $confirm) {
            return ['success' => false, 'error' => 'Las contraseñas no coinciden.'];
        }

        $error = $this->validatePasswordStrength($password);
        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        $tokenHash = hash('sha256', $token);
        $stmt = $this->db->prepare('
            SELECT * FROM password_resets
            WHERE token_hash = ? AND used = 0 AND expires_at > NOW()
            LIMIT 1
        ');
        $stmt->execute([$tokenHash]);
        $reset = $stmt->fetch();

        if (!$reset) {
            return ['success' => false, 'error' => 'El enlace no es válido o ha expirado.'];
        }

        $user = $this->userModel->findByEmail($reset['email']);
        if (!$user) {
            return ['success' => false, 'error' => 'No se pudo procesar la solicitud.'];
        }

        $this->userModel->updatePassword(
            $user['id'],
            password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])
        );

        $this->db->prepare('UPDATE password_resets SET used = 1 WHERE token_hash = ?')
                 ->execute([$tokenHash]);

        return ['success' => true];
    }

    private function isBlocked(string $email, string $ip): bool
    {
        $since = date('Y-m-d H:i:s', strtotime('-' . self::BLOCK_MINUTES . ' minutes'));
        $stmt  = $this->db->prepare('
            SELECT COUNT(*) FROM login_attempts
            WHERE (email = ? OR ip_address = ?)
              AND success = 0
              AND created_at >= ?
        ');
        $stmt->execute([$email, $ip, $since]);
        return (int) $stmt->fetchColumn() >= self::MAX_ATTEMPTS;
    }

    private function recordAttempt(string $email, string $ip, bool $success): void
    {
        $this->db->prepare('
            INSERT INTO login_attempts (email, ip_address, success) VALUES (?, ?, ?)
        ')->execute([$email, $ip, (int) $success]);
    }

    private function validateRegistration(array $data): string
    {
        if (empty($data['name']))      return 'El nombre es obligatorio.';
        if (empty($data['last_name'])) return 'Los apellidos son obligatorios.';
        if (!$data['terms'])           return 'Debes aceptar los términos de uso.';

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'El correo electrónico no es válido.';
        }

        if ($data['password'] !== $data['password_confirm']) {
            return 'Las contraseñas no coinciden.';
        }

        return $this->validatePasswordStrength($data['password']);
    }

    private function validatePasswordStrength(string $password): string
    {
        if (strlen($password) < 10) {
            return 'La contraseña debe tener al menos 10 caracteres.';
        }
        if (strlen(preg_replace('/[^A-Z]/', '', $password)) < 2) {
            return 'La contraseña debe contener al menos 2 mayúsculas.';
        }
        if (strlen(preg_replace('/[^a-z]/', '', $password)) < 2) {
            return 'La contraseña debe contener al menos 2 minúsculas.';
        }
        if (strlen(preg_replace('/[^0-9]/', '', $password)) < 2) {
            return 'La contraseña debe contener al menos 2 números.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return 'La contraseña debe contener al menos 1 carácter especial.';
        }
        return '';
    }

    private function sendEmail(string $to, string $subject, string $body): void
    {
        $headers  = "From: " . MAIL_NOREPLY_NAME . " <" . MAIL_NOREPLY_ADDRESS . ">\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        mail($to, $subject, $body, $headers);
    }
}
