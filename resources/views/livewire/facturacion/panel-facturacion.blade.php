<div>
    <div>
        @if($facturas->isEmpty())
            <div class="alert alert-info">
                No hay facturas registradas
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Factura</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($facturas as $factura)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $factura->numero_factura }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $factura->contrato->cliente->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${{ number_format($factura->monto_total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
