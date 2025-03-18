@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1 class="ml-3">Asignar Contrato</h1>
   @livewireStyles

@stop

@section('content')
       {{-- <!-- Mostrar mensaje de error -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

    <div class="container-fluid ">
        <!-- Card para mostrar los clientes sin contrato -->
        <div class="card">
            <div class="card-header">
                <h4><strong>Nombre:</strong> <span class="text-success">{{ $cliente->nombre }}</span></h4>
            </div>
            <div class="card-body">
               <!-- Mostrar los datos del cliente -->
               <p><strong>ID Cliente:</strong> {{ $cliente->id }}</p>
               <p><strong>Nombre:</strong> {{ $cliente->nombre }}</p>
               <p><strong>Dirección:</strong> {{ $cliente->direccion }}</p>

                <!-- Formulario para asignar el contrato -->
                <form action="{{ route('guardarContrato') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                
                    <!-- Selector para el plan -->
                    <div class="mb-3">
                        <label for="plan_id" class="form-label">Plan:</label>
                        <select class="form-select" name="plan_id" required>
                            <option value="" disabled selected>Seleccione un Plan</option>
                            @foreach ($planes as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <!-- Fecha de inicio -->
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio:</label>
                        <input type="date" class="form-control" name="fecha_inicio" required>
                    </div>
                
                    <!-- Fecha de fin -->
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin:</label>
                        <input type="date" class="form-control" name="fecha_fin" required>
                    </div>
                
                    <!-- Precio -->
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio:</label>
                        <input type="text" class="form-control" name="precio" pattern="^\d+(\.\d{1,3})?$" title="El precio debe tener hasta 3 decimales" required>
                        <small class="form-text text-muted">Ejemplo: 80.000</small>
                    </div>
                
                    <!-- Botón de envío -->
                    <button type="submit" class="btn btn-primary">Asignar Contrato</button>
                
                    <!-- Botón de cancelación -->
                    <a href="{{ route('contratoIndex') }}" class="btn btn-secondary">Cancelar</a>
                </form>
                
            </div>
        </div>
    </div>  --}}
    @livewire('asignar-contrato', ['cliente' => $cliente->id])
    @livewireScripts

@stop

@section('css')
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> 
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@stop

