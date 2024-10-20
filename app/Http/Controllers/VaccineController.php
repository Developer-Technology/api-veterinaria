<?php

namespace App\Http\Controllers;

use App\Models\Vaccine;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class VaccineController extends Controller
{
    // Middleware JWT para proteger las rutas
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // Listar todas las vacunas con sus respectivas especies
    public function index(Request $request)
    {
        // Verificar si se envía el parámetro species_id
        $speciesId = $request->input('species_id');

        if ($speciesId) {
            // Filtrar las vacunas por el species_id recibido
            $vaccines = Vaccine::with('species')->where('species_id', $speciesId)->get();
        } else {
            // Obtener todas las vacunas si no se envía el parámetro
            $vaccines = Vaccine::with('species')->get();
        }

        // Mapear los resultados para formatearlos según se requiere
        $formattedVaccines = $vaccines->map(function ($vaccine) {
            return [
                'id' => $vaccine->id,
                'vaccineName' => $vaccine->vaccineName,
                'species_id' => $vaccine->species_id,
                'specieName' => $vaccine->species ? $vaccine->species->specieName : null,
                'created_at' => $vaccine->created_at,
                'updated_at' => $vaccine->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedVaccines
        ], 200);
    }

    // Obtener una vacuna específica por su ID
    public function show($id)
    {
        $vaccine = Vaccine::with('species')->find($id);

        if (!$vaccine) {
            return response()->json([
                'success' => false,
                'message' => 'Vacuna no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $vaccine
        ], 200);
    }

    // Crear una nueva vacuna
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vaccineName' => 'required|string|max:150|unique:vaccines,vaccineName',
            'species_id' => 'required|exists:species,id',
        ], [
            'vaccineName.required' => 'El nombre es obligatorio.',
            'vaccineName.unique' => 'El nombre ya está registrado.',
            'species_id.required' => 'La especie es obligatoria.',
            'species_id.exists' => 'La especie no está registrada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $vaccine = Vaccine::create([
            'vaccineName' => $request->vaccineName,
            'species_id' => $request->species_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vacuna creada con éxito',
            'vaccine' => $vaccine
        ], 201);
    }

    // Actualizar una vacuna existente
    public function update(Request $request, $id)
    {
        $vaccine = Vaccine::find($id);

        if (!$vaccine) {
            return response()->json([
                'success' => false,
                'message' => 'Vacuna no encontrada',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'vaccineName' => 'required|string|max:150|unique:vaccines,vaccineName,' . $id,
            'species_id' => 'required|exists:species,id',
        ], [
            'vaccineName.required' => 'El nombre es obligatorio.',
            'vaccineName.unique' => 'El nombre ya está registrado.',
            'species_id.required' => 'La especie es obligatoria.',
            'species_id.exists' => 'La especie no está registrada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $vaccine->update($request->only(['vaccineName', 'species_id']));

        return response()->json([
            'success' => true,
            'message' => 'Vacuna actualizada con éxito',
            'data' => $vaccine
        ], 200);
    }

    // Eliminar una vacuna
    public function destroy($id)
    {
        $vaccine = Vaccine::find($id);

        if (!$vaccine) {
            return response()->json([
                'success' => false,
                'message' => 'Vacuna no encontrada',
            ], 404);
        }

        $vaccine->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vacuna eliminada con éxito',
        ], 200);
    }

}