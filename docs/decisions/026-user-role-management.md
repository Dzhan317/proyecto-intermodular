# Gestión de roles de usuario — eliminación del botón de cambio de rol

## Contexto

Durante el desarrollo del panel de administración se implementó un botón "Ascender/Degradar" en la vista de usuarios que permitía cambiar el rol de un usuario entre `customer` y `admin` directamente desde la interfaz.

---

## Problema detectado

Tras analizar el comportamiento real de la funcionalidad, se identificó un problema de diseño: el sistema permitía crear administradores sin ningún tipo de restricción sobre sus permisos. Un administrador recién ascendido tendría acceso completo al panel, incluyendo la gestión de otros usuarios, productos, categorías y pedidos.

Esto contradice el principio de mínimo privilegio y no refleja cómo funcionaría un sistema de administración real, donde distintos roles tienen distintos niveles de acceso.

---

## Decisión

Se eliminó el botón "Ascender/Degradar" y toda la lógica asociada:

| Archivo | Cambio |
|---|---|
| `app/Controllers/AdminController.php` | Eliminado el método `updateUserRole()` |
| `app/Views/admin/users.php` | Eliminado el formulario del botón Ascender/Degradar |
| `app/routes.php` | Eliminada la ruta `POST /admin/users/:id/role` |

La columna "Rol" en la tabla de usuarios se mantiene como información de solo lectura — sigue siendo útil para identificar el tipo de cada cuenta.

---

## Mejora futura

Esta funcionalidad se deja documentada como mejora futura bajo el nombre **Sistema de roles múltiples**, que contempla:

- Un rol `superadmin` con acceso completo (el administrador actual)
- Roles de administrador con permisos limitados y configurables por sección
- Una interfaz para asignar y revocar permisos de forma controlada

Esta mejora requiere un rediseño del sistema de autorización y cambios en el esquema de la base de datos, por lo que queda fuera del alcance del proyecto actual.
