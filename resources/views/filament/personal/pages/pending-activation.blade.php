
<x-filament::page>
    <style>
        /* Ocultar sidebar y topbar */
        .fi-sidebar,
        .fi-topbar {
            display: none !important;
        }
        
        /* Centrar contenido */
        .fi-main {
            margin-left: 0 !important;
        }
    </style>

    <div class="flex min-h-screen items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md sm:max-w-lg text-center space-y-6 sm:space-y-8 p-6 sm:p-8">
            <div class="flex justify-center mb-4 sm:mb-6">
                <svg class="w-16 h-16 sm:w-20 sm:h-20 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white text-center">
                Cuenta pendiente de activación
            </h1>

            <p class="text-base sm:text-lg text-gray-600 dark:text-gray-400 text-center">
                Tu cuenta se ha creado correctamente, pero todavía no ha sido
                activada por un administrador.
            </p>

            <p class="text-sm text-gray-500 dark:text-gray-500 text-center">
                Recibirás acceso en cuanto sea validada. Te notificaremos por correo electrónico.
            </p>

            <div class="pt-4 sm:pt-6">
                <a href="{{ route('filament.personal.auth.login') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al login
                </a>
            </div>
        </div>
    </div>
</x-filament::page>
