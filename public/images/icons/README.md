# Generación de Iconos PWA

Para completar la instalación de PWA, necesitas generar los iconos en diferentes tamaños.

## Opción 1: Usando una herramienta online (Recomendado)

1. Ve a: https://realfavicongenerator.net/ o https://www.pwabuilder.com/imageGenerator
2. Sube tu logo en alta resolución (mínimo 512x512px)
3. Descarga el paquete de iconos
4. Coloca los archivos en: `public/images/icons/`

## Opción 2: Usando ImageMagick (desde terminal)

Si tienes un logo llamado `logo.png` en alta resolución:

```bash
# Asegúrate de estar en el directorio del proyecto
cd public/images/icons/

# Genera todos los tamaños necesarios
convert /ruta/a/tu/logo.png -resize 72x72 icon-72x72.png
convert /ruta/a/tu/logo.png -resize 96x96 icon-96x96.png
convert /ruta/a/tu/logo.png -resize 128x128 icon-128x128.png
convert /ruta/a/tu/logo.png -resize 144x144 icon-144x144.png
convert /ruta/a/tu/logo.png -resize 152x152 icon-152x152.png
convert /ruta/a/tu/logo.png -resize 192x192 icon-192x192.png
convert /ruta/a/tu/logo.png -resize 384x384 icon-384x384.png
convert /ruta/a/tu/logo.png -resize 512x512 icon-512x512.png
```

## Iconos necesarios

- icon-72x72.png
- icon-96x96.png
- icon-128x128.png
- icon-144x144.png
- icon-152x152.png
- icon-192x192.png
- icon-384x384.png
- icon-512x512.png

## Verificación

Una vez generados los iconos, puedes verificar que PWA funciona:

1. Abre Chrome DevTools (F12)
2. Ve a la pestaña "Application" o "Aplicación"
3. En el menú lateral, selecciona "Manifest"
4. Deberías ver toda la información de tu PWA
