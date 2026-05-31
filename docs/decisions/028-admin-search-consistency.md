# Unificación del buscador del panel admin

## Contexto

El panel de administración tiene buscadores en tres secciones: productos, usuarios y categorías. Los buscadores de productos y usuarios funcionaban mediante formulario GET (requieren pulsar Enter), mientras que el de categorías funcionaba en tiempo real con JavaScript, filtrando las filas visibles en el DOM sin recargar la página.

---

## Problema detectado

El filtrado en tiempo real con JavaScript solo funciona correctamente cuando todos los registros están cargados en el DOM. En categorías esto es viable porque el número de categorías es reducido. Sin embargo, productos y usuarios tienen paginación — el JS solo filtraría los registros visibles en la página actual, no todos los de la BD.

Mantener dos comportamientos distintos en el mismo panel genera incoherencia de UX y puede confundir al usuario.

---

## Decisión

Se eliminó el buscador en tiempo real de categorías y se unificó con el patrón de formulario GET usado en productos y usuarios.

| Archivo | Cambio |
|---|---|
| `app/Views/admin/categories.php` | Input suelto → formulario GET, eliminado JS y atributos `data-name` |
| `app/Controllers/AdminController.php` | Método `categories()` — añadido filtrado por `$_GET['q']` |

Todos los buscadores del panel admin muestran ahora el placeholder *"(Enter para buscar)"* para que el comportamiento sea explícito.

---

## Mejora futura

Implementar búsqueda en tiempo real mediante AJAX para todas las secciones del panel admin, de forma que funcione correctamente con paginación y sin necesidad de recargar la página.
