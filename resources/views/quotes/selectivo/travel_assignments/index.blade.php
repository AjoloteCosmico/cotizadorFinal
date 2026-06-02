@extends('adminlte::page')
 
@section('title', 'COTIZADOR')
 
@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop
 
@section('content')
<div class="container w-full bg-white p-3 rounded-xl shadow-xl">
    <div class="row m-3">
        <div class="row bg-white p-4 shadow-lg rounded-lg">
            <div class="col-sm-12">
                <div class="card">
 
                    {!! Form::open(['method'=>'POST','route'=>['selectivo_travel_assignments_general_update'], 'id'=>'form_viaticos']) !!}
                    <input type="hidden" name="Quotation_Id" value="{{ $Quotation_Id }}">
 
                    {{-- ───────────── CARD HEADER ───────────── --}}
                    <div class="card-header">
                        <h3>Viáticos</h3>
                    </div>
 
                    <div class="card-body">
 
                        {{-- ══════════════════════════════════════
                             PASO 1 — Datos generales
                        ══════════════════════════════════════ --}}
                        <div class="form-group mb-4">
                            <x-jet-label value="* Información General" />
                            <p class="text-muted text-xs mb-2">
                                Complete todos los campos para habilitar la captura de viáticos.
                            </p>
 
                            <div class="col-sm-12 table-responsive text-xs">
                                <table class="table align-middle">
                                    <tr>
                                        <td>Días planeados:</td>
                                        <td>
                                            <input class="form-control input-general" name="dias" id="input_dias"
                                                   type="number" step="1" min="1"
                                                   value="{{ $Quotation->dias }}" required>
                                            <x-jet-input-error for='dias' />
                                        </td>
 
                                        <td>Total de posiciones a instalar:</td>
                                        <td>
                                            <input class="form-control input-general" name="npos" id="input_npos"
                                                   type="number" step="1" min="1"
                                                   value="{{ $Quotation->npos }}" required>
                                            <x-jet-input-error for='npos' />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Número de operarios:</td>
                                        <td>
                                            <input class="form-control input-general" name="operarios" id="input_operarios"
                                                   type="number" step="1" min="1"
                                                   value="{{ $Quotation->operarios }}" required>
                                            <x-jet-input-error for='operarios' />
                                        </td>
 
                                        <td>Posiciones que se instalan por día:</td>
                                        <td>
                                            <input class="form-control input-general" name="posxdia" id="input_posxdia"
                                                   type="number" step="1" min="1"
                                                   value="{{ $Quotation->posxdia }}" required>
                                            <x-jet-input-error for='posxdia' />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td><td></td>
                                        <td class="text-muted text-xs">Días sugeridos según posiciones:</td>
                                        <td id="dias_sugeridos" class="font-bold">
                                            @if($Quotation->posxdia > 0)
                                                {{ round($Quotation->npos / $Quotation->posxdia, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
 
                        {{-- ══════════════════════════════════════
                             PASO 2 — Tabla de viáticos
                        ══════════════════════════════════════ --}}
                        <div class="form-group" id="viaticos_section">
 
                            <x-jet-label value="* Viáticos" />
 
                            {{-- Aviso inline cuando la sección está bloqueada --}}
                            <div id="aviso_bloqueo" class="alert alert-warning text-xs py-2 px-3 mb-2" style="display:none;">
                                <i class="fas fa-lock mr-1"></i>
                                Complete los datos generales (días, operarios, posiciones y posiciones por día) para capturar viáticos.
                            </div>
 
                            <div class="w-100 mb-2 text-right">
                                <button id="btn_add_viatico" class="btn btn-green" type="button" onclick="add_viatico()">
                                    <i class="fas fa-plus-circle"></i>&nbsp; Agregar Viático
                                </button>
                            </div>
 
                            <div class="col-sm-12 table-responsive text-xs">
                                <table class="table table-striped align-middle" id="table_viaticos">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Días</th>
                                            <th>Personas</th>
                                            <th>Unidad</th>
                                            <th>Descripción</th>
                                            <th>Costo por operación</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_viaticos">
                                        @foreach ($QuotationTravelAssignments as $row)
                                        <tr class="fila-viatico">
                                            <td>
                                                <input class="form-control fila-dias" name="dia[{{ $loop->index }}]"
                                                       type="number" step="1" min="1" value="{{ $row->dias }}">
                                            </td>
                                            <td>
                                                <input class="form-control fila-operarios" name="operario[{{ $loop->index }}]"
                                                       type="number" step="1" min="1" value="{{ $row->operarios }}">
                                            </td>
                                            <td class="celda-unidad">{{ $row->unit }}</td>
                                            <td>
                                                <select class="form-control fila-descripcion text-xs" name="description[{{ $loop->index }}]"
                                                        data-index="{{ $loop->index }}">
                                                    @foreach ($Descriptions as $d)
                                                        <option value="{{ $d->description }}"
                                                            @if($d->description == $row->description) selected @endif>
                                                            {{ $d->description }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>
                                                    <input class="form-control fila-costo" name="cost[{{ $loop->index }}]"
                                                           type="number" step="0.01" min="0.01"
                                                           value="{{ $row->cost }}" placeholder="0.00">
                                                </div>
                                            </td>
                                            <td class="text-end celda-subtotal">$ {{ number_format($row->import, 2) }}</td>
                                            <td>
                                                <button class="btn btn-danger btn-sm" type="button" onclick="deleteRow(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-end font-bold text-sm">
                                                Total viáticos:
                                            </td>
                                            <td class="text-end font-bold text-sm" id="td_total">
                                                @if($TotalTravelAssignments)
                                                    $ {{ number_format($TotalTravelAssignments, 2) }}
                                                @else
                                                    $ 0.00
                                                @endif
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr id="fila_calculando" style="display:none;">
                                            <td colspan="7" class="text-center text-muted text-xs">
                                                <i class="fas fa-spinner fa-spin mr-1"></i> Calculando total...
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
 
                        {{-- ══════════════════════════════════════
                             BOTÓN GUARDAR
                        ══════════════════════════════════════ --}}
                        <div class="form-group p-2 d-flex align-items-center gap-2">
                            <button id="btn_guardar" type="submit" class="btn btn-green mb-2">
                                <i class="fa-solid fa-save fa-xl"></i>&nbsp; Guardar viáticos
                            </button>
                            <span id="msg_guardar" class="text-xs text-muted ml-2" style="display:none;">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                Corrija los campos marcados antes de guardar.
                            </span>
                        </div>
 
                    </div>{{-- card-body --}}
 
                    {!! Form::close() !!}
                </div>{{-- card --}}
            </div>
        </div>
    </div>
</div>
@stop
 
@push('css')
<style>
    .fila-viatico.fila-invalida td input,
    .fila-viatico.fila-invalida td select {
        border-color: #e3342f !important;
    }
    .fila-viatico.fila-invalida .celda-subtotal {
        color: #e3342f;
    }
    #aviso_bloqueo {
        border-left: 4px solid #f6993f;
    }
    #viaticos_section.bloqueado {
        pointer-events: none;
        opacity: 0.55;
    }
</style>
@endpush
 
@section('js')
{{-- Pasar las descripciones como JSON para el JS --}}
<script>
    window.TYRSA = {
        nviaticos:    {{ $QuotationTravelAssignments->count() }},
        indexviaticos:{{ $QuotationTravelAssignments->count() }},
        descriptions: @json($Descriptions),   {{-- array de objetos con .description --}}
        calcularUrl:  "{{ route('selectivo_travel_assignments_calcular') }}",
        csrfToken:    "{{ csrf_token() }}"
    };
</script>
<script>
    (function () {
    'use strict';
 
    // ─── Referencias DOM ───────────────────────────────────────────────────
    const inputsGenerales = {
        dias:      document.getElementById('input_dias'),
        npos:      document.getElementById('input_npos'),
        operarios: document.getElementById('input_operarios'),
        posxdia:   document.getElementById('input_posxdia'),
    };
    const viaticosSection  = document.getElementById('viaticos_section');
    const avisoBloqueo     = document.getElementById('aviso_bloqueo');
    const btnAdd           = document.getElementById('btn_add_viatico');
    const btnGuardar       = document.getElementById('btn_guardar');
    const msgGuardar       = document.getElementById('msg_guardar');
    const tdTotal          = document.getElementById('td_total');
    const filaCalculando   = document.getElementById('fila_calculando');
    const diasSugeridos    = document.getElementById('dias_sugeridos');
    const formViaticos     = document.getElementById('form_viaticos');
    const tbody            = document.getElementById('tbody_viaticos');
 
    // ─── Estado ────────────────────────────────────────────────────────────
    let { nviaticos, indexviaticos, descriptions, calcularUrl, csrfToken } = window.TYRSA;
    let calcularTimer = null;   // debounce para llamadas AJAX
 
    // ─── 1. Habilitar / deshabilitar sección de viáticos ──────────────────
    function generalesCompletos() {
        return Object.values(inputsGenerales).every(inp => {
            const v = parseFloat(inp.value);
            return Number.isFinite(v) && v > 0;
        });
    }
 
    function actualizarEstadoSeccion() {
        const ok = generalesCompletos();
        if (ok) {
            viaticosSection.classList.remove('bloqueado');
            avisoBloqueo.style.display = 'none';
        } else {
            viaticosSection.classList.add('bloqueado');
            avisoBloqueo.style.display = 'block';
        }
    }
 
    Object.values(inputsGenerales).forEach(inp => {
        inp.addEventListener('input', actualizarEstadoSeccion);
        inp.addEventListener('change', actualizarEstadoSeccion);
    });
 
    // ─── 2. Días sugeridos en tiempo real ─────────────────────────────────
    function actualizarDiasSugeridos() {
        const npos   = parseFloat(inputsGenerales.npos.value);
        const posxdia = parseFloat(inputsGenerales.posxdia.value);
        if (Number.isFinite(npos) && Number.isFinite(posxdia) && npos > 0 && posxdia > 0) {
            const result = npos / posxdia;
            diasSugeridos.textContent = Number.isInteger(result) ? result : result.toFixed(2);
        } else {
            diasSugeridos.textContent = '';
        }
    }
 
    inputsGenerales.npos.addEventListener('input', actualizarDiasSugeridos);
    inputsGenerales.posxdia.addEventListener('input', actualizarDiasSugeridos);
 
    // ─── 3. Calcular total via AJAX (con debounce) ────────────────────────
    function recogerFilas() {
        const filas = [];
        tbody.querySelectorAll('tr.fila-viatico').forEach(tr => {
            const descripcionEl = tr.querySelector('.fila-descripcion');
            const costoEl       = tr.querySelector('.fila-costo');
            const diasEl        = tr.querySelector('.fila-dias');
            const operariosEl   = tr.querySelector('.fila-operarios');
 
            const description = descripcionEl ? descripcionEl.value : '';
            const cost        = parseFloat(costoEl ? costoEl.value : 0);
            const dias        = parseFloat(diasEl ? diasEl.value : 0);
            const operarios   = parseFloat(operariosEl ? operariosEl.value : 0);
 
            filas.push({ description, cost, dias, operarios });
        });
        return filas;
    }
 
    function filaEsCalculable(fila) {
        return fila.description &&
               fila.cost > 0 &&
               Number.isFinite(fila.cost);
    }
 
    async function calcularTotal() {
        const filas = recogerFilas();
        const filasCalculables = filas.filter(filaEsCalculable);
 
        if (filasCalculables.length === 0) {
            tdTotal.textContent = '$ 0.00';
            return;
        }
 
        filaCalculando.style.display = '';
        tdTotal.textContent = '...';
 
        try {
            const response = await fetch(calcularUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ filas }),
            });
 
            if (!response.ok) throw new Error('Error de red');
 
            const data = await response.json();
 
            // Actualizar subtotales por fila
            const trList = tbody.querySelectorAll('tr.fila-viatico');
            if (data.filas) {
                data.filas.forEach((resultado, i) => {
                    if (trList[i]) {
                        const celdaSubtotal = trList[i].querySelector('.celda-subtotal');
                        if (celdaSubtotal) {
                            celdaSubtotal.textContent = resultado.import != null
                                ? '$ ' + formatearNumero(resultado.import)
                                : '—';
                        }
                    }
                });
            }
 
            // Actualizar total general
            tdTotal.textContent = data.total != null
                ? '$ ' + formatearNumero(data.total)
                : '$ 0.00';
 
        } catch (e) {
            tdTotal.textContent = 'Error al calcular';
            console.error('AJAX calcular total:', e);
        } finally {
            filaCalculando.style.display = 'none';
        }
    }
 
    function dispararCalculoConDebounce() {
        clearTimeout(calcularTimer);
        calcularTimer = setTimeout(calcularTotal, 600);
    }
 
    // ─── 4. Eventos de la tabla (delegación al tbody) ─────────────────────
    tbody.addEventListener('change', function (e) {
        const tr = e.target.closest('tr.fila-viatico');
        if (!tr) return;
 
        if (e.target.classList.contains('fila-descripcion')) {
            // Al cambiar descripción, actualizar celda de unidad desde el array local
            const desc   = descriptions.find(d => d.description === e.target.value);
            const celda  = tr.querySelector('.celda-unidad');
            if (celda) celda.textContent = desc ? desc.unit : '';
            // No disparar cálculo aún — esperamos que el usuario ingrese el costo
        }
 
        if (e.target.classList.contains('fila-dias') ||
            e.target.classList.contains('fila-operarios')) {
            // Solo calcular si ya hay costo en esa fila
            const costoEl = tr.querySelector('.fila-costo');
            if (costoEl && parseFloat(costoEl.value) > 0) {
                dispararCalculoConDebounce();
            }
        }
    });
 
    tbody.addEventListener('blur', function (e) {
        if (e.target.classList.contains('fila-costo')) {
            const val = parseFloat(e.target.value);
            if (Number.isFinite(val) && val > 0) {
                dispararCalculoConDebounce();
            }
        }
    }, true /* useCapture para blur */);
 
    // ─── 5. Agregar fila nueva ────────────────────────────────────────────
    window.add_viatico = function () {
        const idx = indexviaticos;
        const tr  = document.createElement('tr');
        tr.classList.add('fila-viatico');
 
        // Construir opciones del select
        const opciones = descriptions
            .map(d => `<option value="${escHtml(d.description)}">${escHtml(d.description)}</option>`)
            .join('');
 
        tr.innerHTML = `
            <td>
                <input class="form-control fila-dias" name="dia[${idx}]"
                       type="number" step="1" min="1" placeholder="">
            </td>
            <td>
                <input class="form-control fila-operarios" name="operario[${idx}]"
                       type="number" step="1" min="1" placeholder="">
            </td>
            <td class="celda-unidad"></td>
            <td>
                <select class="form-control fila-descripcion text-xs" name="description[${idx}]" data-index="${idx}">
                    ${opciones}
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input class="form-control fila-costo" name="cost[${idx}]"
                           type="number" step="0.01" min="0.01" placeholder="0.00">
                </div>
            </td>
            <td class="text-end celda-subtotal">—</td>
            <td>
                <button class="btn btn-danger btn-sm" type="button" onclick="deleteRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
 
        tbody.appendChild(tr);
        nviaticos++;
        indexviaticos++;
 
        // Enfocar el select de descripción de la nueva fila
        tr.querySelector('.fila-descripcion').focus();
 
        // Actualizar unidad inicial con la primera descripción disponible
        if (descriptions.length > 0) {
            const celdaUnidad = tr.querySelector('.celda-unidad');
            if (celdaUnidad) celdaUnidad.textContent = descriptions[0].unit || '';
        }
    };
 
    // ─── 6. Eliminar fila ─────────────────────────────────────────────────
    window.deleteRow = function (btn) {
        const tr = btn.closest('tr');
        if (tr) {
            tr.remove();
            nviaticos--;
            dispararCalculoConDebounce();
        }
    };
 
    // ─── 7. Validación antes de guardar ───────────────────────────────────
    formViaticos.addEventListener('submit', function (e) {
        let hayErrores = false;
 
        tbody.querySelectorAll('tr.fila-viatico').forEach(tr => {
            const costoEl     = tr.querySelector('.fila-costo');
            const descripEl   = tr.querySelector('.fila-descripcion');
            const costo       = parseFloat(costoEl ? costoEl.value : 0);
            const descripcion = descripEl ? descripEl.value.trim() : '';
 
            const filaInvalida = !descripcion || !Number.isFinite(costo) || costo <= 0;
 
            if (filaInvalida) {
                tr.classList.add('fila-invalida');
                hayErrores = true;
            } else {
                tr.classList.remove('fila-invalida');
            }
        });
 
        if (hayErrores) {
            e.preventDefault();
            msgGuardar.style.display = '';
            // Hacer scroll al primer error
            const primera = tbody.querySelector('.fila-invalida');
            if (primera) primera.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            msgGuardar.style.display = 'none';
        }
    });
 
    // ─── 8. Utilidades ────────────────────────────────────────────────────
    function formatearNumero(n) {
        return Number(n).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
 
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
 
    // ─── Inicializar estado al cargar ─────────────────────────────────────
    actualizarEstadoSeccion();
    actualizarDiasSugeridos();
 
})();
</script>
@stop