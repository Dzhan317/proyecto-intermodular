<?php
declare(strict_types=1);

/*
 * Lógica del sistema de verificación en dos pasos (2FA).
 * Genera el código, lo envía por email y lo valida.
 * Si el email falla, el código se elimina para no bloquear el cooldown.
 */

require_once APP_PATH . '/Models/UserModel.php';
require_once APP_PATH . '/Services/MailService.php';

class TwoFactorService
{
    private PDO         $db;
    private MailService $mailer;

    private const DEFAULT_COOLDOWN     = 60;
    private const DEFAULT_EXPIRY       = 10;
    private const DEFAULT_MAX_ATTEMPTS = 5;

    public function __construct()
    {
        $this->db     = Database::getInstance();
        $this->mailer = new MailService();
    }

    public function generateAndSend(int $userId, string $email, string $name): array
    {
        $cooldown = defined('TWO_FA_RESEND_COOLDOWN') ? TWO_FA_RESEND_COOLDOWN : self::DEFAULT_COOLDOWN;
        $expiry   = defined('TWO_FA_EXPIRY_MINUTES')  ? TWO_FA_EXPIRY_MINUTES  : self::DEFAULT_EXPIRY;

        $this->db->prepare('DELETE FROM two_factor_codes WHERE user_id = ? AND (expires_at < NOW() OR used_at IS NOT NULL)')->execute([$userId]);

        $stmt = $this->db->prepare('SELECT id, created_at FROM two_factor_codes WHERE user_id = ? AND used_at IS NULL AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1');
        $stmt->execute([$userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $secondsSince = time() - strtotime($existing['created_at']);
            if ($secondsSince < $cooldown) {
                $remaining = $cooldown - $secondsSince;
                return ['success' => false, 'error' => "Espera {$remaining} segundo(s) antes de solicitar otro código.", 'cooldown' => true, 'remaining' => $remaining];
            }
            $this->db->prepare('DELETE FROM two_factor_codes WHERE user_id = ? AND used_at IS NULL')->execute([$userId]);
        }

        $code      = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $codeHash  = hash('sha256', $code);
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiry} minutes"));
        $ip        = $_SERVER['REMOTE_ADDR'] ?? '';

        $this->db->prepare('INSERT INTO two_factor_codes (user_id, code_hash, expires_at, request_ip) VALUES (?, ?, ?, ?)')->execute([$userId, $codeHash, $expiresAt, $ip]);

        $sent = $this->sendCodeEmail($email, $name, $code);

        if (!$sent) {
            $this->db->prepare('DELETE FROM two_factor_codes WHERE user_id = ? AND code_hash = ?')->execute([$userId, $codeHash]);
            return ['success' => false, 'error' => 'No se pudo enviar el código. Inténtalo de nuevo.'];
        }

        return ['success' => true];
    }

    public function verify(int $userId, string $code): array
    {
        $maxAttempts = defined('TWO_FA_MAX_ATTEMPTS') ? TWO_FA_MAX_ATTEMPTS : self::DEFAULT_MAX_ATTEMPTS;
        $stmt = $this->db->prepare('SELECT * FROM two_factor_codes WHERE user_id = ? AND used_at IS NULL AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1');
        $stmt->execute([$userId]);
        $record = $stmt->fetch();
        if (!$record) return ['success' => false, 'error' => 'El código ha expirado. Solicita uno nuevo.'];
        if ($record['blocked_until'] && strtotime($record['blocked_until']) > time()) {
            $minutes = ceil((strtotime($record['blocked_until']) - time()) / 60);
            return ['success' => false, 'error' => "Demasiados intentos. Espera {$minutes} minuto(s).", 'blocked' => true];
        }
        if (!hash_equals($record['code_hash'], hash('sha256', $code))) {
            $attempts = (int) $record['failed_attempts'] + 1;
            if ($attempts >= $maxAttempts) {
                $blockedUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $this->db->prepare('UPDATE two_factor_codes SET failed_attempts = ?, blocked_until = ? WHERE id = ?')->execute([$attempts, $blockedUntil, $record['id']]);
                return ['success' => false, 'error' => 'Demasiados intentos fallidos. Espera 15 minutos.', 'blocked' => true];
            }
            $this->db->prepare('UPDATE two_factor_codes SET failed_attempts = ? WHERE id = ?')->execute([$attempts, $record['id']]);
            return ['success' => false, 'error' => "Código incorrecto. Te quedan " . ($maxAttempts - $attempts) . " intento(s)."];
        }
        $this->db->prepare('UPDATE two_factor_codes SET used_at = NOW() WHERE id = ?')->execute([$record['id']]);
        return ['success' => true];
    }

    private function sendCodeEmail(string $to, string $name, string $code): bool
    {
        return $this->mailer->send($to, $name, 'Tu código de verificación — PrimeLux SmartShop', $this->buildEmailTemplate($name, $code));
    }

    private function buildEmailTemplate(string $name, string $code): string
    {
        $minutes = defined('TWO_FA_EXPIRY_MINUTES') ? TWO_FA_EXPIRY_MINUTES : self::DEFAULT_EXPIRY;
        $appUrl  = defined('APP_URL') ? APP_URL : '';
        $digits  = implode('', array_map(fn($d) => "<span style='display:inline-block;width:40px;height:48px;line-height:48px;text-align:center;font-size:28px;font-weight:700;color:#FFFFFF;background:#1F2937;border-radius:8px;margin:0 4px;border:1px solid #374151;'>{$d}</span>", str_split($code)));
        return <<<HTML
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#0F172A;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr><td align="center" style="padding:40px 20px;">
      <table width="480" cellpadding="0" cellspacing="0" border="0" style="background:#1F2937;border-radius:16px;overflow:hidden;max-width:480px;">
        <tr><td style="background:#2563EB;padding:24px 32px;text-align:center;"><span style="color:#FFFFFF;font-size:20px;font-weight:700;">PrimeLux SmartShop</span></td></tr>
        <tr><td style="padding:32px;">
          <p style="color:#9CA3AF;font-size:15px;margin:0 0 6px;">Hola, <strong style="color:#FFFFFF;">{$name}</strong></p>
          <p style="color:#9CA3AF;font-size:15px;margin:0 0 28px;">Tu código de verificación es:</p>
          <div style="text-align:center;margin-bottom:28px;padding:16px 0;">{$digits}</div>
          <p style="color:#6B7280;font-size:13px;text-align:center;margin:0 0 24px;">Expira en <strong style="color:#F59E0B;">{$minutes} minutos</strong>.</p>
          <div style="background:#111827;border-radius:8px;padding:16px;border:1px solid #374151;"><p style="color:#6B7280;font-size:12px;margin:0;">Si no has iniciado sesión en PrimeLux SmartShop, ignora este mensaje.</p></div>
        </td></tr>
        <tr><td style="padding:16px 32px;text-align:center;border-top:1px solid #374151;"><p style="color:#4B5563;font-size:12px;margin:0;">© 2026 PrimeLux SmartShop &nbsp;·&nbsp; <a href="{$appUrl}" style="color:#2563EB;text-decoration:none;">primeluxshop.es</a></p></td></tr>
      </table>
    </td></tr>
  </table>
</body></html>
HTML;
    }
}
