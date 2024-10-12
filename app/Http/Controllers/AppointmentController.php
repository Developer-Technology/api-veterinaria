<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Validator;

class AppointmentController extends Controller
{
    // Middleware JWT para proteger las rutas
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // Listar todas las citas con la información de la mascota asociada
    public function index()
    {
        $appointments = Appointment::with('pet')->get();

        // Mapear los resultados para formatearlos según se requiere
        $formattedAppointments = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'petName' => $appointment->pet->petName,
                'owner' => $appointment->pet->owner->name, // Supongo que hay una relación de la mascota con el cliente
                'appointmentDate' => $appointment->appointmentDate,
                'reason' => $appointment->reason,
                'status' => $appointment->status,
                'emailAlertSent' => $appointment->emailAlertSent,
                'whatsappAlertSent' => $appointment->whatsappAlertSent,
                'created_at' => $appointment->created_at,
                'updated_at' => $appointment->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedAppointments
        ], 200);
    }

    // Obtener una cita específica por su ID
    public function show($id)
    {
        $appointment = Appointment::with('pet')->find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $appointment
        ], 200);
    }

    // Crear una nueva cita
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pet_id' => 'required|exists:pets,id',
            'appointmentDate' => 'required|date',
            'reason' => 'required|string|max:255',
        ], [
            'pet_id.required' => 'La mascota es obligatoria.',
            'pet_id.exists' => 'La mascota no está registrada.',
            'appointmentDate.required' => 'La fecha de la cita es obligatoria.',
            'reason.required' => 'El motivo de la cita es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $appointment = Appointment::create([
            'pet_id' => $request->pet_id,
            'appointmentDate' => $request->appointmentDate,
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita creada con éxito',
            'data' => $appointment
        ], 201);
    }

    // Actualizar una cita existente
    public function update(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'appointmentDate' => 'required|date',
            'reason' => 'required|string|max:255',
            'status' => 'required|in:pending,confirmed,cancelled'
        ], [
            'appointmentDate.required' => 'La fecha de la cita es obligatoria.',
            'reason.required' => 'El motivo de la cita es obligatorio.',
            'status.required' => 'El estado de la cita es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $appointment->update($request->only(['appointmentDate', 'reason', 'status']));

        return response()->json([
            'success' => true,
            'message' => 'Cita actualizada con éxito',
            'data' => $appointment
        ], 200);
    }

    // Eliminar una cita
    public function destroy($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada',
            ], 404);
        }

        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cita eliminada con éxito',
        ], 200);
    }

}