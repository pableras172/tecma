<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecuenciaParte extends Model
{
    use HasFactory;

    protected $table = 'secuencias_parte';

    protected $fillable = [
        'codigo',
        'anio',
        'secuencia',
    ];

    /**
     * Obtiene la siguiente secuencia para un código y año dado
     */
    public static function obtenerSiguiente(string $codigo, int $anio): int
    {
        $secuencia = self::firstOrCreate(
            ['codigo' => $codigo, 'anio' => $anio],
            ['secuencia' => 0]
        );

        return $secuencia->secuencia + 1;
    }

    /**
     * Incrementa la secuencia para un código y año dado
     */
    public static function incrementar(string $codigo, int $anio): void
    {
        $secuencia = self::firstOrCreate(
            ['codigo' => $codigo, 'anio' => $anio],
            ['secuencia' => 0]
        );

        $secuencia->increment('secuencia');
    }

    /**
     * Genera el número de parte completo
     */
    public static function generarNumero(string $codigo, int $anio, int $secuencia): string
    {
        return sprintf('%s-%d-%04d', strtoupper($codigo), $anio, $secuencia);
    }
}
