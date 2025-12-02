@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop

@section('content')
    <div class="container w-full bg-white p-3 rounded-xl shadow-xl">
        <div class="row m-3">
            <div  class="row bg-white p-4 shadow-lg rounded-lg">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                        {!! Form::open(['method'=>'POST','route'=>['selectivo_travel_assignments_general_update']]) !!}
                        <input type="hidden" name="Quotation_Id" value="{{$Quotation_Id}}">
                   
                            <div class="card-header">
                                <h3>Viáticos</h3>
                            </div>
                            <div class="card-body">
                                
                                <div class="form-group">
                                    
                                <x-jet-label value="* Información General" />
                                
                                <div class="col-sm-12 table-responsive text-xs">
                                <table class="table align-middle">
                                    <tr>
                                        <td>Dias: </td>
                                        <td> <input class="form-control" name="dias" type="number" step="1" value="{{$Quotation->dias}}" required >
                                        <x-jet-input-error for='dias' /></td>
                                        
                                        <td>Posiciones a armar: </td>
                                        <td> <input class='form-control'  name ='npos' type="number" step='1' value='{{$Quotation->npos}}' required > 
                                        <x-jet-input-error for='npos' /></td>
                                    </tr>
                                    <tr>
                                        <td>Operarios: </td>
                                        <td><input class='form-control'  name ='operarios' type="number" step='1' value='{{$Quotation->operarios}}' required > 
                                        <x-jet-input-error for='operarios' /></td>
                                        
                                        <td>Posiciones por dia: </td>
                                        <td><input class='form-control'  name ='posxdia' type="number" step='1' value='{{$Quotation->posxdia}}' required >
                                        <x-jet-input-error for='posxdia' /> </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>Dias Sugeridos:</td>
                                        <td id="dias_sugeridos"> 
                                        @if($Quotation->posxdia>0)        
                                            {{$Quotation->npos / $Quotation->posxdia}}
                                        @endif
                                        </td>
                                    </tr>
                                </table>
                                </div>
                    

                                </div>
                                <div class="form-group" id="viaticos_section">
                                    <x-jet-label value="* Viáticos" />
                                    
                                    <div class="w-100">&nbsp;</div>
                                     <div class="col-sm-12 text-right">
                                    <button id="btn_add_viatico" class="btn btn-green" onclick='add_viatico()' type="button">   
                                        <i class="fas fa-plus-circle"></i>&nbsp; Agregar Viáticos
                                    </button>
                                    </div>
                                    <div class="col-sm-12 table-responsive text-xs">
                                        <table class="table tabletravelassignments table-striped align-middle" id='table_viaticos'>
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Dias</th>
                                                    <th>Personas</th>
                                                    <th>Unidad</th>
                                                    <th>Descripcion</th>
                                                    <th>Costo por Operacion </th>
                                                    <th>Costo total</th>
                                                    <th> </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($QuotationTravelAssignments as $row)
                                                <tr>
                                                    <td><input class='form-control'  name ='dia[{{$loop->index}}]' type="number" pattern="[0-9]" step='1' value='{{$row->dias}}' > </td>
                                                    <td><input class='form-control'  name ='operario[{{$loop->index}}]' type="number" step='1' value='{{$row->operarios}}' > </td>
                                                    <td>{{$row->unit}}</td>
                                                    <td><select class="form-capture uppercase w-full text-xs" name="description[{{$loop->index}}]">
                            @foreach ($Descriptions as $d)
                                <option value="{{$d->description}}" @if ($d->description == $row->description) selected @endif >{{$d->description}}</option>
                            @endforeach
                        </select></td>
                                                    <td class="text-end" style="white-space: nowrap;">
                                                        <table>
                                                            <tr>
                                                                <td>$</td>
                                                                <td>  <input class='form-control'  name ='cost[{{$loop->index}}]' type="number"  value='{{$row->cost}}' onchange='change_cost({{$loop->index}})' > 
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        </td>
                                                    </td>
                                                    <td class="text-end"> $ {{$row->import}} </td>
                                                    </td><td> <button class="btn btn-danger" type="button" onclick="deleteRow(this)"> <i class="fas fa-trash"></i> </button>    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" class="text-end font-bold text-sm">
                                                        $ @if ($TotalTravelAssignments <> "")
                                                        {{number_format($TotalTravelAssignments,2)}}
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                   
                                </div>
                                <div class="form-group p-2 gap-2 flex items-center">
                            <button id="btn_calcular" class="btn btn-blue mb-2">
                                <i class="fa-solid fa-calculator fa-xl"></i>&nbsp; Calcular Total
                            </button>
                        </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        
                        <div class="form-group p-2 gap-2 flex items-center">
                            <button  class="btn btn-green mb-2"  @if( $Quotation->dias == 0) data-toggle="tooltip" data-placement="top" title="Calcule el total antes de guardar" disabled @endif>
                                <a   id="save_btn" href="{{route('selectivo_quotation_travel_assignments.add_carrito', $Quotation_Id)}}" class="btn btn-green mb-2" >
                                <i class="fa-solid fa-save fa-xl"></i>&nbsp; Guardar
                            </a> </button>
                            
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- mensaje flotante que aparece cuando faltan datos -->
                                <div id="floating_message" class="floating-message" role="alert" aria-live="assertive" style="display:none">
                                    Llene los datos de posiciones, dias, operarios y posiciones por día para continuar
                                </div>
                                <div id="floating_pos" class="floating-message" role="alert" aria-live="assertive" style="display:none">
                                    Se han detectado cambios en las posiciones, calcule el total antes de guardar en el carrito.
                                </div>
