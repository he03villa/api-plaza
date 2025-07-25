<?php

namespace App\Dao;

use App\Models\Empleado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmpleadoDao
{
    public function getEmpleados()
    {
        return Empleado::all();
    }

    public function getEmpleado($id)
    {
        return Empleado::findOrFail($id);
    }

    public function createEmpleado(array $data)
    {
        return Empleado::create($data);
    }

    public function update($id, array $data)
    {
        $empleado = $this->getEmpleado($id);
        $empleado->update($data);
        return $empleado;
    }

    public function delete($id)
    {
        $empleado = $this->getEmpleado($id);
        $empleado->delete();
        return $empleado;
    }

    public function getEmpleadosWithFilter($filter)
    {
        $buscar = $filter['buscar'] ?? null;
        $limite = $filter['limite'] ?? 30;

        $empleados = Empleado::query();

        if ($buscar) {
            $empleados->where('nombres', 'like', '%' . $buscar . '%')
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

        $empleados = $empleados->orderBy('created_at', 'desc')->paginate($limite, $columns = ['*'], $pageName = 'page');
        $total = $empleados->lastPage();
        $currentPage = $empleados->currentPage();
        $response = [
            'empleados' => $empleados->items(),
            'total' => $total,
            'currentPage' => $currentPage
        ];
        return $response;
    }

    public function saveEmpleado(array $data)
    {
        try {
            DB::beginTransaction();
            $empleado = $this->createEmpleado($data);
            DB::commit();
            return ['success' => true, 'empleado' => $empleado, 'message' => 'Empleado successfully registered'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return ['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

    public function updateEmpleado($id, array $data) {
        try {
            DB::beginTransaction();
            $empleado = $this->update($id, $data);
            DB::commit();
            return ['success' => true, 'empleado' => $empleado, 'message' => 'Empleado successfully updated'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return ['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }
}