<?php

namespace App\Http\Controllers;

use App\Models\PetHistory;
use App\Models\PetHistoryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetHistoryFileController extends Controller
{
    // Middleware JWT para proteger las rutas
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // Listar todos los archivos de una historia clínica específica
    public function index($history_id)
    {
        $history = PetHistory::find($history_id);

        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'Historia clínica no encontrada',
            ], 404);
        }

        $files = PetHistoryFile::where('pet_history_id', $history_id)->get();

        return response()->json([
            'success' => true,
            'data' => $files,
        ], 200);
    }

    // Subir un archivo para una historia clínica
    public function store(Request $request, $history_id)
    {
        $history = PetHistory::find($history_id);

        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'Historia clínica no encontrada',
            ], 404);
        }

        // Validar que se esté subiendo un archivo
        $request->validate([
            'file' => 'required|file|max:2048', // Tamaño máximo de 2MB por archivo
        ]);

        // Subir el archivo
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('public/pet_history_files');
            $fileUrl = Storage::url($filePath);

            // Guardar el archivo en la base de datos
            $petHistoryFile = PetHistoryFile::create([
                'pet_history_id' => $history->id,
                'file_path' => $fileUrl,
                'file_type' => $file->getClientOriginalExtension(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Archivo subido con éxito',
                'data' => $petHistoryFile,
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo subir el archivo',
        ], 400);
    }

    // Eliminar un archivo específico de una historia clínica
    public function destroy($file_id)
    {
        $file = PetHistoryFile::find($file_id);

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo no encontrado',
            ], 404);
        }

        // Eliminar el archivo del almacenamiento
        Storage::delete(str_replace('/storage', 'public', $file->file_path));

        // Eliminar el registro del archivo de la base de datos
        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'Archivo eliminado con éxito',
        ], 200);
    }

}