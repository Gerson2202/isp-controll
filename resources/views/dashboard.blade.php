@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

    <h1 class="m-0 text-dark d-flex align-items-center">
        <i class="bi bi-speedometer2 me-3 fs-2 text-info"></i>
        Panel de Control
    </h1>
@stop


@section('content')
    <div class="row">
        <!-- Tarjetas resumen -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>{{ $clientesCount }}</h3>
                    <p>Clientes Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('clientesBuscar') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3>{{ $equiposCount }}</h3>
                    <p>Equipos Instalados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <a href="{{ route('inventarioList') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>{{ $nodosCount }}</h3>
                    <p>Nodos Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-server"></i>
                </div>
                <a href="{{ route('nodosIndex') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>{{ $ticketsAbiertos }}</h3>
                    <p>Tickets Abiertos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <a href="{{ route('ticketsIndex') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- Últimos Tickets -->
    <div class="card">
        <div class="card-header border-transparent">
            <h3 class="card-title">Últimos Tickets</h3>
            <div class="card-tools">
                <span class="badge badge-danger">{{ $ticketsRecientes->count() }} Tickets nuevos</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table m-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ticketsRecientes as $ticket)
                            <tr>
                                <td>
                                    <a href="{{ route('tickets.edit', $ticket->id) }}">
                                        #{{ $ticket->id }}
                                    </a>
                                </td>

                                <td>
                                    <a href="{{ route('clientes.show', $ticket->cliente->id) }}"
                                        style="text-decoration: none; color: #1D4ED8;"
                                        onmouseover="this.style.textDecoration='underline';"
                                        onmouseout="this.style.textDecoration='none';">
                                        {{ $ticket->cliente->nombre }}
                                    </a>
                                </td>



                                <td>{{ Str::limit($ticket->tipo_reporte, 30) }}</td>
                                <td>
                                    <span class="badge badge-{{ $ticket->estado == 'Abierto' ? 'danger' : 'success' }}">
                                        {{ $ticket->estado }}
                                    </span>
                                </td>
                                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <a href="{{ route('tickets.historial') }}" class="btn btn-sm btn-secondary float-right">Ver Todos</a>
        </div>
    </div>
    <div class="row">

        {{-- GRAFICA DE CLIENTES --}}
        <div class="col-md-6">

            <div class="card card-outline card-primary">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>
                        Crecimiento de Clientes
                    </h3>

                    <span class="badge badge-primary">
                        {{ now()->year }}
                    </span>

                </div>

                <div class="card-body">

                    <div class="position-relative" style="height: 350px;">

                        <canvas id="clientesChart"></canvas>

                    </div>

                </div>

            </div>

        </div>

        {{-- GRAFICA CONTABLE --}}
        <div class="col-md-6">

            <div class="card card-outline card-success">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <h3 class="card-title">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        Ingresos Mensuales
                    </h3>

                    <span class="badge badge-success">
                        {{ now()->year }}
                    </span>

                </div>

                <div class="card-body">

                    <div class="position-relative" style="height: 350px;">

                        <canvas id="ingresosChart"></canvas>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <!-- Chat flotante solo para admin -->
    <!--
    @auth
        @if (auth()->user()->email === 'gersonpsj@gmail.com')
            {{-- Cambia por tu condición de admin --}}
            <div id="chatWidget" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
                <button id="chatButton"
                    style="
                    width: 60px; height: 60px; border-radius: 50%;
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                    color: white; font-size: 24px;
                ">💬</button>

                <div id="chatWindow"
                    style="
                    position: absolute; bottom: 80px; right: 0;
                    width: 380px; height: 500px; background: white;
                    border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.2);
                    display: none; flex-direction: column; overflow: hidden;
                    border: 1px solid #e0e0e0;
                ">
                    <div
                        style="
                        background: linear-gradient(135deg, #667eea, #764ba2);
                        color: white; padding: 15px; font-weight: bold;
                    ">
                        🤖 Asistente IA
                        <button id="closeChat"
                            style="float: right; background: none; border: none; color: white; cursor: pointer;">✕</button>
                    </div>

                    <div id="messages" style="flex: 1; overflow-y: auto; padding: 15px; background: #f5f5f5;">
                        <div style="text-align: center; color: #999; padding: 20px;">
                            💡 Pregúntame:<br>
                            • ¿Cuántos clientes hay?<br>
                            • Clientes en mora<br>
                            • Instalaciones de este mes
                        </div>
                    </div>

                    <div style="padding: 15px; background: white; border-top: 1px solid #ddd;">
                        <form id="chatForm" style="display: flex; gap: 10px;">
                            <input type="text" id="questionInput" placeholder="Escribe tu pregunta..."
                                style="
                                flex: 1; padding: 10px; border: 1px solid #ddd;
                                border-radius: 20px; outline: none;
                            ">
                            <button type="submit"
                                style="
                                padding: 10px 20px;
                                background: linear-gradient(135deg, #667eea, #764ba2);
                                color: white; border: none; border-radius: 20px; cursor: pointer;
                            ">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                const chatButton = document.getElementById('chatButton');
                const chatWindow = document.getElementById('chatWindow');
                const closeChat = document.getElementById('closeChat');
                const chatForm = document.getElementById('chatForm');
                const questionInput = document.getElementById('questionInput');
                const messages = document.getElementById('messages');

                chatButton.onclick = () => chatWindow.style.display = chatWindow.style.display === 'none' ? 'flex' : 'none';
                closeChat.onclick = () => chatWindow.style.display = 'none';

                function addMessage(text, isUser) {
                    const div = document.createElement('div');
                    div.style.marginBottom = '10px';
                    div.style.textAlign = isUser ? 'right' : 'left';

                    const bubble = document.createElement('div');
                    bubble.style.display = 'inline-block';
                    bubble.style.padding = '10px 15px';
                    bubble.style.borderRadius = '15px';
                    bubble.style.maxWidth = '80%';
                    bubble.style.wordWrap = 'break-word';
                    bubble.style.whiteSpace = 'pre-wrap';

                    if (isUser) {
                        bubble.style.background = 'linear-gradient(135deg, #667eea, #764ba2)';
                        bubble.style.color = 'white';
                    } else {
                        bubble.style.background = 'white';
                        bubble.style.color = '#333';
                        bubble.style.border = '1px solid #ddd';
                    }

                    bubble.textContent = text;
                    div.appendChild(bubble);
                    messages.appendChild(div);
                    messages.scrollTop = messages.scrollHeight;
                }

                function addTyping() {
                    const div = document.createElement('div');
                    div.id = 'typing';
                    div.style.marginBottom = '10px';
                    div.style.textAlign = 'left';

                    const bubble = document.createElement('div');
                    bubble.style.display = 'inline-block';
                    bubble.style.padding = '10px 15px';
                    bubble.style.background = 'white';
                    bubble.style.border = '1px solid #ddd';
                    bubble.style.borderRadius = '15px';
                    bubble.innerHTML = '🤖 Escribiendo...';

                    div.appendChild(bubble);
                    messages.appendChild(div);
                    messages.scrollTop = messages.scrollHeight;
                }

                function removeTyping() {
                    const typing = document.getElementById('typing');
                    if (typing) typing.remove();
                }

                chatForm.onsubmit = async (e) => {
                    e.preventDefault();
                    const question = questionInput.value.trim();
                    if (!question) return;

                    addMessage(question, true);
                    questionInput.value = '';
                    addTyping();

                    try {
                        const response = await fetch('/chat-ask', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                question: question
                            })
                        });

                        const data = await response.json();
                        removeTyping();

                        if (data.success) {
                            addMessage(data.answer, false);
                        } else {
                            addMessage('Error: No se pudo procesar la pregunta', false);
                        }
                    } catch (error) {
                        removeTyping();
                        addMessage('Error de conexión', false);
                    }
                };
            </script>
        @endif
    @endauth
 -->
    {{-- chat IA N8N --}}
    <livewire:floating-chat />
    {{-- FOOTER --}}
    @include('partials.global-footer')

