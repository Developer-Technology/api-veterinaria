<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Breed;
use App\Models\Specie;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class PetController extends Controller
{
    // Middleware JWT para proteger las rutas
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // Listar todas las mascotas con sus respectivas especies, razas, y clientes
    public function index()
    {
        $pets = Pet::with(['species', 'breed', 'client'])->get();

        // Mapear los resultados para formatearlos según se requiere
        $formattedPets = $pets->map(function ($pet) {
            return [
                'id' => $pet->id,
                'petCode' => $pet->petCode,
                'petName' => $pet->petName,
                'petBirthDate' => $pet->petBirthDate,
                'petWeight' => $pet->petWeight,
                'petColor' => $pet->petColor,
                'species_id' => $pet->species_id,
                'specieName' => $pet->species ? $pet->species->specieName : null,
                'breeds_id' => $pet->breeds_id,
                'breedName' => $pet->breed ? $pet->breed->breedName : null,
                'clientName' => $pet->client ? $pet->client->clientName : null,
                'petGender' => $pet->petGender,
                'petPhoto' => $pet->petPhoto,
                'petAdditional' => $pet->petAdditional,
                'created_at' => $pet->created_at,
                'updated_at' => $pet->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedPets
        ], 200);
    }

    // Obtener una mascota específica por su ID
    public function show($id)
    {
        $pet = Pet::with(['species', 'breed', 'client'])->find($id);

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Mascota no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pet
        ], 200);
    }

    // Crear una nueva mascota
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'petCode' => 'required|string|max:50|unique:pets,petCode',
            'petName' => 'required|string|max:100',
            'petBirthDate' => 'required|date',
            'petWeight' => 'nullable|string|max:10',
            'petColor' => 'nullable|string|max:100',
            'species_id' => 'required|exists:species,id',
            'breeds_id' => 'required|exists:breeds,id',
            'petGender' => 'required|string|max:10',
            'clients_id' => 'required|exists:clients,id',
            'petPhoto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'petCode.required' => 'El código de la mascota es obligatorio.',
            'petName.required' => 'El nombre de la mascota es obligatorio.',
            'petBirthDate.required' => 'La fecha de nacimiento de la mascota es obligatoria.',
            'species_id.required' => 'La especie es obligatoria.',
            'species_id.exists' => 'La especie no está registrada.',
            'breeds_id.required' => 'La raza es obligatoria.',
            'breeds_id.exists' => 'La raza no está registrada.',
            'clients_id.required' => 'El cliente es obligatorio.',
            'clients_id.exists' => 'El cliente no está registrado.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cargar y almacenar la imagen
        $petPhoto = null;
        if ($request->hasFile('petPhoto')) {
            $petPhoto = $request->file('petPhoto')->store('public/pet_images'); // Almacenar en storage/app/public/pet_images
            $petPhoto = Storage::url($petPhoto); // Obtener la URL accesible públicamente
        }

        // Crear la mascota con los datos del formulario y la URL de la imagen
        $pet = Pet::create([
            'petCode' => $request->petCode,
            'petName' => $request->petName,
            'petBirthDate' => $request->petBirthDate,
            'petWeight' => $request->petWeight,
            'petColor' => $request->petColor,
            'species_id' => $request->species_id,
            'breeds_id' => $request->breeds_id,
            'petGender' => $request->petGender,
            'petPhoto' => $petPhoto, // Guardar la URL de la foto
            'petAdditional' => $request->petAdditional,
            'clients_id' => $request->clients_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mascota creada con éxito',
            'data' => $pet
        ], 201);
    }

    // Actualizar una mascota existente
    public function update(Request $request, $id)
    {
        $pet = Pet::find($id);

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Mascota no encontrada',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'petCode' => 'required|string|max:50|unique:pets,petCode,' . $id,
            'petName' => 'required|string|max:100',
            'petBirthDate' => 'required|date',
            'petWeight' => 'nullable|string|max:10',
            'petColor' => 'nullable|string|max:100',
            'species_id' => 'required|exists:species,id',
            'breeds_id' => 'required|exists:breeds,id',
            'petGender' => 'required|string|max:10',
            'clients_id' => 'required|exists:clients,id',
            'petPhoto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'petCode.required' => 'El código de la mascota es obligatorio.',
            'petName.required' => 'El nombre de la mascota es obligatorio.',
            'petBirthDate.required' => 'La fecha de nacimiento de la mascota es obligatoria.',
            'species_id.required' => 'La especie es obligatoria.',
            'species_id.exists' => 'La especie no está registrada.',
            'breeds_id.required' => 'La raza es obligatoria.',
            'breeds_id.exists' => 'La raza no está registrada.',
            'clients_id.required' => 'El cliente es obligatorio.',
            'clients_id.exists' => 'El cliente no está registrado.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Actualizar la imagen si se subió una nueva
        if ($request->hasFile('petPhoto')) {
            if ($pet->petPhoto) {
                Storage::delete(str_replace('/storage', 'public', $pet->petPhoto)); // Eliminar la imagen anterior
            }
            $petPhoto = $request->file('petPhoto')->store('public/pet_images');
            $petPhoto = Storage::url($petPhoto);
            $pet->petPhoto = $petPhoto;
        }

        $pet->update($request->only([
            'petCode', 'petName', 'petBirthDate', 'petWeight', 'petColor', 
            'species_id', 'breeds_id', 'petGender', 'petPhoto', 'petAdditional', 
            'clients_id'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Mascota actualizada con éxito',
            'data' => $pet
        ], 200);
    }

    // Eliminar una mascota
    public function destroy($id)
    {
        $pet = Pet::find($id);

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Mascota no encontrada',
            ], 404);
        }

        // Eliminar la imagen asociada si existe
        if ($pet->petPhoto) {
            Storage::delete(str_replace('/storage', 'public', $pet->petPhoto));
        }

        $pet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mascota eliminada con éxito',
        ], 200);
    }

}