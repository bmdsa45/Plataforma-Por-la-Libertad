# Plataforma Por la Libertad (PPLuy)

Sitio web completo del movimiento PPLuy con funcionalidades públicas y panel de administración. Incluye secciones informativas, formularios de contacto/registro, sistema de administración y monitoreo de salud del sitio.

## Características principales
- **Sitio web público** con páginas informativas
- **Panel de administración** con autenticación
- **Sistema de contacto** con base de datos SQLite
- **Registro de usuarios** y gestión de donantes
- **Monitoreo de salud** del sitio en tiempo real
- **Diseño responsive** con bloques plegables móviles
- **Backup automático** de bases de datos por email

## Estructura del proyecto
```
PPLuy/
├── index.html                 # Portada principal
├── styles.css                 # Estilos globales
├── admin/                     # Panel de administración
│   ├── login.php             # Login de administrador
│   └── monitor.php           # Dashboard de administración
├── database/                  # Bases de datos SQLite
│   ├── contact_form.db       # Mensajes de contacto
│   ├── user_registry.db      # Registro de usuarios
│   └── create_user_registry.php
├── js/                       # Scripts JavaScript
│   ├── collapsible.js        # Bloques plegables móviles
│   ├── site-health.js        # Monitoreo de salud
│   ├── form-validation.js    # Validación de formularios
│   └── registro-validation.js
├── process_form.php          # Procesamiento de contacto
├── process_registro.php      # Procesamiento de registro
├── send_database_email.php   # Backup por email
└── [páginas públicas].html   # Contenido informativo
```

## Desarrollo local

### Servidor estático (para contenido HTML/CSS/JS)
```bash
python -m http.server 8000
```
Acceder a: `http://localhost:8000/`

### Servidor PHP (para funcionalidades backend)
```bash
php -S localhost:8080
```
Acceder a: `http://localhost:8080/`

**Panel de administración**: `http://localhost:8080/admin/login.php`
- Usuario: `admin@ppluy.org`
- Contraseña: `admin123`

## Salud y Seguridad del Sitio
- Ubicación: portada (`index.html`). Lógica en `js/site-health.js`. Estilos en `styles.css`.
- Indicadores:
  - **HTTPS**: Detecta si la conexión es segura (en local suele salir "No seguro").
  - **Latencia**: Mide tiempo de respuesta con `fetch` a `ppl.svg`.
  - **Disponibilidad**: Marca si el recurso responde correctamente.
  - **Escaneo de seguridad**: Puntaje según cabeceras/etiquetas clave presentes:
    - `Strict-Transport-Security` (HSTS)
    - `X-Content-Type-Options` (nosniff)
    - `Content-Security-Policy` (CSP) como cabecera o meta
    - `Referrer-Policy` como cabecera o meta
- Actualización: cada 30 segundos. Colores: verde (ok), amarillo (advertencia), rojo (crítico). Se muestra también "Última revisión" y un **estado general**.

## Despliegue en GitHub Pages
1. Subir a GitHub (rama `main`).
2. En el repo: `Settings` → `Pages` → "Build and deployment".
   - Source: `Deploy from a branch`
   - Branch: `main`, Folder: `/root`
3. La URL pública será similar a: `https://<usuario>.github.io/Plataforma-Por-la-Libertad/`
4. Nota: En Pages, **HTTPS** aparecerá "Activo". Algunas cabeceras pueden no estar visibles o gestionadas por GitHub (es normal). 

### Dominio propio (opcional)
- Configurar DNS (CNAME → `<usuario>.github.io`).
- En `Settings` → `Pages`: añadir el dominio.
- GitHub emitirá el certificado y el indicador HTTPS seguirá activo.

## Mantenimiento
- Actualizar contenido y hacer commit/push:
  - `git add .`
  - `git commit -m "Actualización de contenido"`
  - `git push`
- Revisar la portada para confirmar que los indicadores se muestran y actualizan.

## Notas de seguridad
- Para cabeceras como CSP/HSTS/nosniff/Referrer-Policy, en GitHub Pages la gestión de cabeceras la hace la plataforma. Las etiquetas `meta` ayudan en algunos casos, pero las cabeceras HTTP ofrecen mayor eficacia.

## Redes sociales
- X: https://x.com/PPLuy_
- Ubicación: footer de todas las páginas y sección social-media de `registro.html`.
- Icono: Font Awesome `fa-x-twitter` ya incluido en los HTML. Color configurado en `styles.css`.
- Para añadir otras redes: editar los bloques `.social-links` en los HTML y el estilo correspondiente en `styles.css`.
