# Categoría "Sin categoría" — eliminación segura de categorías

## Problema

El comportamiento original al eliminar una categoría con productos asociados
era devolver `false` y mostrar un error al administrador. Esto obligaba a
reasignar manualmente cada producto antes de poder eliminar la categoría.

---

## Solución implementada

Se creó una categoría especial **"Sin categoría"** con las siguientes
características:

- **ID fijo:** definido como constante en `CategoryModel::UNCATEGORIZED_ID`
- **Estado:** inactiva — no aparece en la tienda pública ni en el menú de categorías
- **Protegida:** no se puede eliminar desde el panel de administración

Al eliminar una categoría con productos, el flujo es:

1. `CategoryModel::delete()` comprueba si es la categoría protegida
2. Si no lo es, reasigna todos los productos de `product_categories` al ID protegido
3. Elimina la categoría
4. Devuelve `['deleted' => true, 'protected' => false, 'reassigned' => N]`

El administrador recibe el mensaje informativo con el número de productos
reasignados.

---

## Por qué inactiva y no oculta por código

Marcar "Sin categoría" como inactiva es la solución más simple: las queries
existentes de la tienda ya filtran por `status = 'active'`, por lo que no
es necesario modificar ningún otro archivo.

---

## Casos contemplados

| Situación | Resultado |
|---|---|
| Eliminar categoría sin productos | Se borra directamente |
| Eliminar categoría con productos | Productos reasignados, luego se borra |
| Intentar eliminar "Sin categoría" | Bloqueado con mensaje de error |

---

## Archivos modificados

| Archivo | Cambio |
|---|---|
| `app/Models/CategoryModel.php` | `delete()` devuelve array, añade reasignación y protección |
| `app/Controllers/AdminController.php` | `deleteCategory()` maneja los tres casos del resultado |
