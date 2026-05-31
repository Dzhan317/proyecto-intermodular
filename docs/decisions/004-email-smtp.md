# Implementación del email SMTP — Fase 2

## Contexto

El sistema 2FA necesita enviar correos de verificación con el código.
El proyecto está alojado en IONOS Hosting Compartido con un único buzón real: `admin@primeluxshop.es`.

---

## Configuración de correo en IONOS

| Dirección | Tipo | Uso |
|---|---|---|
| `admin@primeluxshop.es` | Buzón real | Autenticación SMTP |
| `no-reply@primeluxshop.es` | Reenvío (→ admin) | FROM visible en emails automáticos |
| `soporte@primeluxshop.es` | Reenvío (→ admin) | FROM visible en emails de soporte (Fase 8) |

Los reenvíos reciben mensajes entrantes y los redirigen al buzón real.
IONOS permite usar direcciones del mismo dominio como FROM aunque no sean la cuenta autenticada.

---

## Por qué MailService propio sin dependencias

Se descartó PHPMailer (la alternativa habitual) por tres razones:

**1. Sin Composer en producción** — El hosting compartido de IONOS permite Composer pero añadiría una carpeta `vendor/` de varios MB y un paso de instalación en el servidor. Para el alcance de este proyecto es desproporcionado.

**2. Complejidad innecesaria** — PHPMailer resuelve casos complejos (adjuntos, múltiples destinatarios, OAuth). El proyecto solo necesita enviar emails HTML simples a un destinatario. Un cliente SMTP propio de ~120 líneas cubre exactamente lo que se necesita.

**3. Fiabilidad** — Al controlar el código completo de la conexión SMTP, cualquier problema con IONOS se diagnostica y corrige directamente sin depender de la abstracción de una librería externa.

---

## Cómo funciona la conexión

1. `stream_socket_client` abre conexión TCP a `smtp.ionos.es:587`
2. `EHLO` anuncia el cliente al servidor
3. `STARTTLS` eleva la conexión a TLS cifrado
4. `AUTH LOGIN` autentica con `admin@primeluxshop.es`
5. `MAIL FROM` declara el remitente visible (`no-reply@primeluxshop.es`)
6. `RCPT TO` → `DATA` → mensaje → `QUIT`

---

## Fallback si IONOS rechaza el FROM del alias

Si el servidor devuelve error 550 al declarar `no-reply@primeluxshop.es` como remitente,
la solución es cambiar en `config.php`:

```php
define('MAIL_NOREPLY_ADDRESS', 'admin@primeluxshop.es');
define('MAIL_NOREPLY_NAME',    'PrimeLux SmartShop - No Reply');
```

El email llega igualmente. Solo cambia la dirección visible para el destinatario.
