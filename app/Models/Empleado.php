<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $fillable = [
        'nombres',
        'apellidos',
        'correo',
        'telefono',
        'empresa_id',
        'area_id',
        'user_id',
    ];

    protected $casts = [
        'empresa_id' => 'integer',
        'area_id' => 'integer',
        'user_id' => 'integer',
        'nombres' => 'string',
        'apellidos' => 'string',
        'correo' => 'string',
        'telefono' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
