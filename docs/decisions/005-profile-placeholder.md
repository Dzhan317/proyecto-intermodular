# Vista de perfil de usuario — placeholder en Fase 2

## Por qué existe esta vista en Fase 2

El perfil de usuario (`/profile`) tiene su ruta definida en `routes.php` desde la Fase 0.
Si un usuario autenticado intenta acceder a esa URL antes de que se implemente en Fase 3,
el router intentaría cargar `ProfileController` que no existe → error fatal en producción.

Para evitar ese error y dejar la base preparada, se crea en Fase 2:
- La vista `app/Views/profile/index.php` con la estructura HTML de las secciones
- Sin controlador ni lógica — la ruta todavía apunta a `ProfileController@index` que no existe

## Qué tiene preparado para Fase 3

| Sección | Contenido en Fase 3 |
|---|---|
| Datos personales | Formulario para editar nombre, apellidos y teléfono |
| Cambiar contraseña | Formulario con contraseña actual y nueva (doble validación) |
| Direcciones de envío | CRUD de direcciones vinculado a la tabla `addresses` |

## Nota sobre los 6 inputs del 2FA

En esta misma fase se implementó la pantalla de verificación con 6 cajas individuales
en lugar de un input único. Es el patrón estándar en aplicaciones bancarias y de autenticación.
El comportamiento (avance automático, retroceso con backspace, pegado desde portapapeles
y envío automático al completar) está implementado en `public/assets/js/auth.js`
como función reutilizable `initTwoFactorInputs`.
