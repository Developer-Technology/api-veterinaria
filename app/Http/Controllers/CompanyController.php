<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;

class CompanyController extends Controller
{
    // Aplicar middleware para requerir autenticación mediante JWT
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Obtener todas las empresas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $companies = Company::all(); // Obtener todas las empresas

        return response()->json([
            'success' => true,
            'data' => $companies
        ], 200);
    }

    /**
     * Obtener una empresa específica por ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $company = Company::find($id); // Buscar empresa por ID

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa no encontrada.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $company
        ], 200);
    }

    /**
     * Crear una nueva empresa.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'companyDoc' => 'required|string|unique:companies,companyDoc|max:50',
            'companyName' => 'required|string|max:100',
            'companyAddress' => 'required|string|max:200',
            'companyPhone' => 'required|string|max:20',
            'companyEmail' => 'required|email|max:100|unique:companies,companyEmail',
            'companyPhoto' => 'nullable|string',  // Foto no es obligatoria
            'companyCurrency' => 'required|string|max:10',
            'companyTax' => 'required|numeric|min:0',
        ], [
            'companyDoc.required' => 'El documento es obligatorio.',
            'companyDoc.unique' => 'El documento ya está registrado.',
            'companyName.required' => 'El nombre es obligatorio.',
            'companyAddress.required' => 'La dirección es obligatoria.',
            'companyPhone.required' => 'El teléfono es obligatorio.',
            'companyEmail.required' => 'El correo electrónico es obligatorio.',
            'companyEmail.unique' => 'El correo electrónico ya está registrado.',
            'companyCurrency.required' => 'La moneda es obligatoria.',
            'companyTax.required' => 'El impuesto es obligatorio.',
        ]);

        // Verificar si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear nueva empresa
        $company = Company::create([
            'companyDoc' => $request->companyDoc,
            'companyName' => $request->companyName,
            'companyAddress' => $request->companyAddress,
            'companyPhone' => $request->companyPhone,
            'companyEmail' => $request->companyEmail,
            'companyPhoto' => $request->companyPhoto,  // Puede ser null
            'companyCurrency' => $request->companyCurrency,
            'companyTax' => $request->companyTax,
        ]);

        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Empresa registrada correctamente.',
            'company' => $company
        ], 201);
    }

    /**
     * Actualizar una empresa existente.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validar datos entrantes
        $validator = Validator::make($request->all(), [
            'companyDoc' => 'required|string|max:50|unique:companies,companyDoc,' . $id,
            'companyName' => 'required|string|max:100',
            'companyAddress' => 'required|string|max:200',
            'companyPhone' => 'required|string|max:20',
            'companyEmail' => 'required|email|max:100|unique:companies,companyEmail,' . $id,
            'companyPhoto' => 'nullable|string',  // Foto no es obligatoria
            'companyCurrency' => 'required|string|max:10',
            'companyTax' => 'required|numeric|min:0',
        ], [
            'companyName.required' => 'El nombre es obligatorio.',
            'companyDoc.required' => 'El documento es obligatorio.',
            'companyDoc.unique' => 'El documento ya está registrado.',
            'companyAddress.required' => 'La dirección es obligatoria.',
            'companyPhone.required' => 'El teléfono es obligatorio.',
            'companyEmail.required' => 'El correo electrónico es obligatorio.',
            'companyEmail.unique' => 'El correo electrónico ya está registrado.',
            'companyCurrency.required' => 'La moneda es obligatoria.',
            'companyTax.required' => 'El impuesto es obligatorio.',
        ]);

        // Si la validación falla, devuelve los errores
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar empresa por ID
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa no encontrada.'
            ], 404);
        }

        // Actualizar los datos de la empresa
        $company->companyDoc = $request->companyDoc;
        $company->companyName = $request->companyName;
        $company->companyAddress = $request->companyAddress;
        $company->companyPhone = $request->companyPhone;
        $company->companyEmail = $request->companyEmail;
        $company->companyPhoto = $request->companyPhoto;  // Puede ser null
        $company->companyCurrency = $request->companyCurrency;
        $company->companyTax = $request->companyTax;

        // Guardar cambios
        $company->save();

        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Empresa actualizada correctamente.',
            'data' => $company
        ], 200);
    }

    public function upload(Request $request, $id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa no encontrada',
            ], 404);
        }

        // Validar que exista un archivo de imagen
        $validator = Validator::make($request->all(), [
            'companyPhoto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Manejar la imagen
        if ($request->hasFile('companyPhoto')) {
            // Si ya tiene una imagen, eliminar la anterior
            if ($company->companyPhoto) {
                Storage::delete(str_replace('/storage', 'public', $company->companyPhoto));
            }

            // Guardar la nueva imagen
            $companyPhoto = $request->file('companyPhoto')->store('public/company_logos');
            $company->companyPhoto = Storage::url($companyPhoto);
            $company->save();

            return response()->json([
                'success' => true,
                'message' => 'Logo de la empresa actualizada con éxito',
                'data' => $company->companyPhoto,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo actualizar la logo de la empresa',
        ], 400);
    }

    /**
     * Eliminar una empresa.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa no encontrada.'
            ], 404);
        }

        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Empresa eliminada correctamente.'
        ], 200);
    }

}