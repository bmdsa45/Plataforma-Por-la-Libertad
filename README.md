# Plataforma Por la Libertad (PPLuy) - Angular Version

Sitio web moderno del movimiento PPLuy, migrado a **Angular 19** para ofrecer una experiencia de aplicación de una sola página (SPA) más rápida, interactiva y segura.

## 🚀 Nuevas Características (Angular)
- **Single Page Application (SPA)**: Navegación instantánea entre secciones sin recarga de página.
- **Componentización**: Arquitectura modular con componentes reutilizables para Header, Footer y Páginas.
- **Formularios Reactivos**: Validación avanzada en tiempo real para Contacto y Registro.
- **Estado Dinámico**: Gestión de menús móviles y monitores de salud integrados en el ciclo de vida de Angular.
- **Seguridad Mejorada**: Sanitización automática de contenido y protección contra ataques comunes (XSS/CSRF) integrada en el framework.

## 📁 Estructura del Proyecto Angular
```
ppl-angular/
├── src/
│   ├── app/
│   │   ├── components/       # Componentes globales (Header, Footer)
│   │   ├── pages/            # Páginas de la aplicación (Home, About, etc.)
│   │   ├── app.routes.ts     # Configuración de enrutamiento
│   │   └── app.component.ts  # Componente raíz
│   ├── assets/               # Scripts legacy y recursos estáticos
│   └── styles.css            # Estilos globales migrados
├── public/                   # Imágenes, SVGs y recursos públicos
└── angular.json              # Configuración del espacio de trabajo
```

## 🛠️ Desarrollo Local

### Requisitos previos
- Node.js (v18.13.0 o superior)
- Angular CLI (`npm install -g @angular/cli`)

### Instalación y Ejecución
1. Entrar en la carpeta del proyecto:
   ```bash
   cd ppl-angular
   ```
2. Instalar dependencias:
   ```bash
   npm install
   ```
3. Iniciar el servidor de desarrollo:
   ```bash
   npx ng serve
   ```
4. Abrir en el navegador: `http://localhost:4200/`

## 📊 Monitor de Salud y Seguridad
La funcionalidad de monitoreo ahora está integrada en el componente `HealthMonitor`, permitiendo:
- Actualización de métricas de rendimiento y seguridad en tiempo real.
- Verificación dinámica de estados de conexión y latencia.
- Visualización de medidas de seguridad implementadas (CSRF, XSS, HTTPS).

## 🌐 Despliegue en GitHub Pages
Para desplegar la versión de Angular en GitHub Pages:
1. Instalar el paquete de despliegue:
   ```bash
   npm install -g angular-cli-ghpages
   ```
2. Construir el proyecto:
   ```bash
   npx ng build --base-href /Plataforma-Por-la-Libertad/
   ```
3. Desplegar:
   ```bash
   npx ngh --dir=dist/ppl-angular/browser
   ```

---
*Nota: La estructura original en HTML/PHP se mantiene en la raíz para referencia, pero el desarrollo principal se ha movido a la carpeta `ppl-angular`.*
