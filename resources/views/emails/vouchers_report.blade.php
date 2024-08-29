<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Comprobantes Procesados</title>
</head>
<body>
    <h1>Reporte de Comprobantes Procesados</h1>

    <h2>Comprobantes Subidos Correctamente</h2>
    <ul>
        @forelse($successfulVouchers as $voucher)
            <li>
                {{ $voucher['issuer_name'] }} - {{ $voucher['series'] }} - {{ $voucher['correlative'] }} - {{ $voucher['total_amount'] }}
            </li>
        @empty
            <p>No se subieron comprobantes correctamente.</p>
        @endforelse
    </ul>

    <h2>Comprobantes que No Pudieron Registrarse</h2>
    <ul>
        @forelse($failedVouchers as $failure)
            <li>
                Razón: {{ $failure['reason'] }} - Contenido XML: {{ $failure['xmlContent'] }}
            </li>
        @empty
            <p>Todos los comprobantes se registraron correctamente.</p>
        @endforelse
    </ul>
</body>
</html>
