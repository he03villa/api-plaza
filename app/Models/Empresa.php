<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = [
        'razao_social',
        'nit',
        'direccion',
        'telefono',
        'correo',
        'logo',
    ];

    protected $casts = [
        'logo' => 'string',
        'nit' => 'string',
        'telefono' => 'string',
        'correo' => 'string',
        'direccion' => 'string',
        'razao_social' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }
}
