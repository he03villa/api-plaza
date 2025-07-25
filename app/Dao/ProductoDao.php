<?php

namespace App\Dao;

use App\Models\Producto;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductoDao
{
    public function getAllProductos()
    {
        return Producto::all();
    }

    public function getProducto($id)
    {
        return Producto::findOrFail($id);
    }

    public function createProducto(array $data)
    {
        return Producto::create($data);
    }

    public function update($id, array $data)
    {
        $producto = $this->getProducto($id);
        $producto->update($data);
        return $producto;
    }

    public function delete($id)
    {
        $producto = $this->getProducto($id);
        $producto->delete();
        return $producto;
    }

    public function getProductosWithFilter($filter)
    {
        $buscar = $filter['buscar'] ?? null;
        $limite = $filter['limite'] ?? 30;

        $productos = Producto::query();

        if ($buscar) {
            $productos->where('nombres', 'like', '%' . $buscar . '%')
                ->orWhere('apellidos', 'like', '%' . $buscar . '%')
                ->orWhere('correo', 'like', '%' . $buscar . '%')
                ->orWhereHas('empresa', function ($query) use ($buscar) {
                    $query->where('razon_social', 'like', '%' . $buscar . '%')
                        ->orWhere('nit', 'like', '%' . $buscar . '%');
                })
                ->orWhereHas('area', function ($query) use ($buscar) {
                    $query->where('nombre', 'like', '%' . $buscar . '%');
                });
        }

        $productos = $productos->orderBy('created_at', 'desc')->paginate($limite, $columns = ['*'], $pageName = 'page');
        $total = $productos->lastPage();
        $currentPage = $productos->currentPage();
        $response = [
            'productos' => $productos->items(),
            'total' => $total,
            'currentPage' => $currentPage
        ];
        return $response;
    }

    public function saveProducto(array $data)
    {
        try {
            DB::beginTransaction();
            $dataProducto = [
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'precio' => $data['precio'],
                'descuento' => $data['descuento'],
            ];
            $producto = $this->createProducto($data);
            $rutaImagen = ImageService::guardarImagen("productos/$producto->id", $data['foto'], 'foto');
            $producto->update(['imagen' => $rutaImagen]);
            DB::commit();
            return ['success' => true, 'producto' => $producto, 'message' => 'Producto successfully registered'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return ['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

    public function updateProducto($id, array $data)
    {
        try {
            DB::beginTransaction();
            if (ImageService::validarBase64($data['foto'])) {
                $rutaImagen = ImageService::guardarImagen("empresas/$id", $data['foto'], 'foto');
                $data['foto'] = $rutaImagen;
            }
            $producto = $this->update($id, $data);
            DB::commit();
            return ['success' => true, 'producto' => $producto, 'message' => 'Producto successfully updated'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return ['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }
}