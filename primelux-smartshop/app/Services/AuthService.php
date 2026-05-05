<?php
declare(strict_types=1);

/*
 * Lógica de negocio de autenticación.
 * Centraliza: validación, hashing, rate limiting y envío de email.
 * Usa MailService para todos los envíos — nunca mail() nativo.
 */

require_once APP_PATH . '/Models/UserModel.php';
require_once APP_PATH . '/Services/MailService.php';

class AuthService
{
    private UserModel   $userModel;
    private PDO         $db;
    private MailService $mailer;

    private const MAX_ATTEMPTS  = 5;
    private const BLOCK_MINUTES = 15;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->db        = Database::getInstance();
        $this->mailer    = new MailService();
    }

    public function login(string $email, string $password, string $ip): array
    {
        if ($this->isBlocked($email, $ip)) {
            return ['success' => false, 'error' => 'Demasiados intentos fallidos. Espera unos minutos e inténtalo de nuevo.'];
        }
        $user = $this->userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $this->recordAttempt($email, $ip, false);
            return ['success' => false, 'error' => 'Credenciales incorrectas. Comprueba tu correo y contraseña.'];
        }
        if ($user['status'] !== 'active') {
            return ['success' => false, 'error' => 'Tu cuenta está inactiva. Contacta con soporte.'];
        }
        $this->recordAttempt($email, $ip, true);
        return ['success' => true, 'user' => $user];
    }

    public function register(array $data): array
    {
        $error = $this->validateRegistration($data);
        if ($error) return ['success' => false, 'error' => $error];
        if ($this->userModel->emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Hay un problema con los datos introducidos. Revísalos e inténtalo de nuevo.'];
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
        if (!$user) return;
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->db->prepare('INSERT INTO password_resets (email, token_hash, expires_at) VALUES (?, ?, ?)')->execute([$email, $tokenHash, $expiresAt]);
        $this->mailer->send($email, $user['name'], 'Restablecer contraseña — PrimeLux SmartShop', $this->buildResetEmailTemplate($user['name'], APP_URL . '/reset-password/' . $token));
    }

    public function resetPassword(string $token, string $password, string $confirm): array
    {
        if ($password !== $confirm) return ['success' => false, 'error' => 'Las contraseñas no coinciden.'];
        $error = $this->validatePasswordStrength($password);
        if ($error) return ['success' => false, 'error' => $error];
        $tokenHash = hash('sha256', $token);
        $stmt = $this->db->prepare('SELECT * FROM password_resets WHERE token_hash = ? AND used = 0 AND expires_at > NOW() LIMIT 1');
        $stmt->execute([$tokenHash]);
        $reset = $stmt->fetch();
        if (!$reset) return ['success' => false, 'error' => 'El enlace no es válido o ha expirado.'];
        $user = $this->userModel->findByEmail($reset['email']);
        if (!$user) return ['success' => false, 'error' => 'No se pudo procesar la solicitud.'];
        $this->userModel->updatePassword($user['id'], password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]));
        $this->db->prepare('UPDATE password_resets SET used = 1 WHERE token_hash = ?')->execute([$tokenHash]);
        return ['success' => true];
    }

    private function buildResetEmailTemplate(string $name, string $resetUrl): string
    {
        $appUrl = APP_URL;
        return <<<HTML
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#0F172A;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr><td align="center" style="padding:40px 20px;">
      <table width="480" cellpadding="0" cellspacing="0" border="0" style="background:#1F2937;border-radius:16px;overflow:hidden;max-width:480px;">
        <tr><td style="background:#2563EB;padding:24px 32px;text-align:center;"><span style="color:#FFFFFF;font-size:20px;font-weight:700;">PrimeLux SmartShop</span></td></tr>
        <tr><td style="padding:32px;">
          <p style="color:#9CA3AF;font-size:15px;margin:0 0 6px;">Hola, <strong style="color:#FFFFFF;">{$name}</strong></p>
          <p style="color:#9CA3AF;font-size:15px;margin:0 0 24px;">Recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>
          <div style="text-align:center;margin-bottom:24px;"><a href="{$resetUrl}" style="display:inline-block;background:#2563EB;color:#FFFFFF;text-decoration:none;padding:12px 32px;border-radius:8px;font-weight:600;font-size:15px;">Restablecer contraseña</a></div>
          <p style="color:#6B7280;font-size:13px;text-align:center;margin:0 0 24px;">El enlace expira en <strong style="color:#F59E0B;">1 hora</strong>.</p>
          <div style="background:#111827;border-radius:8px;padding:16px;border:1px solid #374151;"><p style="color:#6B7280;font-size:12px;margin:0;">Si no solicitaste este cambio, ignora este mensaje.</p></div>
        </td></tr>
        <tr><td style="padding:16px 32px;text-align:center;border-top:1px solid #374151;"><p style="color:#4B5563;font-size:12px;margin:0;">© 2026 PrimeLux SmartShop &nbsp;·&nbsp; <a href="{$appUrl}" style="color:#2563EB;text-decoration:none;">primeluxshop.es</a></p></td></tr>
      </table>
    </td></tr>
  </table>
</body></html>
HTML;
    }

    private function isBlocked(string $email, string $ip): bool
    {
        $since = date('Y-m-d H:i:s', strtotime('-' . self::BLOCK_MINUTES . ' minutes'));
        $stmt  = $this->db->prepare('SELECT COUNT(*) FROM login_attempts WHERE (email = ? OR ip_address = ?) AND success = 0 AND created_at >= ?');
        $stmt->execute([$email, $ip, $since]);
        return (int) $stmt->fetchColumn() >= self::MAX_ATTEMPTS;
    }

    private function recordAttempt(string $email, string $ip, bool $success): void
    {
        $this->db->prepare('INSERT INTO login_attempts (email, ip_address, success) VALUES (?, ?, ?)')->execute([$email, $ip, (int) $success]);
    }

    private function validateRegistration(array $data): string
    {
        if (empty($data['name']))      return 'El nombre es obligatorio.';
        if (empty($data['last_name'])) return 'Los apellidos son obligatorios.';
        if (!$data['terms'])           return 'Debes aceptar los términos de uso.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) return 'El correo electrónico no es válido.';
        if ($data['password'] !== $data['password_confirm']) return 'Las contraseñas no coinciden.';
        return $this->validatePasswordStrength($data['password']);
    }

    private function validatePasswordStrength(string $password): string
    {
        if (strlen($password) < 10) return 'La contraseña debe tener al menos 10 caracteres.';
        if (strlen(preg_replace('/[^A-Z]/', '', $password)) < 2) return 'La contraseña debe contener al menos 2 mayúsculas.';
        if (strlen(preg_replace('/[^a-z]/', '', $password)) < 2) return 'La contraseña debe contener al menos 2 minúsculas.';
        if (strlen(preg_replace('/[^0-9]/', '', $password)) < 2) return 'La contraseña debe contener al menos 2 números.';
        if (!preg_match('/[^A-Za-z0-9]/', $password)) return 'La contraseña debe contener al menos 1 carácter especial.';
        return '';
    }
}
