# Gestión de sesión — Fase 7

## Problema

La configuración inicial usaba `SESSION_LIFETIME = 3600` (1 hora) para todos
los usuarios. Esto era insuficiente para usuarios normales que visitan la
tienda esporádicamente y demasiado permisivo para el panel de administración.

---

## Solución: sesión por inactividad

En lugar de un tiempo fijo desde el login, se implementa un control de
inactividad basado en `$_SESSION['last_activity']`. En cada petición
autenticada se comprueba el tiempo transcurrido desde la última actividad.

```
Última actividad hace 3 horas — usuario normal (límite 7 días) → sesión válida
Última actividad hace 3 horas — administrador (límite 2 horas) → sesión expirada
```

Este es el estándar de plataformas como Amazon (30 días), Shopify (1 año),
PrestaShop (1 hora) y Magento (1 hora).

---

## Valores implementados

| Rol | Límite de inactividad | Referencia |
|---|---|---|
| Usuario normal | 7 días | Amazon, Zara, El Corte Inglés |
| Administrador | 2 horas | PrestaShop, Magento |

---

## Arquitectura

El control se implementa en `Controller::checkInactivity()`:

```php
private function checkInactivity(int $maxInactivity): void
{
    $now          = time();
    $lastActivity = $_SESSION['last_activity'] ?? $now;

    if (($now - $lastActivity) > $maxInactivity) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['session_expired'] = true;
        $this->redirect(APP_URL . '/login');
    }

    $_SESSION['last_activity'] = $now;
}
```

- `requireAuth()` llama a `checkInactivity(7 días)` para usuarios normales
- `requireAdmin()` llama a `checkInactivity(2 horas)` para administradores

---

## Cookie de sesión

`SESSION_LIFETIME` se establece en 604800 (7 días) — coincide con el límite
máximo de inactividad. Actúa como techo absoluto de la cookie del navegador.
El control real lo hace siempre `checkInactivity()`.

---

## Relación con el 2FA

El 2FA interviene únicamente durante el proceso de login. Una vez completada
la verificación, `verify2fa()` guarda en sesión:

```php
$_SESSION['user_id']        = $userId;
$_SESSION['user_role']      = $user['role'];
$_SESSION['user_name']      = $user['name'];
$_SESSION['user_last_name'] = $user['last_name'];
$_SESSION['last_activity']  = time();
```

El `last_activity` se inicializa en el momento del login para que el contador
de inactividad empiece a contar desde ese instante.

---

## Mensaje de sesión expirada

Cuando una sesión expira por inactividad, `$_SESSION['session_expired'] = true`
se guarda en la nueva sesión limpia. `AuthController::loginForm()` lo detecta
y muestra un mensaje informativo al usuario antes de limpiar el flag.
