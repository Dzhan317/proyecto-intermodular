# Configuración de despliegue — PrimeLux SmartShop

## Entorno de producción

**Proveedor:** IONOS Hosting Compartido
**Dominio principal:** https://primeluxshop.es
**Dominio secundario:** primeluxmarket.com (redirección al dominio principal, sin contenido propio)
**PHP:** 8.4
**Base de datos:** MySQL 8 — host `db5020370348.hosting-data.io`
**Certificado SSL:** Incluido en el plan, gestionado por IONOS

---

## Estructura de carpetas en el servidor

```
/ (raíz del servidor)
├── app/              Lógica de la aplicación
├── config/           Configuración (config.php solo en servidor, nunca en GitHub)
├── logs/             Logs de errores de producción
└── public/           Raíz del dominio — única carpeta expuesta al navegador
    ├── .htaccess
    ├── index.php
    └── assets/
```

El dominio `primeluxshop.es` apunta directamente a `/public`. Las carpetas `app/` y `config/` son inaccesibles desde el navegador.

---

## Cómo se despliega

El despliegue es manual mediante FTP/SFTP, utilizando Filezilla.

**Datos de conexión SFTP:**
- Host: `access-5020169311.webspace-host.com`
- Puerto: 22
- Protocolo: SFTP

**Archivos que NO se suben al servidor:**
- `config/config.php` — contiene credenciales reales, solo existe en local y en el servidor (no en GitHub)
- Archivos de `docs/` — documentación, no es código de la aplicación
- `.git/` — control de versiones, no necesario en producción

---

## Configuración del .htaccess

El archivo `public/.htaccess` hace tres cosas:

1. **Bloquea** el acceso directo a archivos sensibles (`.env`, `.sql`, `.log`, etc.)
2. **Fuerza HTTPS** — IONOS usa un proxy inverso, por lo que la redirección se detecta con la cabecera `X-Forwarded-Proto` en lugar de `HTTPS`
3. **Enruta** todas las peticiones a `index.php` excepto archivos y carpetas que existen físicamente

---

## Variables de entorno y configuración

Toda la configuración vive en `config/config.php`. Este archivo define:
- Rutas absolutas del proyecto (`ROOT_PATH`, `APP_PATH`, etc.)
- Credenciales de base de datos
- Credenciales SMTP para envío de email
- Claves de Stripe (Fase 6)
- Entorno (`APP_ENV`: `development` o `production`)

En producción, `APP_ENV = 'production'` desactiva la visualización de errores.

---

## Base de datos

El schema completo con las 21 tablas está en `docs/database/001-database-schema.sql`. Para importarlo, ejecutarlo desde phpMyAdmin sobre la base de datos correspondiente.

**Acceso a phpMyAdmin:** Panel IONOS → Bases de datos → Administrar
