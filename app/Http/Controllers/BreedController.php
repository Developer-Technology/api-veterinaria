<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class BreedController extends Controller
{
    // Middleware JWT para proteger las rutas
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // Listar todas las razas con sus respectivas especies
    public function index()
    {
        $breeds = Breed::with('species')->get();

        // Mapear los resultados para formatearlos según se requiere
        $formattedBreeds = $breeds->map(function ($breed) {
            return [
                'id' => $breed->id,
                'breedName' => $breed->breedName,
                'species_id' => $breed->species_id,
                'specieName' => $breed->species ? $breed->species->specieName : null,
                'created_at' => $breed->created_at,
                'updated_at' => $breed->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedBreeds
        ], 200);
    }

    // Obtener una raza específica por su ID
    public function show($id)
    {
        $breed = Breed::with('species')->find($id);

        if (!$breed) {
            return response()->json([
                'success' => false,
                'message' => 'Raza no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $breed
        ], 200);
    }

    // Crear una nueva raza
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'breedName' => 'required|string|max:100|unique:breeds,breedName',
            'species_id' => 'required|exists:species,id',
        ], [
            'breedName.required' => 'El nombre es obligatorio.',
            'breedName.unique' => 'El nombre ya está registrado.',
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

        $breed = Breed::create([
            'breedName' => $request->breedName,
            'species_id' => $request->species_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Raza creada con éxito',
            'breed' => $breed
        ], 201);
    }

    // Actualizar una raza existente
    public function update(Request $request, $id)
    {
        $breed = Breed::find($id);

        if (!$breed) {
            return response()->json([
                'success' => false,
                'message' => 'Raza no encontrada',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'breedName' => 'required|string|max:100|unique:breeds,breedName,' . $id,
            'species_id' => 'required|exists:species,id',
        ], [
            'breedName.required' => 'El nombre es obligatorio.',
            'breedName.unique' => 'El nombre ya está registrado.',
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

        $breed->update($request->only(['breedName', 'species_id']));

        return response()->json([
            'success' => true,
            'message' => 'Raza actualizada con éxito',
            'data' => $breed
        ], 200);
    }

    // Eliminar una raza
    public function destroy($id)
    {
        $breed = Breed::find($id);

        if (!$breed) {
            return response()->json([
                'success' => false,
                'message' => 'Raza no encontrada',
            ], 404);
        }

        $breed->delete();

        return response()->json([
            'success' => true,
            'message' => 'Raza eliminada con éxito',
        ], 200);
    }

}