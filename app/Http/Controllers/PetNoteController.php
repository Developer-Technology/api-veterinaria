<?php

namespace App\Http\Controllers;

use App\Models\PetNote;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class PetNoteController extends Controller
{
    // Aplicar middleware para requerir autenticación mediante JWT
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Obtener todas las notas de mascotas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $petNotes = PetNote::with('pet')->get(); // Obtener todas las notas con sus respectivas mascotas

        return response()->json([
            'success' => true,
            'data' => $petNotes
        ], 200);
    }

    /**
     * Obtener una nota de mascota específica por ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $petNote = PetNote::with('pet')->find($id); // Buscar la nota de mascota por ID

        if (!$petNote) {
            return response()->json([
                'success' => false,
                'message' => 'Nota de mascota no encontrada.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $petNote
        ], 200);
    }

    /**
     * Crear una nueva nota de mascota.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'pet_id' => 'required|exists:pets,id',
            'noteDescription' => 'required|string|max:140',
            'noteDate' => 'required|date',
        ], [
            'pet_id.required' => 'El ID de la mascota es obligatorio.',
            'pet_id.exists' => 'La mascota no está registrada.',
            'noteDescription.required' => 'La descripción es obligatoria.',
            'noteDescription.max' => 'La descripción no puede exceder los 140 caracteres.',
            'noteDate.required' => 'La fecha es obligatoria.',
            'noteDate.date' => 'La fecha debe ser válida.',
        ]);

        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear nueva nota de mascota
        $petNote = PetNote::create([
            'pet_id' => $request->pet_id,
            'noteDescription' => $request->noteDescription,
            'noteDate' => $request->noteDate,
        ]);

        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Nota de mascota registrada correctamente.',
            'data' => $petNote
        ], 201);
    }

    /**
     * Actualizar una nota de mascota existente.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validar datos entrantes
        $validator = Validator::make($request->all(), [
            'noteDescription' => 'required|string|max:140'
        ], [
            'noteDescription.required' => 'La descripción es obligatoria.',
            'noteDescription.max' => 'La descripción no puede exceder los 140 caracteres.'
        ]);

        // Si la validación falla, devuelve los errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar nota de mascota por ID
        $petNote = PetNote::find($id);

        if (!$petNote) {
            return response()->json([
                'success' => false,
                'message' => 'Nota de mascota no encontrada.'
            ], 404);
        }

        // Actualizar los datos de la nota de mascota
        $petNote->update([
            'noteDescription' => $request->noteDescription
        ]);

        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Nota de mascota actualizada correctamente.',
            'data' => $petNote
        ], 200);
    }

    /**
     * Eliminar una nota de mascota.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $petNote = PetNote::find($id);

        if (!$petNote) {
            return response()->json([
                'success' => false,
                'message' => 'Nota de mascota no encontrada.'
            ], 404);
        }

        // Eliminar la nota de mascota
        $petNote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nota de mascota eliminada correctamente.'
        ], 200);
    }

    // Eliminar todas las notas asociadas a una mascota por su pet_id
    public function destroyByPetId($petId)
    {
        // Buscar todas las notas asociadas con el pet_id
        $vaccineHistories = PetNote::where('pet_id', $petId)->get();

        if ($vaccineHistories->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron historiales de notas para esta mascota',
            ], 404);
        }

        // Eliminar cada vacuna encontrada
        foreach ($vaccineHistories as $vaccineHistory) {
            $vaccineHistory->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Todos los historiales de notas eliminados con éxito',
        ], 200);
    }

}