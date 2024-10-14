<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\User;
use App\Models\PetHistory;
use App\Models\PetHistoryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class PetHistoryController extends Controller
{
    // Middleware JWT para proteger las rutas
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
        $shistories = PetHistory::all(); // Obtener todas las historias

        return response()->json([
            'success' => true,
            'data' => $shistories
        ], 200);
    }

    // Listar todas las historias clínicas de una mascota específica
    public function allHistory($pet_id)
    {
        $pet = Pet::find($pet_id);

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Mascota no encontrada',
            ], 404);
        }

        $histories = PetHistory::with('files')->where('pet_id', $pet_id)->get();

        return response()->json([
            'success' => true,
            'data' => $histories,
        ], 200);
    }

    // Crear una nueva historia clínica para una mascota
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'history_code' => 'required|string|max:50',
            'history_date' => 'required|date',
            'history_time' => 'required',
            'history_reason' => 'required|string|max:100',
            'history_symptoms' => 'required|string|max:350',
            'history_diagnosis' => 'required|string|max:350',
            'history_treatment' => 'required|string|max:350',
            'user_id' => 'required|exists:users,id',
            'pet_id' => 'required|exists:pets,id',
            'files.*' => 'nullable|file|max:2048', // Soporta múltiples archivos con un tamaño máximo de 2MB cada uno
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Crear la nueva historia clínica
        $petHistory = PetHistory::create([
            'history_code' => $request->history_code,
            'history_date' => $request->history_date,
            'history_time' => $request->history_time,
            'history_reason' => $request->history_reason,
            'history_symptoms' => $request->history_symptoms,
            'history_diagnosis' => $request->history_diagnosis,
            'history_treatment' => $request->history_treatment,
            'user_id' => $request->user_id,
            'pet_id' => $request->pet_id,
        ]);

        // Guardar archivos asociados, si los hay
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('public/pet_history_files');
                $fileUrl = Storage::url($filePath);

                PetHistoryFile::create([
                    'pet_history_id' => $petHistory->id,
                    'file_path' => $fileUrl,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Historia clínica creada con éxito',
            'data' => $petHistory,
        ], 201);
    }

    // Obtener una historia clínica específica
    public function show($id)
    {
        $history = PetHistory::with('files')->find($id);

        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'Historia clínica no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $history,
        ], 200);
    }

    // Actualizar una historia clínica existente
    public function update(Request $request, $id)
    {
        $history = PetHistory::find($id);

        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'Historia clínica no encontrada',
            ], 404);
        }

        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'history_code' => 'required|string|max:50',
            'history_date' => 'required|date',
            'history_time' => 'required',
            'history_reason' => 'required|string|max:100',
            'history_symptoms' => 'required|string|max:350',
            'history_diagnosis' => 'required|string|max:350',
            'history_treatment' => 'required|string|max:350',
            'user_id' => 'required|exists:users,id',
            'pet_id' => 'required|exists:pets,id',
            'files.*' => 'nullable|file|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Actualizar los datos de la historia clínica
        $history->update([
            'history_code' => $request->history_code,
            'history_date' => $request->history_date,
            'history_time' => $request->history_time,
            'history_reason' => $request->history_reason,
            'history_symptoms' => $request->history_symptoms,
            'history_diagnosis' => $request->history_diagnosis,
            'history_treatment' => $request->history_treatment,
            'user_id' => $request->user_id,
            'pet_id' => $request->pet_id,
        ]);

        // Guardar archivos asociados, si los hay
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('public/pet_history_files');
                $fileUrl = Storage::url($filePath);

                PetHistoryFile::create([
                    'pet_history_id' => $history->id,
                    'file_path' => $fileUrl,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Historia clínica actualizada con éxito',
            'data' => $history,
        ], 200);
    }

    // Eliminar una historia clínica y sus archivos asociados
    public function destroy($id)
    {
        $history = PetHistory::with('files')->find($id);

        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'Historia clínica no encontrada',
            ], 404);
        }

        // Eliminar archivos asociados
        foreach ($history->files as $file) {
            Storage::delete(str_replace('/storage', 'public', $file->file_path));
            $file->delete();
        }

        // Eliminar la historia clínica
        $history->delete();

        return response()->json([
            'success' => true,
            'message' => 'Historia clínica y archivos asociados eliminados con éxito',
        ], 200);
    }

}