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

    // Crear un nuevo registro de historial de vacuna
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vaccine_id' => 'required|exists:vaccines,id',
            'historiaFecha' => 'required|date',
            'historiaProducto' => 'required|string|max:150',
            'historiaObservacion' => 'nullable|string|max:150',
            'pet_id' => 'required|exists:pets,id',
        ], [
            'vaccine_id.required' => 'La vacuna es obligatoria.',
            'vaccine_id.exists' => 'La vacuna no está registrada.',
            'historiaFecha.required' => 'La fecha es obligatoria.',
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
            'vaccine_id', 'historiaFecha', 'historiaProducto', 'historiaObservacion', 'pet_id'
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
            'historiaFecha' => 'required|date',
            'historiaProducto' => 'required|string|max:150',
            'historiaObservacion' => 'nullable|string|max:150',
            'pet_id' => 'required|exists:pets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $vaccineHistory->update($request->only([
            'vaccine_id', 'historiaFecha', 'historiaProducto', 'historiaObservacion', 'pet_id'
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