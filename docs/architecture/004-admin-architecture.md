# Arquitectura del panel de administración

## Separación de contextos

La aplicación tiene dos contextos completamente distintos:

**Tienda pública** (`layouts/main.php`)
- Accesible por cualquier visitante
- Header con logo, buscador, carrito y categorías
- Footer con enlaces informativos
- Enfocada en la experiencia de compra del cliente

**Panel de administración** (`layouts/admin.php`)
- Accesible solo por usuarios con rol `admin`
- Sidebar de navegación lateral con secciones del admin
- Header simplificado con nombre del administrador y acceso a la tienda
- Enfocada en la gestión interna del negocio

Cada contexto usa su propio layout PHP — no comparten ningún partial de
cabecera o pie de página.

---

## Estructura de archivos del admin

```
app/
├── Controllers/
│   └── AdminController.php        — lógica de todas las secciones admin
├── Views/
│   ├── layouts/
│   │   └── admin.php              — layout con sidebar
│   └── admin/
│       ├── dashboard.php          — estadísticas y últimos pedidos
│       ├── products.php           — listado de productos con margen
│       ├── products-form.php      — formulario crear/editar producto
│       ├── orders.php             — listado de pedidos con cambio de estado
│       ├── users.php              — listado de usuarios con bloqueo
│       ├── categories.php         — listado de categorías
│       └── categories-form.php    — formulario crear/editar categoría
```

---

## Patrón de formularios compartidos

Los formularios de creación y edición comparten la misma vista PHP.
La variable `$isEdit = !empty($product)` controla el comportamiento:

- `$isEdit = false` → formulario vacío, botón "Crear", action apunta a `/create`
- `$isEdit = true`  → formulario relleno con datos actuales, botón "Guardar cambios", action apunta a `/:id/edit`

Este patrón evita duplicar código HTML y garantiza coherencia visual entre
ambas operaciones.

---

## Sidebar dinámico

El sidebar del layout admin genera los enlaces de navegación a partir de un array PHP definido en el propio layout. El ítem activo se detecta comparando la URL actual con el `match` de cada enlace, aplicando la clase
`bg-[var(--color-brand)]` al ítem correspondiente.

Esto permite añadir nuevas secciones al panel simplemente añadiendo una entrada al array — sin modificar la lógica del sidebar.
