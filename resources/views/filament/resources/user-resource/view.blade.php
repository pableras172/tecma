<x-filament::section>
    <x-filament::grid :columns="2">
        <div class="mt-4">
            <x-filament::section.heading>Foto de perfil</x-filament::section.heading>
            @if ($record->foto)
                <img src="{{ Storage::disk('public')->url($record->foto) }}" alt="Foto" class="h-32 w-32 rounded-full object-cover mt-2">
            @else
                <p>Sin foto</p>
            @endif
        </div>
        <div>
            <x-filament::section.heading>Información personal</x-filament::section.heading>
            <p><strong>Nombre:</strong> {{ $record->name }}</p>
            <p><strong>Email:</strong> {{ $record->email }}</p>
            <p><strong>Teléfono:</strong> {{ $record->telefono }}</p>
            <p><strong>DNI:</strong> {{ $record->dni }}</p>
            <p><strong>Departamento:</strong> {{ $record->departamento->nombre ?? '-' }}</p>
            <p><strong>Categoría:</strong> {{ $record->categoriaProfesional->nombre ?? '-' }}</p>
        </div>

        <div>
            <x-filament::section.heading>Ubicación</x-filament::section.heading>
            <p><strong>País:</strong> {{ $record->city->province->country->name ?? '-' }}</p>
            <p><strong>Provincia:</strong> {{ $record->city->province->name ?? '-' }}</p>
            <p><strong>Ciudad:</strong> {{ $record->city->name ?? '-' }}</p>
            <p><strong>Dirección:</strong> {{ $record->direccion }}</p>
        </div>
    </x-filament::grid>
</x-filament::section>
