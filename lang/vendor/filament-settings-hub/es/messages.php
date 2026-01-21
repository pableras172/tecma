<?php

return [
    'title' => 'Configuración',
    'group' => 'Configuración',
    'back' => 'Volver',
    'settings' => [
        'site' => [
            'title' => 'Configuración del Sitio',
            'description' => 'Administrar la configuración de tu sitio',
            'form' => [
                'site_name' => 'Nombre del Sitio',
                'site_description' => 'Descripción del Sitio',
                'site_logo' => 'Logo del Sitio',
                'site_profile' => 'Imagen de Perfil del Sitio',
                'site_keywords' => 'Palabras Clave del Sitio',
                'site_email' => 'Email del Sitio',
                'site_phone' => 'Teléfono del Sitio',
                'site_author' => 'Autor del Sitio',
            ],
            'site-map' => 'Generar Mapa del Sitio',
            'site-map-notification' => 'Mapa del Sitio Generado Exitosamente',
        ],
        'social' => [
            'title' => 'Menú Social',
            'description' => 'Administrar tu menú social',
            'form' => [
                'site_social' => 'Enlaces Sociales',
                'vendor' => 'Proveedor',
                'link' => 'Enlace',
            ],
        ],
    ],
];
