# Stack tecnológico — PrimeLux SmartShop

Justificación de cada tecnología elegida y las alternativas descartadas.

---

## PHP 8.4 (backend)

PHP es el lenguaje más extendido en hosting compartido. IONOS lo soporta nativamente sin configuración adicional, lo que elimina la necesidad de entornos Docker o servidores dedicados. La versión 8.4 aporta union types, `readonly` properties y mejoras de rendimiento respecto a versiones anteriores.

**Alternativas descartadas:** Node.js y Python requieren configuración de servidor que el hosting compartido de IONOS no permite sin un plan más caro.

---

## MySQL 8 (base de datos)

Motor relacional incluido en el plan de hosting de IONOS sin coste adicional. La naturaleza relacional del e-commerce (productos, variantes, pedidos, usuarios) encaja bien con un modelo tabular y claves foráneas. Las 21 tablas del schema están normalizadas y usan índices en las columnas de búsqueda frecuente.

**Alternativas descartadas:** PostgreSQL no está disponible en el plan de IONOS contratado. MongoDB requeriría modelar relaciones de forma manual, lo que añade complejidad sin beneficio claro para este caso de uso.

---

## Tailwind CSS via CDN (estilos)

Tailwind permite construir interfaces directamente en el HTML sin cambiar de contexto entre archivos. La versión CDN elimina el paso de compilación, lo que es suficiente para un proyecto de este tamaño y ahorra tiempo de configuración de herramientas de build.

El CDN se carga desde los layouts de vistas (`main.php`, `auth.php`, `checkout.php`) mediante:

```
<script src="https://cdn.tailwindcss.com"></script>
```

Los estilos que Tailwind no puede gestionar (variables CSS del sistema de diseño, autofill de inputs, scrollbar) se gestionan en `public/assets/css/app.css`.

**Alternativas descartadas:** Bootstrap impone demasiada estructura visual predefinida que contradice el sistema de diseño propio del proyecto. Compilar Tailwind con npm añadiría un paso de build que complica el despliegue en IONOS.

---

## JavaScript vanilla ES6 (frontend)

No se usa ningún framework de frontend porque las interacciones de la aplicación son sencillas: toggles, validación de formularios, indicadores visuales. Añadir React o Vue para esto sería sobreingeniería. El código JS está organizado en módulos por sección (`auth.js`, etc.) dentro de `public/assets/js/`.

---

## Stripe Checkout (pagos)

Stripe Checkout delega toda la pantalla de pago a Stripe, lo que significa que los datos de tarjeta nunca pasan por el servidor. Esto simplifica enormemente el cumplimiento de PCI DSS y elimina la responsabilidad de gestionar datos financieros sensibles. La integración se hace mediante sesiones de Checkout y webhooks para confirmar el pago.

**Alternativas descartadas:** PayPal tiene una API más compleja y peor documentación para integraciones nuevas. Implementar un formulario de pago propio requeriría certificación PCI DSS.

---

## IONOS Hosting Compartido (infraestructura)

El dominio principal `primeluxshop.es` y el hosting están contratados en IONOS. El dominio `primeluxmarket.com` actúa como redirección hacia el dominio principal y no aloja ningún archivo ni dato sensible. El plan incluye PHP 8.4, MySQL 8, SMTP y certificado SSL. El despliegue es por FTP (filezilla).

El directorio raíz del dominio apunta a `/public`, lo que garantiza que `app/` y `config/` (con las credenciales) son inaccesibles desde el navegador.

---

## Git + GitHub (control de versiones)

Estrategia de ramas Git Flow simplificada: `main` (producción estable), `develop` (integración) y ramas `feature/fase-N-nombre` para cada fase. Los merges siguen el protocolo feature → develop → main, nunca directo a main.

El archivo `config.php` está excluido del repositorio mediante `.gitignore` para evitar exponer credenciales.