@stop
 @push('css')
  <style>
    .floating-message{
        position: fixed;
        right: 20px;
        top: 10%;
        background: #427ae8ff; /* rojo claro */
        color: #fff;
        padding: 10px 14px;
        border-radius: 8px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
        z-index: 9999;
        font-weight: 600;
        font-size: 16px;
    }
</style>
 @endpush


@section('js')
<script type="text/javascript" src="{{ asset('vendor/mystylesjs/js/tabletravelassignments.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>

<script>
    var nviaticos=0+{{$QuotationTravelAssignments->count()}};
    var indexviaticos=0+{{$QuotationTravelAssignments->count()}}
    var btnSave = document.getElementById('save_btn');
    console.log(nviaticos);
    function deleteRow(r) {
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("table_viaticos").deleteRow(i);
        nviaticos-=1;
        }
    function add_viatico(){
        
        var table = document.getElementById('table_viaticos');
        var row = table.insertRow(nviaticos+1);
        var cell0 = row.insertCell(0);
        var cell1 = row.insertCell(1);
        var cell2 = row.insertCell(2);
        var cell3 = row.insertCell(3);
        var cell4 = row.insertCell(4);
        var cell5 = row.insertCell(5);
        var cell6 = row.insertCell(6);
        cell0.innerHTML = "<input class='form-control'  name ='dia["+indexviaticos+"]' type='number' step='1'  required >";
        cell1.innerHTML = "<input class='form-control'  name ='operario["+indexviaticos+"]' type='number' step='1 'required  >";
        cell2.innerHTML = " ";
        cell3.innerHTML = "<select class='form-capture uppercase w-full text-xs' name='description["+indexviaticos+"]' required  > \
                            @foreach ($Descriptions as $d) \
                                <option value='{{$d->description}}' >{{$d->description}}</option> \
                            @endforeach \
                        </select>";
        cell4.innerHTML = "<input class='form-control'  name ='cost["+indexviaticos+"]' type='number' required > ";
        cell6.innerHTML = "<button class='btn btn-danger' type='button' onclick='deleteRow(this)'> <i class='fas fa-trash'></i> </button> ";

        nviaticos+=1;
        indexviaticos+=1;
        btnSave.removeAttribute('href');
        btnSave.disabled=true;
        const floatingPos = document.getElementById('floating_pos');
        if (floatingPos) {
            floatingPos.style.display = 'block';
        }
        console.log(nviaticos);
    }

 @if( $Quotation->dias == 0) 
     
    document.getElementById('save_btn').removeAttribute('href');
    document.getElementById('save_btn').disabled=true;
     @endif  
</script>

<script>
    // actualiza el td#dias_sugeridos cuando cambian npos o posxdia
    (function(){
        const nposInput = document.querySelector('input[name="npos"]');
        const posxdiaInput = document.querySelector('input[name="posxdia"]');
        const diasTd = document.getElementById('dias_sugeridos');

        if (!nposInput || !posxdiaInput || !diasTd) return;

        function updateDiasSugeridos(){
            const npos = parseFloat(nposInput.value);
            const posxdia = parseFloat(posxdiaInput.value);

            if (Number.isFinite(npos) && Number.isFinite(posxdia) && npos > 0 && posxdia > 0) {
                const result = npos / posxdia;
                // mostrar con hasta 2 decimales si no es entero
                diasTd.innerText = Number.isInteger(result) ? result : result.toFixed(2);
            } else {
                diasTd.innerText = '';
            }
        }

        nposInput.addEventListener('input', updateDiasSugeridos);
        posxdiaInput.addEventListener('input', updateDiasSugeridos);
        // inicializar al cargar
        updateDiasSugeridos();
    })();
</script>

<script>
(function(){
    const inputs = {
        dias: document.querySelector('input[name="dias"]'),
        npos: document.querySelector('input[name="npos"]'),
        posxdia: document.querySelector('input[name="posxdia"]'),
        operarios: document.querySelector('input[name="operarios"]')
    };
    const viaticosSection = document.getElementById('viaticos_section');
    const btnAdd = document.getElementById('btn_add_viatico');
    const btnCalcular = document.getElementById('btn_calcular');
    const tableViaticos = document.getElementById('table_viaticos');
    const floatingMessage = document.getElementById('floating_message');

    if (!inputs.dias || !inputs.npos || !inputs.posxdia || !inputs.operarios || !viaticosSection) return;

    function setViaticosEnabled(enabled){
        // visual
        viaticosSection.style.pointerEvents = enabled ? 'auto' : 'none';
        viaticosSection.style.opacity = enabled ? '1' : '0.6';
        // botones
        if (btnAdd) btnAdd.disabled = !enabled;
        if (btnCalcular) btnCalcular.disabled = !enabled;
        // inputs/selects/buttons inside viaticos section (table and controls)
        const controls = viaticosSection.querySelectorAll('input, select, button, textarea');
        controls.forEach(el=>{
            // don't disable the calculate or add buttons twice (they are handled)
            if (el === btnAdd || el === btnCalcular) return;
            // allow leaving existing readonly/display-only elements untouched
            try { el.disabled = !enabled; } catch(e){}
        });
        if (floatingMessage) {
            floatingMessage.style.display = enabled ? 'none' : 'block';
        }
    }

    function allValidGreaterThanZero(){
        return ['dias','npos','posxdia','operarios'].every(key=>{
            const v = parseFloat(inputs[key].value);
            return Number.isFinite(v) && v > 0;
        });
    }

    function updateState(){
        setViaticosEnabled(allValidGreaterThanZero());
    }

    // listen changes
    Object.values(inputs).forEach(inp=>{
        inp.addEventListener('input', updateState);
        inp.addEventListener('change', updateState);
    });

    // initialize on load
    updateState();
     
})();
</script>
@stop