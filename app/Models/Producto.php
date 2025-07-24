<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
        'precio',
        'descuento',
        'empresa_id',
    ];

    protected $casts = [
        'precio' => 'float',
        'descuento' => 'float',
        'empresa_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function getTotalPrecioAttribute()
    {
        return $this->precio - ($this->precio * ($this->descuento / 100));
    }

    public function getTotalDescuentoAttribute()
    {
        return $this->precio * ($this->descuento / 100);
    }

    public function getImagenUrlAttribute()
    {
        return asset('storage/' . $this->imagen);
    }
}
