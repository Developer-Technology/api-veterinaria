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
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        // Validación
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'doc' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'privilege' => 'required|string|in:User,Admin,Super Admin'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'doc.required' => 'El documento es obligatorio.',
            'doc.unique' => 'El documento ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'privilege.required' => 'El privilegio es obligatorio.',
            'privilege.in' => 'El privilegio debe ser uno de los siguientes: User, Admin, Super Admin.'
        ]);
    
        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Crear nuevo usuario
        $user = User::create([
            'doc' => $request->doc,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'sex' => $request->sex,
            'status' => $request->status,
            'privilege' => $request->privilege,
            'username' => $request->username
        ]);
    
        // Respuesta
        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado correctamente.',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ]
        ], 201);
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

    /**
     * Eliminar un usuario.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente.'
        ], 200);
    }

}