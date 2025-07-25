<?php

namespace App\Http\Controllers;

use App\Dao\EmpresaDao;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    protected $_empresaDao;

    public function __construct(EmpresaDao $empresaDao) {
        $this->_empresaDao = $empresaDao;
    }

    public function index(Request $request) {
        $filter = [
            "buscar" => $request->get('buscar') ?? null,
            "limite" => $request->get('limit') ?? 30
        ];
        $empresas = $this->_empresaDao->getAllWithFilter($filter);
        return response()->json($empresas, 200);
    }

    public function show($id) {
        $empresa = $this->_empresaDao->get($id);
        return response()->json($empresa, 200);
    }

    public function store(Request $request) {
        $data = $request->all();
        $empresa = $this->_empresaDao->saveEmpresa($data);
        if (!$empresa['success']) {
            return response()->json($empresa, 400);
        }
        return response()->json($empresa, 201);
    }

    public function update(Request $request, $id) {
        $data = $request->all();
        $empresa = $this->_empresaDao->updateEmpresa($id, $data);
        if (!$empresa['success']) {
            return response()->json($empresa, 400);
        }
        return response()->json($empresa, 200);
    }

    public function destroy($id) {
        $empresa = $this->_empresaDao->delete($id);
        return response()->json($empresa, 200);
    }

    public function empresas() {
        $empresas = $this->_empresaDao->getAllEmpresas();
        return response()->json($empresas, 200);
    }
}
