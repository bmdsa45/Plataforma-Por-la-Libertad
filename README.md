# Plataforma Por la Libertad (PPLuy)

Sitio web informativo del movimiento PPLuy. Incluye secciones públicas, formulario de contacto/registro y mejoras de usabilidad móvil (bloques plegables). Se añadió una sección de **Salud y Seguridad del Sitio** en la portada con indicadores claros y actualización automática.

## Estructura rápida
- `index.html`: Portada (con sección de salud).
- `styles.css`: Estilos globales.
- `js/collapsible.js`: Bloques plegables en móvil (redes/footer/FAQs).
- `js/site-health.js`: Indicadores de salud en tiempo real.
- `contacto.html`, `registro.html`, `documentos.html`, `noticias.html`, `propuestas.html`, `quienes-somos.html`: Páginas del sitio.

## Desarrollo local
1. En la carpeta del proyecto, ejecutar: `python -m http.server 8000`
2. Abrir: `http://localhost:8000/index.html`

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
