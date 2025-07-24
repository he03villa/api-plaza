<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'empresa_id',
    ];

    protected $casts = [
        'empresa_id' => 'integer',
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',  
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
