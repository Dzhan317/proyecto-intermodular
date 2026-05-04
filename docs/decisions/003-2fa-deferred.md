# Verificación 2FA diferida a Fase 2

## Qué se diseñó

Desde el inicio se contempló un 2FA obligatorio: tras el login correcto, el sistema genera un código de 6 dígitos, lo envía por email y solicita su introducción antes de conceder acceso.

## Por qué se divide en dos fases

La Fase 1 establece la capa de autenticación base. Verificar que funciona en producción antes de añadir el 2FA tiene sentido porque ambos comparten la misma infraestructura de email y sesión. Si hubiera un problema, separarlo permite aislar el fallo con claridad.

## Estado al cerrar Fase 1

- Tabla `two_factor_codes` creada con todos los campos necesarios
- Vista `auth/verify-2fa.php` creada como placeholder funcional
- Rutas `/verify-2fa` definidas en `routes.php`
- El controlador redirige al formulario tras login exitoso
- La lógica completa (generación, envío y validación) se implementa en Fase 2

## Condición para implementarlo

La Fase 2 comienza directamente con el 2FA. No hay dependencias bloqueantes.
