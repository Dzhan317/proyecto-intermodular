# Precio de coste y márgenes — Fase 7

## Contexto

Un e-commerce real necesita conocer no solo los ingresos brutos sino también
el coste de los productos vendidos para calcular el margen de beneficio.
Sin esta información, el dashboard admin solo muestra ingresos — que no
reflejan la rentabilidad real del negocio.

Esta funcionalidad fue incorporada a propuesta del tutor de prácticas, que
trabaja con sistemas de gestión de márgenes en entornos reales.

---

## Cambio en la BD

```sql
ALTER TABLE products
ADD COLUMN cost_price DECIMAL(10,2) UNSIGNED DEFAULT 0.00 AFTER base_price;
```

`cost_price` representa el precio que el administrador paga al proveedor por
cada unidad del producto. Solo es visible en el panel de administración.

---

## Cómo se introduce el dato

El administrador introduce el precio de coste manualmente en el formulario de
creación/edición de cada producto. Esto refleja la realidad de un negocio
real — cada producto tiene su propio acuerdo con el proveedor y su propio
margen.

Márgenes orientativos por categoría usados en los datos iniciales:

| Categoría | Margen aproximado | cost_price = base_price × |
|---|---|---|
| Portátiles / PC | 12% | 0.88 |
| Móviles / Tablets | 8% | 0.92 |
| Componentes | 15% | 0.85 |
| Periféricos | 28% | 0.72 |
| Software / Licencias | 50% | 0.50 |
| Redes | 20% | 0.80 |

---

## Cálculo de métricas en el dashboard

```sql
-- Ingresos totales (pedidos pagados, enviados o entregados)
SELECT COALESCE(SUM(total), 0)
FROM orders
WHERE status IN ('paid', 'shipped', 'delivered');

-- Coste total de productos vendidos
SELECT COALESCE(SUM(oi.quantity * p.cost_price), 0)
FROM order_items oi
INNER JOIN variants v ON v.id = oi.variant_id
INNER JOIN products p ON p.id = v.product_id
INNER JOIN orders o   ON o.id = oi.order_id
WHERE o.status IN ('paid', 'shipped', 'delivered');
```

**Margen bruto** = ingresos − coste total

**% margen** = (margen bruto / ingresos) × 100

---

## Por qué `paid + shipped + delivered`

El estado logístico de un pedido (`shipped`, `delivered`) no afecta al
ingreso — Stripe ya procesó el cobro cuando el pedido pasó a `paid`.
Solo `pending` (pago no confirmado) y `cancelled` se excluyen porque no
representan dinero recibido.

---

## Visualización en el listado de productos

Cada producto muestra su % de margen individual con código de color:

| Color | Significado |
|---|---|
| Verde | Margen ≥ 20% — rentable |
| Ámbar | Margen 10-19% — margen ajustado |
| Rojo | Margen < 10% — revisar pricing |

Si `cost_price = 0` (no introducido), se muestra "Sin datos" en lugar de
un porcentaje erróneo.

---

## Limitaciones

Este sistema calcula el **margen bruto** — no el beneficio neto. No contempla:
- Comisiones de Stripe (1,4% + 0,25 € por transacción en Europa)
- Costes de envío asumidos por el negocio
- Gastos generales (hosting, dominio, etc.)

Para el alcance de este proyecto, el margen bruto es suficiente para
demostrar el concepto y satisfacer el requisito planteado.
