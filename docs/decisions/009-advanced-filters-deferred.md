# Filtros avanzados por categoría — mejora futura

## Qué se diseñó

El anteproyecto contemplaba filtros dinámicos adaptados a cada categoría. Por ejemplo:
- Informática: marca, RAM, almacenamiento, procesador
- Electrónica: marca, sistema operativo, tamaño de pantalla
- Componentes: tipo, compatibilidad, capacidad
- Periféricos: tipo de conexión, marca, compatibilidad

## Qué se implementó en Fase 4

Filtros genéricos aplicables a cualquier categoría:
- Rango de precio con slider doble (valores dinámicos según la categoría)
- Disponibilidad (solo en stock)
- Orden (más recientes, precio ascendente/descendente)

## Por qué no se implementaron los filtros avanzados

Los filtros avanzados por categoría requieren una tabla de atributos de producto que no existe en el schema actual. La estructura necesaria sería:

```sql
CREATE TABLE product_attributes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    name        VARCHAR(100) NOT NULL,   -- ej: "RAM", "Marca", "Almacenamiento"
    value       VARCHAR(255) NOT NULL,   -- ej: "16 GB", "Apple", "512 GB"
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

Con esta tabla se podrían extraer dinámicamente los atributos disponibles en cada categoría y mostrarlos como checkboxes en el sidebar, sin hardcodear nada.

## Condición para implementarlo

Requiere una nueva migración de base de datos y poblar los atributos de los productos existentes. Estimación: 4-6 horas. Se implementaría como una fase adicional si hay tiempo disponible tras completar las fases principales.
