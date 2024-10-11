<?php

namespace App\Http\Controllers;

use App\Models\Specie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class SpecieController extends Controller
{
    // Aplicar middleware para requerir autenticación mediante JWT
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Obtener todas las especies.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $species = Specie::all(); // Obtener todas las especies

        return response()->json([
            'success' => true,
            'data' => $species
        ], 200);
    }

    /**
     * Obtener una especie específica por ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $specie = Specie::find($id); // Buscar especie por ID

        if (!$specie) {
            return response()->json([
                'success' => false,
                'message' => 'Especie no encontrada.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $specie
        ], 200);
    }

    /**
     * Crear una nueva especie.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'specieName' => 'required|string|max:20|unique:species,specieName',
        ], [
            'specieName.unique' => 'El nombre ya está registrado.',
            'specieName.required' => 'El nombre es obligatorio.',
            'specieName.max' => 'El nombre no puede exceder los 20 caracteres.',
        ]);

        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear nueva especie
        $specie = Specie::create([
            'specieName' => $request->specieName,
        ]);

        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Especie registrada correctamente.',
            'specie' => [
                'id' => $specie->id,
                'specieName' => $specie->specieName,
                'created_at' => $specie->created_at,
            ]
        ], 201);
    }

    /**
     * Actualizar una especie existente.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validar datos entrantes
        $validator = Validator::make($request->all(), [
            'specieName' => 'required|string|max:20|unique:species,specieName,' . $id,
        ], [
            'specieName.unique' => 'El nombre ya está registrado.',
            'specieName.required' => 'El nombre es obligatorio.',
            'specieName.max' => 'El nombre no puede exceder los 20 caracteres.',
        ]);

        // Si la validación falla, devuelve los errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar especie por ID
        $specie = Specie::find($id);

        if (!$specie) {
            return response()->json([
                'success' => false,
                'message' => 'Especie no encontrada.'
            ], 404);
        }

        // Actualizar los datos de la especie
        $specie->specieName = $request->specieName;

        // Guardar cambios
        $specie->save();

        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Especie actualizada correctamente.',
            'data' => $specie
        ], 200);
    }

    /**
     * Eliminar una especie.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $specie = Specie::find($id);

        if (!$specie) {
            return response()->json([
                'success' => false,
                'message' => 'Especie no encontrada.'
            ], 404);
        }

        // Verificar si existen razas asociadas a esta especie
        if ($specie->breeds()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la especie porque tiene razas asociadas.'
            ], 400);  // Código de error 400: Petición incorrecta
        }

        $specie->delete();

        return response()->json([
            'success' => true,
            'message' => 'Especie eliminada correctamente.'
        ], 200);
    }
 
}