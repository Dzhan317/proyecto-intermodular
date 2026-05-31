# Arquitectura de seguridad

## Visión general

PrimeLux SmartShop implementa múltiples capas de seguridad que cubren autenticación, autorización, protección de formularios, gestión de sesión y comunicaciones cifradas.

---

## Autenticación

### Hashing de contraseñas — bcrypt
Las contraseñas se almacenan en la BD usando `password_hash()` con el algoritmo bcrypt y coste 12. Nunca se almacena la contraseña en texto plano.

```php
password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
```

El coste 12 es el estándar recomendado — suficientemente lento para dificultar ataques de fuerza bruta offline sin impactar la experiencia del usuario.

### Verificación en dos pasos (2FA)
Tras introducir email y contraseña correctamente, el sistema genera un código numérico de 6 dígitos con una validez de 10 minutos y lo envía al correo del usuario mediante `TwoFactorService`.

El código se almacena hasheado en `two_factor_codes` — nunca en texto plano. La verificación usa `hash_equals()` para evitar ataques de timing.

Protecciones adicionales del 2FA:
- Máximo 5 intentos fallidos antes del bloqueo temporal
- Mínimo 60 segundos entre reenvíos
- El código expira tras 10 minutos aunque no se haya usado

### Rate limiting en login
`login_attempts` registra cada intento de autenticación con email, IP y resultado. Si se detectan demasiados intentos fallidos desde la misma IP o para el mismo email, el sistema bloquea temporalmente los intentos.

Esto previene ataques de fuerza bruta contra las cuentas de usuario.

---

## Autorización

### Sistema de roles
La tabla `users` define dos roles mediante ENUM: `admin` y `customer`.
El rol se guarda en `$_SESSION['user_role']` al completar el login.

### Guards en controladores
Cada método protegido llama al guard correspondiente como primera instrucción:

```php
// Usuarios autenticados
protected function requireAuth(): void

// Solo administradores
protected function requireAdmin(): void
```

Si la comprobación falla, el usuario es redirigido inmediatamente — el código del método nunca llega a ejecutarse.

---

## Protección CSRF

Todos los formularios incluyen un token CSRF generado con `bin2hex(random_bytes(32))` y almacenado en sesión. Cada POST verifica que el token del formulario coincida con el de la sesión usando `hash_equals()` para evitar ataques de timing.

```php
<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
```

Si el token no coincide, se regenera y se redirige al formulario con
un mensaje de error — nunca se procesa la petición.

---

### Sesión por inactividad
En lugar de un tiempo fijo desde el login, la sesión expira por inactividad. Cada petición autenticada actualiza `$_SESSION['last_activity']`.

| Rol | Límite de inactividad |
|---|---|
| Usuario normal | 7 días |
| Administrador | 2 horas |

### Configuración de la cookie de sesión
```php
session_set_cookie_params([
    'lifetime' => 604800,  // 7 días — techo absoluto
    'path'     => '/',
    'secure'   => true,    // solo HTTPS en producción
    'httponly' => true,    // inaccesible desde JavaScript
    'samesite' => 'Lax',   // protección CSRF adicional
]);
```

`httponly` impide que JavaScript acceda a la cookie de sesión, bloqueando ataques XSS que intenten robarla. `secure` garantiza que la cookie solo se transmite por HTTPS.

---

## Comunicaciones cifradas

El dominio `primeluxshop.es` tiene SSL habilitado mediante el panel de IONOS. Todas las comunicaciones entre el cliente y el servidor usan HTTPS.

Las peticiones HTTP son redirigidas automáticamente a HTTPS mediante el `.htaccess`.

---

## Protección de datos sensibles

### config.php excluido de GitHub
El fichero `config/config.php` contiene credenciales de BD y claves de API de Stripe. Está incluido en `.gitignore` y nunca se sube al repositorio.

### Datos de tarjeta
Los datos de tarjeta de crédito nunca pasan por el servidor de la aplicación. Stripe Checkout gestiona el pago en sus propios servidores — la aplicación solo recibe el `session_id` para verificar el resultado.

### Prepared statements
Todas las consultas a la BD usan PDO con prepared statements, eliminando el riesgo de inyección SQL.

```php
$stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
```

---

## Seguridad en el panel admin

El vendor de Stripe (`public/vendor/`) está excluido del repositorio mediante `.gitignore` — son archivos de terceros que no deben versionarse.

Los datos sensibles mostrados en el panel (precios de coste, datos de usuarios, pedidos) solo son accesibles para usuarios con rol `admin`, verificado en cada petición mediante `requireAdmin()`.
