# Arquitectura MVC — PrimeLux SmartShop

## Por qué MVC

Se eligió el patrón Modelo-Vista-Controlador porque separa de forma clara tres responsabilidades que en un e-commerce crecen de forma independiente: la lógica de negocio, el acceso a datos y la presentación al usuario. Esto permite trabajar en el panel de administración sin tocar las vistas del cliente, o cambiar una consulta SQL sin afectar al HTML.

Se descartaron los frameworks PHP más populares (Laravel, Symfony) porque el hosting compartido de IONOS no tiene restricciones que los requieran, y un MVC ligero propio cubre exactamente lo que el proyecto necesita sin dependencias externas.

---

## Estructura de carpetas

```
primelux-smartshop/
├── public/              Único directorio expuesto al servidor web
│   ├── index.php        Punto de entrada — todas las peticiones pasan por aquí
│   ├── .htaccess        Routing limpio y redirección HTTPS
│   └── assets/
│       ├── css/         Estilos globales (app.css)
│       ├── js/          Scripts por sección (auth.js, ...)
│       └── img/         Imágenes, iconos, logos y favicon
│
├── app/                 Lógica de la aplicación — inaccesible desde el navegador
│   ├── Core/
│   │   ├── Router.php       Mapea URLs a controladores
│   │   ├── Database.php     Conexión PDO singleton
│   │   └── Controller.php   Clase base con métodos comunes
│   ├── Controllers/     Reciben la petición y coordinan la respuesta
│   ├── Models/          Consultas a la base de datos
│   ├── Services/        Lógica de negocio compleja
│   ├── Views/           Plantillas HTML organizadas por sección
│   └── routes.php       Mapa completo de URLs de la aplicación
│
└── config/
    ├── config.php           Configuración real (solo en local y servidor)
    └── config.example.php   Plantilla sin credenciales (en GitHub)
```

---

## Responsabilidad de cada capa

**Controllers** — Reciben la petición HTTP, validan el token CSRF, llaman al Service o Model correspondiente y devuelven una vista o redirección. No contienen lógica de negocio ni consultas SQL directas.

**Models** — Contienen exclusivamente las consultas a la base de datos usando PDO con sentencias preparadas. Un modelo por tabla principal. No conocen nada de HTTP ni de vistas.

**Services** — Contienen la lógica de negocio que es demasiado compleja para un controlador: procesamiento de pagos, envío de emails, generación de tokens, cálculo de recomendaciones. Un servicio puede usar varios modelos.

**Views** — Plantillas PHP que generan HTML. Reciben datos del controlador mediante `extract()`. No hacen consultas ni lógica de negocio. El layout compartido de cada sección (cabecera, pie, assets) vive en `Views/layouts/`.

**Core** — Las tres clases base del sistema. No se modifican durante el desarrollo de funcionalidades; solo si hay que cambiar algo estructural del framework.

---

## Flujo de una petición

```
Navegador → .htaccess → index.php → Router → Controller → Service/Model → View → Respuesta
```

1. El navegador hace una petición a cualquier URL
2. `.htaccess` la redirige a `index.php` si no es un archivo físico
3. `index.php` carga la configuración, arranca la sesión y llama al Router
4. El Router compara la URL con las rutas definidas en `routes.php`
5. El Controller correspondiente recibe la petición y los parámetros
6. El Controller llama al Service o Model que necesite
7. El resultado se pasa a la View, que genera el HTML
8. El HTML se envía al navegador
