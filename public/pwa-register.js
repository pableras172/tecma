// Registrar Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then((registration) => {
        console.log('Service Worker registrado con éxito:', registration.scope);
      })
      .catch((error) => {
        console.log('Error al registrar Service Worker:', error);
      });
  });
}

// Detectar si la app puede instalarse
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
  // Prevenir que Chrome 67 y anteriores muestren el prompt automáticamente
  e.preventDefault();
  // Guardar el evento para usarlo después
  deferredPrompt = e;
  
  // Mostrar botón de instalación si existe
  const installButton = document.getElementById('pwa-install-button');
  if (installButton) {
    installButton.style.display = 'block';
    
    installButton.addEventListener('click', () => {
      // Ocultar el botón
      installButton.style.display = 'none';
      // Mostrar el prompt
      deferredPrompt.prompt();
      // Esperar a que el usuario responda
      deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
          console.log('Usuario aceptó la instalación');
        } else {
          console.log('Usuario rechazó la instalación');
        }
        deferredPrompt = null;
      });
    });
  }
});

// Detectar cuando la app se ha instalado
window.addEventListener('appinstalled', () => {
  console.log('PWA instalada con éxito');
});
