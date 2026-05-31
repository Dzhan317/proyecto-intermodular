# Diseño responsive — menú hamburguesa y adaptación móvil

## Contexto

La aplicación se desarrolló inicialmente con foco en escritorio. Durante las
pruebas en dispositivos móviles se detectaron tres problemas principales:

1. El menú de navegación de la tienda ocupaba espacio innecesario en móvil
   y los iconos del header se solapaban
2. El panel de administración no era usable en pantallas pequeñas — el
   sidebar fijo empujaba el contenido fuera de la pantalla
3. El badge de mensajes no leídos del soporte no era visible en móvil
   porque el icono estaba oculto con `hidden md:flex`

---

## Soluciones implementadas

### Menú hamburguesa en la tienda

En pantallas menores a `md` (768px), la barra de navegación se oculta y
aparece un botón hamburguesa (☰/✕) en el header. Al pulsarlo, se despliega
un panel con los enlaces de navegación, el buscador y el acceso al soporte.
En escritorio no cambia nada.

### Panel de administración responsive

El sidebar del admin se oculta en móvil (`hidden md:flex`) y aparece un
botón hamburguesa en el header del panel. Al pulsarlo, se despliega el menú
completo debajo del header con todos los enlaces, Ver tienda y Cerrar sesión.
Los paddings del contenido se reducen en móvil (`px-4 md:px-8`).

### Badge de soporte en móvil

El badge de mensajes no leídos se duplicó para que aparezca también dentro
del menú hamburguesa de la tienda, junto al enlace "Soporte". El mismo
endpoint `/support/unread` alimenta ambos badges mediante `initSupportBadge()`.

---

## Archivos modificados

| Archivo | Cambio |
|---|---|
| `app/Views/layouts/partials/header.php` | Hamburguesa tienda + badge móvil |
| `app/Views/layouts/admin.php` | Hamburguesa admin + paddings responsive |
| `public/assets/js/support-chat.js` | `initSupportBadge()` actualiza `supportBadgeMobile` |
| `public/assets/css/app.css` | `overflow-x: hidden` en html y body |
