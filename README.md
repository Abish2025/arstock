# ARStock Manager

Sistema integral de gestión de stock, punto de venta (POS), cuentas corrientes de clientes (cuaderno de fiados), control de caja por turnos, compras a proveedores y reportes de rentabilidad real para comercios minoristas y mayoristas.

Desarrollado sobre **Laravel 13** y **PostgreSQL**.

---

## 🚀 Requisitos del Sistema

- **PHP 8.3** o superior (con extensiones `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `bcmath`)
- **PostgreSQL 14** o superior
- **Composer** (v2.x)
- **Node.js** (v18+) y **NPM**

---

## 🛠️ Instalación y Configuración Paso a Paso

### 1. Clonar el Repositorio
```bash
git clone https://github.com/Abish2025/arstock.git
cd arstock
```

### 2. Instalar Dependencias de PHP y Frontend
```bash
composer install
npm install
```

### 3. Configurar el Entorno (.env)
Copiar el archivo de ejemplo:
```bash
cp .env.example .env
```
Generar la clave de aplicación:
```bash
php artisan key:generate
```

Configurar los parámetros de conexión a PostgreSQL en `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=arstock
DB_USERNAME=postgres
DB_PASSWORD=tu_password_aqui
```

> **Nota:** Asegúrate de que la base de datos `arstock` esté creada en tu servidor PostgreSQL antes de ejecutar las migraciones.

### 4. Ejecutar Migraciones y Datos Iniciales (Seeders)
Ejecutar la creación limpia de tablas y datos de prueba:
```bash
php artisan migrate:fresh --seed
```

### 5. Compilar los Recursos Frontend
Para desarrollo:
```bash
npm run dev
```
Para producción:
```bash
npm run build
```

### 6. Iniciar el Servidor Local
```bash
php artisan serve
```
La aplicación quedará disponible en `http://localhost:8000`.

---

## 🔐 Usuarios y Credenciales por Defecto

El seeder inicial genera dos cuentas con diferentes perfiles y permisos:

| Rol | Correo Electrónico | Contraseña | Alcance y Permisos |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@arstock.com` | `arstock123` | Acceso total: creación de usuarios, costos de compra, reportes de rentabilidad, proveedores, ajustes de stock y anulación de ventas. |
| **Cajero / Empleado** | `cajero@arstock.com` | `arstock123` | Acceso operativo: punto de cobro (POS), consulta de catálogo y precios, cobro de clientes fiados, apertura y cierre de su caja. |

---

## 📦 Módulos del Sistema y Funcionamiento

### 1. Control de Roles y Permisos (Middleware)
- Restricción estricta tanto a nivel de rutas como de controladores (`RoleMiddleware`).
- Los cajeros no tienen visibilidad de los costos de compra ni de las métricas de rentabilidad.
- Los usuarios desactivados son expulsados automáticamente del sistema al intentar operar.

### 2. Cajas y Turnos de Cajero (Apertura y Cierre)
- **Apertura de turno:** Ingreso del monto en efectivo inicial (fondo de cambio).
- **Arqueo en vivo:** Monitoreo en tiempo real del dinero cobrado desagregado por Efectivo, Transferencia/QR, Tarjeta y Fiado.
- **Cierre de turno:** El cajero ingresa el conteo físico de billetes, el sistema compara con el monto esperado (`Fondo inicial + Ventas en efectivo`) y calcula de forma transparente la diferencia (sobrante o faltante).
- **Asociación:** Cada venta queda vinculada al cajero y al turno de caja activo.

### 3. Punto de Cobro (POS) y Ventas
- **Lector de código de barras:** Entrada rápida con foco automático. Al escanear o tipear el código y presionar Enter, el producto se añade al ticket con confirmación visual.
- **Formas de cobro:**
  - **Efectivo:** Calculador automático de vuelto en vivo a partir del monto entregado.
  - **Transferencia / QR:** Registro del código o identificador de transferencia.
  - **Tarjeta (Débito/Crédito):** Registro de número de cupón / lote.
  - **Fiado (Cuenta Corriente):** Obliga a asociar un cliente registrado y valida en tiempo real que la venta no exceda el límite de crédito disponible.
- **Integridad atómica:** La venta, sus ítems, el descuento de inventario (`lockForUpdate`) y el movimiento en cuenta corriente se procesan dentro de una sola transacción de base de datos (`DB::transaction`).
- **Anulación segura:** Solo el Administrador puede anular ventas. La anulación restituye automáticamente las unidades al stock, revierte el saldo deudor del cliente si fue a fiado y registra la auditoría correspondiente.

### 4. Productos e Inventario
- CRUD completo con código único, categorías y precios válidos.
- **Alertas de stock diferenciadas:**
  - `Stock Crítico`: unidades &le; `stock_critico` (alerta roja urgente).
  - `Stock Bajo`: unidades &le; `stock_minimo` (alerta amarilla).
  - `Normal`: niveles saludables.
- **Protección de datos históricos:** El sistema prohíbe eliminar productos que tengan ventas o compras registradas en el historial.

### 5. Auditoría y Ajustes de Stock
- Historial completo de movimientos (`movimientos_stock`) con trazabilidad de: fecha, producto, tipo (venta, compra, anulación, merma, rotura, entrada, ajuste manual), cantidad, stock anterior, stock resultante, motivo y usuario responsable.

### 6. Proveedores e Ingreso de Mercadería (Compras)
- Registro de compras y recepciones con selección de proveedor, fecha y número de remito/factura.
- Carga dinámica de productos recibidos y costo unitario.
- Al confirmar el ingreso, el sistema:
  1. Incrementa automáticamente el stock disponible.
  2. Actualiza el costo de referencia (`precio_compra`) en el catálogo.
  3. Asienta el movimiento en el historial de stock.

### 7. Clientes y Cuentas Corrientes (Cuaderno de Fiados)
- Ficha individual de cliente con historial completo de movimientos (fiados otorgados y entregas/pagos).
- **Control de pagos:** Validación para impedir registrar pagos superiores a la deuda actual del cliente.
- **Control de crédito:** Verificación de límite de crédito configurable para evitar sobreendeudamiento.
- **Protección de deuda:** No es posible eliminar clientes con movimientos o ventas asociadas.

### 8. Dashboard y Reportes de Rentabilidad Real (Sin datos ficticios)
- **Eliminación total de datos simulados:** Todas las métricas, tarjetas y gráficos de Chart.js provienen de consultas reales a la base de datos PostgreSQL.
- **Cálculo de CMV y Ganancia Neta:** La rentabilidad se calcula rigurosamente a partir del costo unitario histórico registrado en el momento exacto de la venta (`detalle_ventas.costo_unitario`), asegurando precisión contable ante variaciones de precios futuros.
- Exclusión automática de ventas anuladas.
- Filtros por períodos predefinidos (Hoy, Ayer, Últimos 7 días, Este Mes, Este Año, Histórico) y rango de fechas personalizado (`fecha_desde` / `fecha_hasta`).
- Vista limpia de impresión y exportación de pedidos de reposición a proveedores.

---

## 🧪 Pruebas Automatizadas

El proyecto cuenta con una suite completa de pruebas de integración con PHPUnit:

```bash
php artisan test
```

### Cobertura de Pruebas:
- `AuthAndRolesTest`: Autenticación, rechazo de credenciales incorrectas, bloqueo de usuarios inactivos, restricciones 403 para cajeros en módulos administrativos y acceso autorizado para administradores.
- `VentasIntegridadTest`: Transacción atómica, cálculo de vuelto, ventas fiadas, validación de límite de crédito, bloqueo por stock insuficiente, restitución de inventario y reversión de deuda al anular.
- `ClientesYCuentaCorrienteTest`: Carga de clientes con saldo cero, abonos parciales, rechazo de pagos mayores a la deuda y bloqueo de eliminación de clientes con operaciones.
- `ProductosYStockTest`: Carga con stock inicial, ajustes por merma/rotura y bloqueo de eliminación de productos con historial.
- `ReportesYGananciasTest`: Verificación de cálculo de Costo de Mercadería Vendida (CMV), ganancia neta y exclusión de operaciones anuladas.

---

## 📄 Licencia

Este software es de código abierto bajo la licencia [MIT](LICENSE).
