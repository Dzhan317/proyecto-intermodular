# Decisiones de seguridad en autenticación — Fase 1

Decisiones técnicas tomadas durante la implementación del sistema de autenticación y la justificación de cada una.

---

## Protección contra enumeración de usuarios

El flujo de login se divide en dos pasos: primero el email, luego la contraseña. Si en el paso 1 se mostrara "este email no existe" cuando el email no está registrado, un atacante podría descubrir qué emails tienen cuenta en la plataforma enviando miles de peticiones automatizadas.

La solución implementada es mostrar siempre el mismo mensaje de error genérico: *"Credenciales incorrectas. Comprueba tu correo y contraseña."* El atacante no puede distinguir entre un email inexistente y una contraseña incorrecta.

El mismo criterio se aplica al formulario de recuperación de contraseña.

---

## Hashing de contraseñas con bcrypt

Las contraseñas se almacenan usando `password_hash()` con `PASSWORD_BCRYPT` y coste 12. El coste 12 significa 2^12 = 4096 iteraciones, lo que hace cada hash tarde ∼300ms. Imperceptible para el usuario, inviable para un ataque de diccionario.

Se descartó MD5 y SHA-256 porque son algoritmos de hash rápido, diseñados para integridad de datos, no para contraseñas.

---

## Rate limiting de intentos de login

La tabla `login_attempts` registra cada intento con el email, la IP y si fue exitoso. Tras 5 intentos fallidos en 15 minutos desde el mismo email o IP, el acceso se bloquea temporalmente. El bloqueo se libera solo, lo que evita que un atacante bloquee permanentemente la cuenta de otro usuario.

---

## Tokens CSRF en todos los formularios

Todos los formularios POST incluyen un token CSRF generado con `bin2hex(random_bytes(32))` y almacenado en sesión. Previene ataques Cross-Site Request Forgery.

---

## Tokens de recuperación de contraseña

Generados con `bin2hex(random_bytes(32))` (256 bits), almacenados como hash SHA-256, expiran en 1 hora y tienen un campo `used` que impide reutilizarlos.

---

## Requisitos de contraseña

Mínimo 10 caracteres, 2 mayúsculas, 2 minúsculas, 2 números y 1 carácter especial. Validación en frontend (orientativa) y en backend (real).
