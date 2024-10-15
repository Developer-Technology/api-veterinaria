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
        // Cargar las historias con las relaciones de la mascota y el usuario
        $shistories = PetHistory::with(['pet', 'user'])->get();

        // Formatear los datos para incluir los nombres de la mascota y del usuario
        $formattedHistories = $shistories->map(function ($history) {
            return [
                'id' => $history->id,
                'history_code' => $history->history_code,
                'history_date' => $history->history_date,
                'history_time' => $history->history_time,
                'history_reason' => $history->history_reason,
                'history_symptoms' => $history->history_symptoms,
                'history_diagnosis' => $history->history_diagnosis,
                'history_treatment' => $history->history_treatment,
                'userName' => $history->user ? $history->user->name : null, // Nombre del usuario
                'petName' => $history->pet ? $history->pet->petName : null, // Nombre de la mascota
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedHistories
        ], 200);
    }

    // Listar todas las historias clínicas de una mascota específica
    public function allHistory($pet_id)
    {
        // Buscar la mascota por ID
        $pet = Pet::find($pet_id);

        // Verificar si la mascota existe
        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Mascota no encontrada',
            ], 404);
        }

        // Cargar las historias relacionadas con la mascota y el usuario
        $shistories = PetHistory::with(['pet', 'user'])
            ->where('pet_id', $pet_id)  // Filtrar por el ID de la mascota
            ->get();

        // Formatear los datos para incluir los nombres de la mascota y del usuario
        $formattedHistories = $shistories->map(function ($history) {
            return [
                'id' => $history->id,
                'history_code' => $history->history_code,
                'history_date' => $history->history_date,
                'history_time' => $history->history_time,
                'history_reason' => $history->history_reason,
                'history_symptoms' => $history->history_symptoms,
                'history_diagnosis' => $history->history_diagnosis,
                'history_treatment' => $history->history_treatment,
                'userName' => $history->user ? $history->user->name : null, // Nombre del usuario
                'petName' => $history->pet ? $history->pet->petName : null, // Nombre de la mascota
                'files' => $history->files->map(function($file) {
                    return [
                        'id' => $file->id,
                        'file_path' => $file->file_path,
                        'file_type' => $file->file_type,
                    ];
                }) // Incluir los archivos asociados
            ];
        });

        // Devolver las historias formateadas
        return response()->json([
            'success' => true,
            'data' => $formattedHistories,
        ], 200);
    }

    // Crear una nueva historia clínica para una mascota
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'history_date' => 'required|date',
            'history_time' => 'required',
            'history_reason' => 'required|string|max:100',
            'history_symptoms' => 'required|string|max:350',
            'history_diagnosis' => 'required|string|max:350',
            'history_treatment' => 'required|string|max:350',
            'user_id' => 'required|exists:users,id',
            'pet_id' => 'required|exists:pets,id',
            'files.*' => 'nullable|file|max:2048', // Soporta múltiples archivos con un tamaño máximo de 2MB cada uno
        ], [
            'history_date.required' => 'La fecha es obligatoria.',
            'history_time.required' => 'La hora es obligatoria.',
            'history_reason.required' => 'El motivo es obligatorio.',
            'history_symptoms.required' => 'El síntoma es obligatorio.',
            'history_diagnosis.required' => 'El diagnóstico es obligatorio.',
            'history_treatment.required' => 'El tratamiento es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Generar el código de la historia clínica automáticamente
        $lastHistory = PetHistory::orderBy('id', 'desc')->first();
        $nextCode = $this->generateNextHistoryCode($lastHistory);

        // Crear la nueva historia clínica
        $petHistory = PetHistory::create([
            'history_code' => $nextCode,  // Código generado automáticamente
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

    // Función para generar el siguiente código de historia
    private function generateNextHistoryCode($lastHistory)
    {
        // Si no existe una historia previa, generar el primer código
        if (!$lastHistory) {
            return 'HM-00001';
        }

        // Extraer el número del último código
        $lastCode = $lastHistory->history_code;
        $lastNumber = (int)substr($lastCode, 3);  // Extraer la parte numérica del código (por ejemplo: 00001)
        
        // Incrementar el número
        $nextNumber = $lastNumber + 1;
        
        // Generar el nuevo código con el formato "HM-XXXXX"
        return 'HM-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
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