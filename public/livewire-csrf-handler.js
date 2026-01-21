// Detectar y manejar errores 419 (CSRF Token Expired)
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
            if (status === 419) {
                preventDefault();
                
                // Mostrar notificación al usuario
                if (confirm('Tu sesión ha expirado. ¿Deseas recargar la página?')) {
                    window.location.reload();
                }
            }
        });
    });
});