@stop



@push('js')
    {{-- GRAFICA DE LINEA DE CLIENTES  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const canvas = document.getElementById('clientesChart');

            if (!canvas) {
                console.log('Canvas no encontrado');
                return;
            }

            const ctx = canvas.getContext('2d');

            new Chart(ctx, {

                type: 'line',

                data: {

                    labels: @json($mesesClientes),

                    datasets: [{

                        label: 'Clientes Registrados',

                        data: @json($totalesClientes),

                        borderColor: '#007bff',

                        backgroundColor: (context) => {

                            const chart = context.chart;
                            const {
                                ctx,
                                chartArea
                            } = chart;

                            if (!chartArea) {
                                return null;
                            }

                            const gradient = ctx.createLinearGradient(
                                0,
                                chartArea.top,
                                0,
                                chartArea.bottom
                            );

                            gradient.addColorStop(0, 'rgba(0,123,255,0.45)');
                            gradient.addColorStop(1, 'rgba(0,123,255,0.02)');

                            return gradient;
                        },

                        borderWidth: 4,

                        fill: true,

                        tension: 0.45,

                        pointRadius: 5,

                        pointHoverRadius: 8,

                        pointBackgroundColor: '#007bff',

                        pointBorderColor: '#fff',

                        pointBorderWidth: 2,

                        cubicInterpolationMode: 'monotone'

                    }]
                },

                options: {

                    interaction: {

                        intersect: false,

                        mode: 'index'
                    },

                    plugins: {

                        legend: {

                            display: true
                        },

                        tooltip: {

                            backgroundColor: '#111',

                            padding: 12,

                            titleFont: {
                                size: 14
                            },

                            bodyFont: {
                                size: 13
                            }

                        }

                    },

                    responsive: true,

                    maintainAspectRatio: false,

                    scales: {

                        y: {

                            beginAtZero: true,

                            min: 0,

                            max: 30,

                            ticks: {

                                stepSize: 1,

                                precision: 0

                            },

                            grid: {

                                color: 'rgba(0,0,0,0.05)'

                            }

                        }

                    }

                }

            });

        });
    </script>

    {{-- GRAFICA CONTABLE --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const canvasIngresos = document.getElementById('ingresosChart');

            if (!canvasIngresos) return;

            const ctxIngresos = canvasIngresos.getContext('2d');

            new Chart(ctxIngresos, {

                type: 'bar',

                data: {

                    labels: @json($mesesIngresos),

                    datasets: [

                        {
                            label: 'Facturado ($)',
                            data: @json($totalesFacturado),
                            backgroundColor: 'rgba(255, 193, 7, 0.7)',
                            borderColor: '#ffc107',
                            borderWidth: 2,
                            borderRadius: 8
                        },

                        {
                            label: 'Pagado ($)',
                            data: @json($totalesPagado),
                            backgroundColor: 'rgba(40, 167, 69, 0.7)',
                            borderColor: '#28a745',
                            borderWidth: 2,
                            borderRadius: 8
                        }

                    ]
                },

                options: {

                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {

                        legend: {
                            display: true
                        },

                        tooltip: {
                            callbacks: {
                                label: function(context) {

                                    const value = new Intl.NumberFormat('es-CO').format(context.parsed
                                        .y);

                                    return context.dataset.label + ': $ ' + value;
                                }
                            }
                        }

                    },

                    scales: {

                        y: {

                            beginAtZero: true,

                            ticks: {
                                callback: function(value) {

                                    return new Intl.NumberFormat('es-CO').format(value);
                                }
                            }
                        }

                    },

                    onClick: function(evt, elements) {

                        if (elements.length === 0) return;

                        const chart = this;

                        const index = elements[0].index;
                        const datasetIndex = elements[0].datasetIndex;

                        const label = chart.data.labels[index];
                        const datasetLabel = chart.data.datasets[datasetIndex].label;
                        const value = chart.data.datasets[datasetIndex].data[index];

                        const formatted = new Intl.NumberFormat('es-CO').format(value);

                        alert(`${datasetLabel}\n${label}: $ ${formatted}`);
                    }

                }
            });

        });
    </script>
@endpush
