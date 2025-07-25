<?php

namespace App\Dao;

use App\Models\Empresa;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmpresaDao
{
    public function getAll()
    {
        return Empresa::all();
    }

    public function get($id)
    {
        return Empresa::findOrFail($id);
    }

    public function create(array $data)
    {
        return Empresa::create($data);
    }

    public function update($id, array $data)
    {
        $empresa = $this->get($id);
        $empresa->update($data);
        return $empresa;
    }

    public function delete($id)
    {
        $empresa = $this->get($id);
        $empresa->delete();
        return $empresa;
    }

    public function getAllWithFilter($filter) {
        $buscar = $filter['buscar'] ?? null;
        $limite = $filter['limite'] ?? 30;

        $empresas = Empresa::query();

        if ($buscar) {
            $empresas->where('razon_social', 'like', '%' . $buscar . '%')
                ->orWhere('nit', 'like', '%' . $buscar . '%')
                ->orWhere('direccion', 'like', '%' . $buscar . '%')
                ->orWhere('telefono', 'like', '%' . $buscar . '%')
                ->orWhere('correo', 'like', '%' . $buscar . '%');
        }
        
        $empresas = $empresas->orderBy('created_at', 'desc')->paginate($limite, $columns = ['*'], $pageName = 'page');
        $total = $empresas->lastPage();
        $currentPage = $empresas->currentPage();
        $response = [
            'empresas' => $empresas->items(),
            'total' => $total,
            'currentPage' => $currentPage
        ];
        return $response;
    }

    public function saveEmpresa (array $data) {
        try {
            DB::beginTransaction();
            $dataEmpresa = [
                'razon_social' => $data['razon_social'],
                'nit' => $data['nit'],
                'direccion' => $data['direccion'],
                'telefono' => $data['telefono'],
                'correo' => $data['correo'],
            ];
    
            $empresa = $this->create($dataEmpresa);
            $rutaImagen = ImageService::guardarImagen("empresas/$empresa->id", $data['logo'], 'logo');
    
            $empresa->update(['logo' => $rutaImagen]);
    
            DB::commit();
            return ['success' => true, 'empresa' => $empresa, 'message' => 'Empresa successfully registered'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return ['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

    public function updateEmpresa($id, array $data) {
        try {
            DB::beginTransaction();
            $empresa = $this->get($id);
            if (ImageService::validarBase64($data['logo'])) {
                $rutaImagen = ImageService::guardarImagen("empresas/$id", $data['logo'], 'logo');
                $data['logo'] = $rutaImagen;
            }
            $empresa->update($data);
            DB::commit();
            return ['success' => true, 'empresa' => $empresa, 'message' => 'Empresa successfully updated'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return ['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

    public function getAllEmpresas() {
        $empresas = Empresa::select('id', 'razon_social')->get();
        return $empresas;
    }
}