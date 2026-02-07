@extends('adminlte::page')
@section('title', 'Crear Planes')

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-clipboard-list mr-2 text-success"></i>
    Gestión de Planes
</h1>
@stop

@section('content')

<div class="card text-center">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">

            <!-- Pestaña Crear -->
            <li class="nav-item" role="presentation">
                <a class="nav-link active"
                   id="link-tab"
                   data-bs-toggle="tab"
                   href="#link"
                   role="tab"
                   aria-controls="link"
                   aria-selected="true">
                    Agregar Plan
                </a>
            </li>

            <!-- Pestaña Listar -->
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   id="planes-tab"
                   data-bs-toggle="tab"
                   href="#planes"
                   role="tab"
                   aria-controls="planes"
                   aria-selected="false">
                    Planes
                </a>
            </li>

        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="myTabContent">

            <!-- ================= TAB PLANES ================= -->
            <div class="tab-pane fade"
                 id="planes"
                 role="tabpanel"
                 aria-labelledby="planes-tab">

                <livewire:planes.listar-planes />

            </div>

            <!-- ================= TAB CREAR ================= -->
            <div class="tab-pane fade show active"
                 id="link"
                 role="tabpanel"
                 aria-labelledby="link-tab">

                <livewire:planes.crear-plan />

            </div>

        </div>
    </div>
</div>

@stop

@include('partials.global-footer')
