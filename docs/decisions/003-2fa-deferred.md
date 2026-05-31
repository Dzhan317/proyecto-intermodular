# Verificación 2FA diferida a Fase 2

## Qué se diseñó

Desde el inicio del proyecto se contempló un sistema de autenticación en dos factores obligatorio para todos los usuarios. Al completar el login con email y contraseña correctos, el sistema genera un código de 6 dígitos, lo envía al correo del usuario y solicita su introducción antes de conceder acceso.

## Por qué se divide en dos fases

La Fase 1 establece la capa de autenticación base: registro, login, gestión de sesión, protección contra fuerza bruta y recuperación de contraseña. Verificar que esta capa funciona correctamente en producción antes de añadir el 2FA tiene sentido porque ambos sistemas comparten la misma infraestructura de email y sesión.

Si hubiera un problema con el envío de emails o con la gestión de sesión, sería difícil saber si el fallo está en la autenticación base o en el 2FA. Separarlo permite aislar y resolver problemas de forma más ordenada.

## Estado al cerrar Fase 1

- Tabla `two_factor_codes` creada en el schema con todos los campos necesarios
- Vista `auth/verify-2fa.php` creada como placeholder funcional
- Rutas `/verify-2fa` (GET y POST) definidas en `routes.php`
- El controlador redirige al formulario tras login exitoso
- La lógica de generación del código, envío por email y validación se implementa en Fase 2

## Condición para implementarlo

La Fase 2 comienza directamente con el 2FA. No hay dependencias bloqueantes.
