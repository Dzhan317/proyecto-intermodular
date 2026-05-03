# app/

Contiene toda la lógica de la aplicación, organizada por capas.

- `Core/` — clases base del sistema: router, conexión a BD y controlador base
- `Controllers/` — reciben las peticiones y coordinan la respuesta
- `Models/` — acceso y consultas a la base de datos
- `Services/` — lógica de negocio compleja (carrito, pedidos, pagos, 2FA)
- `Helpers/` — funciones de utilidad reutilizables
- `Views/` — plantillas HTML que se muestran al usuario
