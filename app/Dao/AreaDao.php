<?php

namespace App\Dao;

use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AreaDao
{
    public function getAreas()
    {
        return Area::all();
    }

    public function getArea($id)
    {
        return Area::findOrFail($id);
    }

    public function createArea(array $data)
    {
        return Area::create($data);
    }

    public function update($id, array $data)
    {
        $area = $this->getArea($id);
        $area->update($data);
        return $area;
    }

    public function delete($id)
    {
        $area = $this->getArea($id);
        $area->delete();
        return $area;
    }

    public function getAreasWithFilter($filter)
    {
        $buscar = $filter['buscar'] ?? null;
        $limite = $filter['limite'] ?? 30;

        $areas = Area::query();

        if ($buscar) {
            $areas->where('nombre', 'like', '%' . $buscar . '%')
                ->orWhere('id', 'like', '%' . $buscar . '%');
        }

        $areas = $areas->orderBy('created_at', 'desc')->paginate($limite, $columns = ['*'], $pageName = 'page');
        $total = $areas->lastPage();
        $currentPage = $areas->currentPage();
        $response = [
            'areas' => $areas->items(),
            'total' => $total,
            'currentPage' => $currentPage
        ];
        return $response;
    }

    public function saveArea(array $data) {
        try {
            DB::beginTransaction();
            $area = $this->createArea($data);
            DB::commit();
            return ['success' => true, 'area' => $area, 'message' => 'Area successfully registered'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return ['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

    public function updateArea($id, array $data) {
        try {
            DB::beginTransaction();
            $area = $this->update($id, $data);
            DB::commit();
            return ['success' => true, 'area' => $area, 'message' => 'Area successfully updated'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('error', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return ['success' => false, 'message' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

    public function getAllAreas() {
        $areas = Area::select('id', 'nombre')->get();
        return $areas;
    }
}