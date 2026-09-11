<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Reposición y Compras - ARStock</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4 portrait;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 24px;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .brand {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }
        .meta {
            text-align: right;
            font-size: 11px;
            color: #475569;
        }
        .meta strong {
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }
        th.text-right, td.text-right {
            text-align: right;
        }
        th.text-center, td.text-center {
            text-align: center;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .tag-critico {
            color: #b91c1c;
            font-weight: bold;
        }
        .total-box {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
            margin-bottom: 40px;
        }
        .total-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 20px;
            text-align: right;
        }
        .total-card span {
            font-size: 11px;
            color: #64748b;
        }
        .total-card strong {
            display: block;
            font-size: 18px;
            color: #0f172a;
            margin-top: 2px;
        }
        .footer-signatures {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .signature-line {
            width: 200px;
            border-top: 1px dashed #94a3b8;
            text-align: center;
            padding-top: 8px;
            font-size: 10px;
            color: #64748b;
        }
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .no-print-bar button {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 12px;
        }
        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    {{-- Barra superior interactiva (no sale al imprimir) --}}
    <div class="no-print-bar">
        <span>📄 Lista de Compras y Reposición de Mercadería</span>
        <button onclick="window.print()">🖨️ Imprimir / Guardar como PDF</button>
    </div>

    {{-- Encabezado del Documento --}}
    <div class="header">
        <div>
            <div class="brand">ARStock</div>
            <div class="subtitle">Orden de Reposición de Mercadería y Pedidos a Proveedores</div>
        </div>
        <div class="meta">
            <div>Fecha de emisión: <strong>{{ date('d/m/Y H:i') }}</strong></div>
            <div>Total artículos a reponer: <strong>{{ $productosReponer->count() }}</strong></div>
        </div>
    </div>

    {{-- Tabla de Productos a Reponer --}}
    <table>
        <thead>
            <tr>
                <th style="width: 10%">Cód.</th>
                <th style="width: 30%">Producto</th>
                <th style="width: 20%">Proveedor / Preventista</th>
                <th class="text-center" style="width: 10%">Stock / Mín.</th>
                <th class="text-center" style="width: 10%">A Pedir</th>
                <th class="text-right" style="width: 10%">P. Costo</th>
                <th class="text-right" style="width: 10%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productosReponer as $prod)
                <tr>
                    <td style="font-family: monospace; font-size: 10px;">{{ $prod->codigo ?? '—' }}</td>
                    <td>
                        <strong>{{ $prod->nombre }}</strong>
                        <div style="font-size: 10px; color: #64748b;">{{ $prod->categoria->nombre ?? 'Sin categoría' }}</div>
                    </td>
                    <td>
                        <strong>{{ $prod->proveedor->empresa ?? 'Sin proveedor asignado' }}</strong>
                        @if ($prod->proveedor && $prod->proveedor->telefono)
                            <div style="font-size: 10px; color: #64748b;">Tel: {{ $prod->proveedor->telefono }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="{{ $prod->stock <= $prod->stock_critico ? 'tag-critico' : '' }}">
                            {{ $prod->stock }}
                        </span> / {{ $prod->stock_minimo }}
                    </td>
                    <td class="text-center">
                        <strong style="color: #0f172a; font-size: 12px;">+{{ $prod->sugerido_reponer }}</strong>
                    </td>
                    <td class="text-right">${{ number_format($prod->precio_compra, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>${{ number_format($prod->costo_reposicion, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 25px; color: #64748b;">
                        Todos los productos tienen niveles de stock saludables. No hay compras pendientes requeridas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Total de Inversión --}}
    <div class="total-box">
        <div class="total-card">
            <span>Inversión Total Estimada:</span>
            <strong>${{ number_format($totalInversion, 0, ',', '.') }}</strong>
        </div>
    </div>

    {{-- Firmas de Conformidad --}}
    <div class="footer-signatures">
        <div class="signature-line">
            Firma Responsable de Compras / Local
        </div>
        <div class="signature-line">
            Firma / Conformidad del Proveedor
        </div>
    </div>

</body>
</html>
