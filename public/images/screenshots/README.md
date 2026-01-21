# Screenshots PWA

Para completar la configuración de PWA y eliminar los warnings, necesitas agregar screenshots de tu aplicación.

## Screenshots necesarios:

### 1. Desktop Screenshot (Obligatorio)
- **Nombre**: `desktop-screenshot.png`
- **Tamaño recomendado**: 1280x720px (mínimo)
- **Ratio**: 16:9 o similar (horizontal)
- **Contenido**: Captura de pantalla de la vista de escritorio del dashboard

### 2. Mobile Screenshot (Obligatorio)
- **Nombre**: `mobile-screenshot.png`
- **Tamaño recomendado**: 540x720px
- **Ratio**: 3:4 (vertical)
- **Contenido**: Captura de pantalla de la vista móvil del dashboard

## Cómo generar los screenshots:

### Opción 1: Usando Chrome DevTools
1. Abre tu aplicación en Chrome
2. Presiona F12 para abrir DevTools
3. Click en el icono de dispositivo móvil (Toggle Device Toolbar)
4. Para desktop: Selecciona "Responsive" y ajusta a 1280x720
5. Para mobile: Selecciona "iPhone 12 Pro" o similar
6. Toma la captura con la herramienta de captura de Chrome o con tu sistema

### Opción 2: Usando una extensión
- **GoFullPage**: Captura de pantalla completa
- **Awesome Screenshot**: Permite editar antes de guardar

### Opción 3: Screenshot programático
```bash
# Si tienes instalado Playwright o Puppeteer
npx playwright screenshot http://localhost:8080/dashboard desktop-screenshot.png --viewport-size=1280,720
npx playwright screenshot http://localhost:8080/dashboard mobile-screenshot.png --viewport-size=540,720
```

## Consejos:
- Usa una vista representativa de tu aplicación (el dashboard principal)
- Asegúrate de que no haya información sensible visible
- Los screenshots se mostrarán en el prompt de instalación de PWA
- Guarda ambos archivos en esta carpeta: `/public/images/screenshots/`

Una vez agregados los screenshots, los warnings desaparecerán y tendrás una "Richer PWA Install UI" con vista previa de la app.
