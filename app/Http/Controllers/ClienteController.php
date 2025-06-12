<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar si el usuario actual tiene ID 1 o 2
        if (!in_array(auth()->id(), [1, 2,3])) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
            // O puedes redirigirlo si prefieres:
            // return redirect()->route('dashboard')->with('error', 'Acceso no autorizado');
        }

        return view('clientes.create');
    }
    // Enviar ala vista imagenes
    public function imagenes($id)
    {
        $cliente = Cliente::findOrFail($id);
        // Retorna la vista con las imágenes
        return view('clientes.imagenes', compact('cliente'));
    }

    public function search()
    {
         // Verificar si el usuario actual tiene ID 1 o 2
         if (!in_array(auth()->id(), [1, 2,3])) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
            
        }
        return view('clientes.search');
    }

    public function historialFacturas(Cliente $cliente)
    {
        return view('clientes.historial-facturas', compact('cliente'));
    }

    public function graficas($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.graficas', compact('cliente'));
    }
    
    // Funcion para mostrar clientes sin ip asignadas 
    public function asignarIPindex()
    {
         // Verificar si el usuario actual tiene ID 1 o 2
         if (!in_array(auth()->id(), [1, 2,3])) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
            
        }
       // Obtener todos los clientes donde el campo 'ip' sea nulo y que tengan un contrato asignado
            $clientes = Cliente::whereNull('ip')
            ->whereHas('contratos')  // Asegurarse de que el cliente tenga un contrato
            ->get();

        // Retornar la vista con los clientes
        return view('clientes.asignar_ip', compact('clientes'));
    }

     // Vista para asignar IP a un cliente en particular 

    public function asignarIpCliente($id_cliente)
    {
       $cliente= Cliente::findOrFail($id_cliente);
       return view('clientes.asignaripshow', compact('cliente'));
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
         // Verificar si el usuario actual tiene ID 1 o 2
         if (!in_array(auth()->id(), [1, 2,3])) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
          
        }
        $inventarios = Inventario::where('cliente_id', $id)
        ->get();
        
        // Cargar tickets con sus visitas relacionadas
        $cliente = Cliente::with(['contrato.plan', 'tickets.visita'])->find($id);
       
        // Obtener los tickets del cliente
        $tickets = $cliente->tickets;
        // Contar el total de tickets abiertos
        $totalTicketsAbiertos = $cliente->tickets()->where('estado', 'abierto')->count();
        return view('clientes.show', compact('cliente','tickets','totalTicketsAbiertos','inventarios'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // return $request;
        $request->validate([
            'cedula' => 'required|string|max:20',
            'telefono' => 'required|string|max:20',
            'correo' => 'nullable|email',
            'direccion' => 'required|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'nombre' => 'nullable|string',
            'descripcion' => 'nullable|string',
        ]);
    
        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());
    
        return redirect()->back()->with('success', 'Información del cliente actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        //
    }
}
