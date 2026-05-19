# Decisiones de seguridad en autenticación — Fase 1

Decisiones técnicas tomadas durante la implementación del sistema de autenticación y la justificación de cada una.

---

## Protección contra enumeración de usuarios

El flujo de login se divide en dos pasos: primero el email, luego la contraseña. Si en el paso 1 se mostrara "este email no existe" cuando el email no está registrado, un atacante podría descubrir qué emails tienen cuenta en la plataforma enviando miles de peticiones automatizadas.

La solución implementada es mostrar siempre el mismo mensaje de error genérico, independientemente de si el email existe o no: *"Credenciales incorrectas. Comprueba tu correo y contraseña."* Esto hace que el atacante no pueda distinguir entre un email inexistente y una contraseña incorrecta.

El mismo criterio se aplica al formulario de recuperación de contraseña: siempre se muestra *"Si el correo está registrado, recibirás un enlace en breve."*

---

## Hashing de contraseñas con bcrypt

Las contraseñas se almacenan usando `password_hash()` con el algoritmo `PASSWORD_BCRYPT` y coste 12. El coste 12 significa que el algoritmo realiza 2^12 = 4096 iteraciones, lo que hace que cada hash tarde aproximadamente 300ms en generarse. Este tiempo es imperceptible para un usuario legítimo, pero hace que un ataque de fuerza bruta por diccionario sea computacionalmente inviable.

Se descartó MD5 y SHA-256 porque son algoritmos de hash rápido, diseñados para integridad de datos, no para contraseñas.

---

## Rate limiting de intentos de login

La tabla `login_attempts` registra cada intento de autenticación con el email, la IP de origen y si fue exitoso. Si se detectan 5 o más intentos fallidos en los últimos 15 minutos desde el mismo email o IP, se bloquea temporalmente el acceso con el mensaje de demasiados intentos.

El bloqueo es temporal y se libera automáticamente transcurridos 15 minutos, lo que evita que un atacante pueda bloquear permanentemente la cuenta de un usuario legítimo.

---

## Tokens CSRF en todos los formularios

Todos los formularios POST incluyen un token CSRF generado con `bin2hex(random_bytes(32))` y almacenado en sesión. El servidor valida que el token del formulario coincida con el de sesión antes de procesar cualquier petición. Esto previene ataques Cross-Site Request Forgery, donde un sitio malicioso podría hacer que el navegador del usuario enviara peticiones no autorizadas.

---

## Tokens de recuperación de contraseña

Los tokens de recuperación se generan con `bin2hex(random_bytes(32))` (256 bits de entropía), se almacenan como hash SHA-256 en la tabla `password_resets` y expiran en 1 hora. El token en claro solo viaja una vez por email y nunca se guarda en la base de datos.

Cada token tiene el campo `used` que se marca como verdadero tras su primer uso, impidiendo que el mismo enlace se pueda utilizar dos veces.

---

## Requisitos de contraseña

Se exigen los siguientes criterios mínimos en el registro y al restablecer contraseña:

- Mínimo 10 caracteres
- Al menos 2 letras mayúsculas
- Al menos 2 letras minúsculas
- Al menos 2 números
- Al menos 1 carácter especial

Estos criterios se validan tanto en el frontend (indicador visual en tiempo real) como en el backend (antes de guardar en base de datos). La validación frontend es orientativa; la del backend es la que tiene valor de seguridad real.
