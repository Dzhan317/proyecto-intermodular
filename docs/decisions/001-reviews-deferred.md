# Reseñas de productos — decisión de aplazamiento

## Qué se diseñó

En los diseños originales del proyecto se contempló una pantalla de reseñas que permitiría a los usuarios valorar los productos que han comprado, con puntuación de 1 a 5 estrellas y comentario libre.

## Por qué no se implementa en el MVP

El alcance del proyecto está acotado a 64 horas de desarrollo con 9 fases definidas. Implementar las reseñas correctamente requiere:

- Pantalla de reseña en el detalle de producto
- Restricción: solo pueden reseñar usuarios que hayan comprado ese producto
- Listado de reseñas con paginación
- Moderación desde el panel de administración
- Cálculo de puntuación media

Ese trabajo se estima en 5-8 horas que comprometen la entrega de fases con mayor impacto directo en la funcionalidad del e-commerce.

## Estado actual

La tabla `reviews` está creada en el schema desde la Fase 0 para no tener que modificar la base de datos si se implementa en el futuro. El código de aplicación no la utiliza todavía.

## Condición para implementarla

Si las 9 fases del MVP quedan cerradas con tiempo disponible, las reseñas se añadirán como décima fase sin impacto en la estructura existente.
