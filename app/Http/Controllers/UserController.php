<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Obtener todos los usuarios.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Obtener todos los usuarios
        $users = User::all();

        return response()->json([
            'success' => true,
            'data' => $users,
        ], 200);
    }

    /**
     * Obtener un usuario por su ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Buscar el usuario por ID
        $user = User::find($id);

        // Verificar si el usuario existe
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    /**
     * Editar un usuario existente.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validar los datos de la solicitud
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'doc' => 'required|string|unique:users,doc,' . $id, // Ignora el ID actual en la validación única
            'email' => 'required|email|unique:users,email,' . $id, // Ignora el ID actual en la validación única
            'phone' => 'nullable|string',
            'sex' => 'nullable|string|in:Masculino,Femenino',
            'status' => 'required|string|in:Activo,Inactivo',
            'privilege' => 'required|string|in:User,Admin,Super Admin'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'doc.required' => 'El documento es obligatorio.',
            'doc.unique' => 'El documento ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'El correo electrónico ya está registrado.',
        ]);

        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar el usuario por ID
        $user = User::find($id);

        // Verificar si el usuario existe
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        // Actualizar los datos del usuario
        $user->name = $request->name;
        $user->doc = $request->doc;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->sex = $request->sex;
        $user->status = $request->status;
        $user->privilege = $request->privilege;

        // Guardar los cambios
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente.',
            'data' => $user
        ], 200);
    }

}