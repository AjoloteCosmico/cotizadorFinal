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
                                        <td> <input class="form-control" name="dias" type="number" step="1" value="{{$Quotation->dias}}" >
                                        <x-jet-input-error for='dias' /></td>
                                        
                                        <td>Posiciones a armar: </td>
                                        <td> <input class='form-control'  name ='npos' type="number" step='1' value='{{$Quotation->npos}}' > 
                                        <x-jet-input-error for='npos' /></td>
                                    </tr>
                                    <tr>
                                        <td>Operarios: </td>
                                        <td><input class='form-control'  name ='operarios' type="number" step='1' value='{{$Quotation->operarios}}' > 
                                        <x-jet-input-error for='operarios' /></td>
                                        
                                        <td>Posiciones por dia: </td>
                                        <td><input class='form-control'  name ='posxdia' type="number" step='1' value='{{$Quotation->posxdia}}' >
                                        <x-jet-input-error for='posxdia' /> </td>
                                    </tr>
                                </table>
                                </div>
                    

                                </div>
                                <div class="form-group">
                                    <x-jet-label value="* Viáticos" />
                                    
                                    <div class="w-100">&nbsp;</div>
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
                                    <div class="col-sm-12 text-right">
                                    <button class="btn btn-green" onclick='add_viatico()' type="button">   
                                        <i class="fas fa-plus-circle"></i>&nbsp; Agregar Viáticos
                                    </button>
                                    </div>
                                </div>
                                <div class="form-group p-2 gap-2 flex items-center">
                            <button class="btn btn-blue mb-2">
                                <i class="fa-solid fa-calculator fa-xl"></i>&nbsp; Calcular
                            </button>
                        </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                        <div class="form-group p-2 gap-2 flex items-center">
                            <a href="{{route('selectivo_quotation_travel_assignments.add_carrito', $Quotation_Id)}}" class="btn btn-black mb-2">
                                <i class="fa-solid fa-save fa-xl"></i>&nbsp; Guardar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script type="text/javascript" src="{{ asset('vendor/mystylesjs/js/tabletravelassignments.js') }}"></script>

<script>
    var nviaticos=0+{{$QuotationTravelAssignments->count()}};
    var indexviaticos=0+{{$QuotationTravelAssignments->count()}}
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
        cell0.innerHTML = "<input class='form-control'  name ='dia["+indexviaticos+"]' type='number' step='1'  >";
        cell1.innerHTML = "<input class='form-control'  name ='operario["+indexviaticos+"]' type='number' step='1' >";
        cell2.innerHTML = " ";
        cell3.innerHTML = "<select class='form-capture uppercase w-full text-xs' name='description["+indexviaticos+"]'> \
                            @foreach ($Descriptions as $d) \
                                <option value='{{$d->description}}' >{{$d->description}}</option> \
                            @endforeach \
                        </select>";
        cell4.innerHTML = "<input class='form-control'  name ='cost["+indexviaticos+"]' type='number'> ";
        cell6.innerHTML = "<button class='btn btn-danger' type='button' onclick='deleteRow(this)'> <i class='fas fa-trash'></i> </button> ";

        nviaticos+=1;
        indexviaticos+=1;
        console.log(nviaticos);
    }

   
</script>
@stop