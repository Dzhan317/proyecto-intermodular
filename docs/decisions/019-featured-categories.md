# Categorías destacadas en la home — Fase 7

## Problema

Con la ampliación del catálogo a 14 categorías, mostrar todas en la portada
de la tienda generaba una cuadrícula demasiado extensa. En e-commerce reales
(Amazon, PCComponentes, MediaMarkt) la home muestra una selección de
categorías principales, no el listado completo.

---

## Solución implementada

Se añade la columna `featured BOOLEAN DEFAULT FALSE` a la tabla `categories`.
El administrador controla desde el panel qué categorías aparecen en la home
marcando o desmarcando el checkbox "Mostrar en la home" en el formulario de
cada categoría.

```sql
ALTER TABLE categories ADD COLUMN featured BOOLEAN DEFAULT FALSE AFTER status;
```

---

## Funcionamiento

**En la home:** `CategoryModel::getFeatured()` devuelve solo las categorías
con `featured = 1` y `status = active`. Las demás siguen siendo accesibles
desde el menú de navegación (desplegable de categorías en el header).

**En el admin:** el listado de categorías muestra una columna "En home" con
el badge "★ Destacada" para identificar visualmente cuáles están activas en
la portada. El formulario de crear/editar incluye el checkbox de control.

---

## Por qué no se limita el número máximo

Añadir una restricción de máximo N categorías destacadas requeriría lógica
adicional en el controlador y mensajes de error específicos. Dado el tiempo
disponible y que el administrador es el único que gestiona esto, se optó por
un checkbox libre sin restricción numérica — el admin decide cuántas mostrar
con criterio propio.

---

## Navegación completa

Las categorías no destacadas no desaparecen — siguen siendo accesibles desde
el desplegable "Categorías" del header de navegación, que usa `getAll()` y
devuelve todas las categorías activas independientemente del campo `featured`.
