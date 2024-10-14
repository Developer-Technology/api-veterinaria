<?php

namespace App\Http\Controllers;

use App\Models\VaccineHistory;
use App\Models\Pet;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Validator;

class VaccineHistoryController extends Controller
{
    // Middleware JWT para proteger las rutas
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // Listar todo el historial de vacunas con las mascotas y las vacunas relacionadas
    public function index()
    {
        $vaccineHistories = VaccineHistory::with(['pet', 'vaccine'])->get();

        $formattedHistories = $vaccineHistories->map(function ($history) {
            return [
                'id' => $history->id,
                'vaccine_id' => $history->vaccine_id,
                'vaccineName' => $history->vaccine ? $history->vaccine->name : null,
                'historiaFecha' => $history->historiaFecha,
                'historiaProducto' => $history->historiaProducto,
                'historiaObservacion' => $history->historiaObservacion,
                'pet_id' => $history->pet_id,
                'petName' => $history->pet ? $history->pet->petName : null,
                'created_at' => $history->created_at,
                'updated_at' => $history->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedHistories
        ], 200);
    }

    // Obtener un historial de vacuna por ID
    public function show($id)
    {
        $vaccineHistory = VaccineHistory::with(['pet', 'vaccine'])->find($id);

        if (!$vaccineHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Historial de vacuna no encontrado',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $vaccineHistory
        ], 200);
    }

    // Obtener el historial de vacunas por el ID de la mascota (pet_id)
    public function showPet($petId)
    {
        // Buscar todos los historiales de vacunas relacionados con la mascota por pet_id
        $vaccineHistory = VaccineHistory::with('vaccine')
                            ->where('pet_id', $petId)
                            ->get();

        // Mapeamos el historial para combinar los datos del historial y la vacuna
        $combinedData = $vaccineHistory->map(function($history) {
            return [
                'id' => $history->id,
                'vaccineDate' => $history->vaccine_date,  // Asegúrate que este nombre coincida con tu columna
                'productUsed' => $history->product,  // Asegúrate que este nombre coincida con tu columna
                'observations' => $history->observation,  // Asegúrate que este nombre coincida con tu columna
                'petId' => $history->pet_id,  // Aquí corregí el error de sintaxis
                'vaccineId' => $history->vaccine_id,
                'vaccineName' => $history->vaccine->vaccineName,
                'created_at' => $history->created_at,
                'updated_at' => $history->updated_at
            ];
        });

        // Retornar el historial de vacunas de la mascota
        return response()->json([
            'success' => true,
            'data' => $combinedData
        ], 200);
    }


    // Crear un nuevo registro de historial de vacuna
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vaccine_id' => 'required|exists:vaccines,id',
            'vaccine_date' => 'required|date',
            'product' => 'required|string|max:150',
            'observation' => 'nullable|string|max:150',
            'pet_id' => 'required|exists:pets,id',
        ], [
            'vaccine_id.required' => 'La vacuna es obligatoria.',
            'vaccine_id.exists' => 'La vacuna no está registrada.',
            'vaccine_date.required' => 'La fecha es obligatoria.',
            'product.required' => 'El producto es obligatorio.',
            'pet_id.required' => 'La mascota es obligatoria.',
            'pet_id.exists' => 'La mascota no está registrada.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $vaccineHistory = VaccineHistory::create($request->only([
            'vaccine_id', 'vaccine_date', 'product', 'observation', 'pet_id'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Historial de vacuna creado con éxito',
            'data' => $vaccineHistory
        ], 201);
    }

    // Actualizar un historial de vacuna existente
    public function update(Request $request, $id)
    {
        $vaccineHistory = VaccineHistory::find($id);

        if (!$vaccineHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Historial de vacuna no encontrado',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'vaccine_id' => 'required|exists:vaccines,id',
            'vaccine_date' => 'required|date',
            'product' => 'required|string|max:150',
            'observation' => 'nullable|string|max:150'
        ], [
            'vaccine_id.required' => 'La vacuna es obligatoria.',
            'vaccine_id.exists' => 'La vacuna no está registrada.',
            'vaccine_date.required' => 'La fecha es obligatoria.',
            'product.required' => 'El producto es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $vaccineHistory->update($request->only([
            'vaccine_id', 'vaccine_date', 'product', 'observation'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Historial de vacuna actualizado con éxito',
            'data' => $vaccineHistory
        ], 200);
    }

    // Eliminar un historial de vacuna
    public function destroy($id)
    {
        $vaccineHistory = VaccineHistory::find($id);

        if (!$vaccineHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Historial de vacuna no encontrado',
            ], 404);
        }

        $vaccineHistory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Historial de vacuna eliminado con éxito',
        ], 200);
    }

}