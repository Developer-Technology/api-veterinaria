<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class SupplierController extends Controller
{
    // Aplicar middleware para requerir autenticación mediante JWT
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Obtener todos los proveedores.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $suppliers = Supplier::all(); // Obtener todos los proveedores

        return response()->json([
            'success' => true,
            'data' => $suppliers
        ], 200);
    }

    /**
     * Obtener un proveedor específico por ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $supplier = Supplier::find($id); // Buscar proveedor por ID

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $supplier
        ], 200);
    }

    /**
     * Crear un nuevo proveedor.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        // Validación
        $validator = Validator::make($request->all(), [
            'supplierDoc' => 'required|string|unique:suppliers,supplierDoc',
            'supplierName' => 'required|string|max:50',
            'supplierPhone' => 'nullable|string|max:20',
            'supplierEmail' => 'nullable|email|max:150|unique:suppliers,supplierEmail',
            'supplierAddress' => 'nullable|string|max:150',
        ], [
            'supplierDoc.required' => 'El documento es obligatorio.',
            'supplierDoc.unique' => 'El documento ya está registrado.',
            'supplierName.required' => 'El nombre es obligatorio.',
            'supplierEmail.unique' => 'El correo electrónico ya está registrado.',
        ]);
    
        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Crear nuevo proveedor
        $supplier = Supplier::create([
            'supplierDoc' => $request->supplierDoc,
            'supplierName' => $request->supplierName,
            'supplierPhone' => $request->supplierPhone,
            'supplierEmail' => $request->supplierEmail,
            'supplierAddress' => $request->supplierAddress
        ]);
    
        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Proveedor registrado correctamente.',
            'supplier' => [
                'id' => $supplier->id,
                'supplierDoc' => $supplier->supplierDoc,
                'supplierName' => $supplier->supplierName,
                'created_at' => $supplier->created_at,
            ]
        ], 201);
    }   
    
    /**
     * Actualizar un proveedor existente.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validar datos entrantes
        $validator = Validator::make($request->all(), [
            'supplierDoc' => 'required|string|unique:suppliers,supplierDoc,' . $id,
            'supplierName' => 'required|string|max:255',
            'supplierPhone' => 'nullable|string',
            'supplierEmail' => 'required|email|unique:suppliers,supplierEmail,' . $id,
        ], [
            'supplierName.required' => 'El nombre es obligatorio.',
            'supplierDoc.required' => 'El documento es obligatorio.',
            'supplierDoc.unique' => 'El documento ya está registrado.',
            'supplierEmail.required' => 'El correo electrónico es obligatorio.',
            'supplierEmail.unique' => 'El correo electrónico ya está registrado.',
        ]);

        // Si la validación falla, devuelve los errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar proveedor por ID
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado.'
            ], 404);
        }

        // Actualizar los datos del proveedor
        $supplier->supplierDoc = $request->supplierDoc;
        $supplier->supplierName = $request->supplierName;
        $supplier->supplierPhone = $request->supplierPhone;
        $supplier->supplierEmail = $request->supplierEmail;
        $supplier->supplierAddress = $request->supplierAddress;
        $supplier->supplierPhotoUrl = $request->supplierPhotoUrl;

        // Guardar cambios
        $supplier->save();

        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Proveedor actualizado correctamente.',
            'data' => $supplier
        ], 200);

    }

    /**
     * Eliminar un proveedor.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'Proveedor no encontrado.'
            ], 404);
        }

        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proveedor eliminado correctamente.'
        ], 200);
    }

}