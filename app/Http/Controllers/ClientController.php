<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ClientController extends Controller
{
    // Aplicar middleware para requerir autenticación mediante JWT
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Obtener todos los clientes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $clients = Client::all(); // Obtener todos los clientes

        return response()->json([
            'success' => true,
            'data' => $clients
        ], 200);
    }

    /**
     * Obtener un cliente específico por ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $client = Client::find($id); // Buscar cliente por ID

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $client
        ], 200);
    }

    /**
     * Crear un nuevo cliente.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        // Validación
        $validator = Validator::make($request->all(), [
            'clientDoc' => 'required|string|unique:clients,clientDoc',
            'clientName' => 'required|string|max:50',
            'clientPhone' => 'nullable|string|max:20',
            'clientEmail' => 'nullable|email|max:150|unique:clients,clientEmail',
            'clientAddress' => 'nullable|string|max:150',
        ], [
            'clientDoc.required' => 'El documento es obligatorio.',
            'clientDoc.unique' => 'El documento ya está registrado.',
            'clientName.required' => 'El nombre es obligatorio.',
            'clientEmail.unique' => 'El correo electrónico ya está registrado.',
        ]);
    
        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Crear nuevo cliente
        $client = Client::create([
            'clientDoc' => $request->clientDoc,
            'clientName' => $request->clientName,
            'clientPhone' => $request->clientPhone,
            'clientEmail' => $request->clientEmail,
            'clientAddress' => $request->clientAddress,
            'clientGender' => $request->clientGender,
        ]);
    
        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Cliente registrado correctamente.',
            'client' => [
                'id' => $client->id,
                'clientDoc' => $client->clientDoc,
                'clientName' => $client->clientName,
                'created_at' => $client->created_at,
            ]
        ], 201);
    }      

    /**
     * Actualizar un cliente existente.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validar datos entrantes
        $validator = Validator::make($request->all(), [
            'clientDoc' => 'required|string|unique:clients,clientDoc,' . $id,
            'clientName' => 'required|string|max:255',
            'clientPhone' => 'nullable|string',
            'clientGender' => 'nullable|string|in:Masculino,Femenino',
            'clientEmail' => 'required|email|unique:clients,clientEmail,' . $id,
        ], [
            'clientName.required' => 'El nombre es obligatorio.',
            'clientDoc.required' => 'El documento es obligatorio.',
            'clientDoc.unique' => 'El documento ya está registrado.',
            'clientEmail.required' => 'El correo electrónico es obligatorio.',
            'clientEmail.unique' => 'El correo electrónico ya está registrado.',
        ]);

        // Si la validación falla, devuelve los errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar cliente por ID
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado.'
            ], 404);
        }

        // Actualizar los datos del cliente
        $client->clientDoc = $request->clientDoc;
        $client->clientName = $request->clientName;
        $client->clientGender = $request->clientGender;
        $client->clientPhone = $request->clientPhone;
        $client->clientEmail = $request->clientEmail;
        $client->clientAddress = $request->clientAddress;
        $client->clientPhotoUrl = $request->clientPhotoUrl;

        // Guardar cambios
        $client->save();

        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado correctamente.',
            'data' => $client
        ], 200);

    }

    /**
     * Eliminar un cliente.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado.'
            ], 404);
        }

        $client->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado correctamente.'
        ], 200);
    }

}