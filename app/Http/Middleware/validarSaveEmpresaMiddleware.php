<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class validarSaveEmpresaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make($request->all(), [
            'razon_social' => 'required|string',
            'nit' => 'required|string|unique:empresas',
            'direccion' => 'nullable|string',
            'correo' => 'nullable|email|unique:empresas',
            'telefono' => 'nullable|string',
            'logo' => 'nullable|string',
        ], [
            'required' => 'El :attribute es requerido.',
            'email' => 'El :attribute debe ser un correo válido.',
            'unique' => 'El :attribute ya está registrado.',
            'string' => 'El :attribute debe ser una cadena de texto.',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        return $next($request);
    }
}
