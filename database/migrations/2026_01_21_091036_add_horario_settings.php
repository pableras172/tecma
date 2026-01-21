<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('horarios.hora_entrada', '08:00');
        $this->migrator->add('horarios.hora_salida', '17:00');        
    }
};
