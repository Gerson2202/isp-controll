<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Plan;
use Illuminate\Http\Request;

class ContratoController extends Controller
{
    public function index()
    {
        // Obtener clientes que no tienen contrato asociado
        $clientesSinContrato = Cliente::doesntHave('contratos')->get();
        // return $clientesSinContrato;
        return view('contratos.index',compact('clientesSinContrato'));
    }

    // Pagina donde muestro todos los contratos y poder editarlos 
    
    public function list()
    {
        
        return view('contratos.list');
    }
    
    

    public function asignarContrato($cliente_id)
    {
        // Buscar el cliente
        $cliente = Cliente::findOrFail($cliente_id);
    
        // Verificar si el cliente ya tiene un contrato asignado
        if ($cliente->contratos()->exists()) {
            // Si ya tiene un contrato, redirigir con un mensaje de error
            return redirect()->route('ERROR AL INTENTAR INGRESAR A UN CLIENTE CON CONTRATO CREADO')->with('error', 'El cliente ya tiene un contrato asignado.');
        }
    
        // Si no tiene un contrato, obtener los planes y mostrar la vista
        $planes = Plan::all();
        return view('contratos.asignar', compact('cliente', 'planes'));
    }

    public function guardarContrato(Request $request)
    {
        
        // Validar los datos del formulario
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'plan_id' => 'required|exists:plans,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'precio' => 'required|numeric|min:0',
        ]);

        // Verificar si el cliente ya tiene un contrato asignado
        $cliente = Cliente::find($request->cliente_id);

        // Si el cliente ya tiene un contrato, retornar un error
        if ($cliente->contratos()->exists()) {
            return redirect()->back()->with('error', 'Este cliente ya tiene un contrato asignado.');
        }

        // Si no tiene contrato, guardar el nuevo contrato
        $contrato = new Contrato();
        $contrato->cliente_id = $validated['cliente_id'];
        $contrato->plan_id = $validated['plan_id'];
        $contrato->fecha_inicio = $validated['fecha_inicio'];
        $contrato->fecha_fin = $validated['fecha_fin'];
        $contrato->precio = $validated['precio'];
        $contrato->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('contratoIndex')->with('success', 'Contrato asignado correctamente.');
    }
}
