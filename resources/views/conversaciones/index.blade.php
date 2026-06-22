@extends('adminlte::page')

@section('title', 'Conversaciones')

@section('content_header')
<h1 class="ml-3">
    <i class="fas fa-comments mr-2"></i>
    Conversaciones
</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body p-0" style="height: 75vh;">
            @livewire('conversaciones.chat')
        </div>
    </div>
@stop

@include('partials.global-footer')

@push('css')
<style>
    .chat-container {
        height: 100%;
        background: white;
        overflow: hidden;
    }

    .chat-container .row {
        height: 100%;
    }

    /* Sidebar con scroll */
    .chat-container .col-12.col-md-4 {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    /* Área de chat con scroll */
    .chat-container .col-12.col-md-8 {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .list-group-item.active {
        background-color: #e8f5e9 !important;
        border-color: #c8e6c9 !important;
        color: #1b5e20 !important;
    }

    .list-group-item:hover {
        background-color: #f5f5f5;
    }

    #messageArea {
        scroll-behavior: smooth;
        overflow-y: auto !important;
    }

    /* Scroll personalizado */
    .overflow-auto::-webkit-scrollbar {
        width: 6px;
    }

    .overflow-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .overflow-auto::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Asegurar que los textos no se desborden */
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .chat-container {
            height: auto;
        }
        .chat-container .col-12 {
            height: 300px !important;
        }
        .chat-container .col-md-8 {
            height: 450px !important;
        }
    }
</style>
@endpush

@push('js')
<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('mensajes-cargados', function() {
            const messageArea = document.getElementById('messageArea');
            if (messageArea) {
                setTimeout(() => {
                    messageArea.scrollTop = messageArea.scrollHeight;
                }, 100);
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const messageArea = document.getElementById('messageArea');
        if (messageArea) {
            setTimeout(() => {
                messageArea.scrollTop = messageArea.scrollHeight;
            }, 500);
        }
    });
</script>
@endpush