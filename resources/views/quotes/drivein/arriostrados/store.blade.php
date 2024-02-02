@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop

@section('content')
    <div class="container w-full bg-white p-3 rounded-xl shadow-xl">
        <div class="row m-3">
            <div  class="row bg-white p-4 shadow-lg rounded-lg">
                <div class="col-sm-6 col-xs-12">
                    <h2><i class="fa-solid fa-crop-simple"></i>&nbsp;ARRIOSTRADOS DRIVE IN</h2>
                    <span>Los datos de su cotización se muestran a continuación.</span>
                    <div class="card-body text-center">
                        <div class="container">
                            <img src="{{asset('vendor/img/postes/logo.png')}}" class="img-thumbnail img-fluid max-h-80 rounded mx-auto d-block" alt="">
                            @if($Rolados->amount>0)
                            <div class="row mt-2 flex-col items-center">
                                {!! DNS1D::getBarcodeHTML($Rolados->sku, "C128",2,30) !!}
                                {{$Rolados->sku}} 
                            </div>
                            @endif
                            <br>
                            @if($Estructurales->amount>0)
                            <div class="row mt-2 flex-col items-center">
                                 {!! DNS1D::getBarcodeHTML($Estructurales->sku, "C128",2,30) !!}
                                {{$Estructurales->sku}} 
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group p-2 text-sm font-semibold table-responsive">
                        <table class="table">
                            <tr class="text-center">
                                <th colspan="2">Datos de Cotización</th>
                            </tr>
                            
                            @if($Rolados->amount>0)
                            <tr class="text-right">
                                <td>Cantidad: {{$Rolados->amount}}</td>
                                <td>Tipo: {{$Rolados->description}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>Largo: {{$Rolados->length}}</td>
                                <td>Peso por pieza: {{$Rolados->piece_weight}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>Desarrollo: {{$Rolados->desarrollo}}</td>
                                <td>M2: {{$Rolados->m2}}</td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="2">Costo x Unidad: ${{number_format($Rolados->unit_price, 2)}}</td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="3">Costo Total: ${{number_format($Rolados->total_price, 2)}}</td>
                            </tr> @endif
                        </table>
                        <br>
                            @if($Estructurales->amount>0)
                        <table class="table">
                            
                            <tr class="text-right">
                                <td>Cantidad: {{$Estructurales->amount}}</td>
                                <td>Tipo: {{$Estructurales->description}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>Largo: {{$Estructurales->length}}</td>
                                <td>Peso por pieza: {{$Estructurales->piece_weight}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>Desarrollo: {{$Estructurales->desarrollo}}</td>
                                <td>M2: {{$Estructurales->m2}}</td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="2">Costo x Unidad: ${{number_format($Estructurales->unit_price, 2)}}</td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="3">Costo Total: ${{number_format($Estructurales->total_price, 2)}}</td>
                            </tr>
                        </table>@endif

                        <h1>
                            PRECIO TOTAL : ${{number_format($Estructurales->total_price + $Rolados->total_price, 2)}}
                        </h1>
                    </div>
                    <div class="form-group p-2 gap-2 flex items-center">
                        <a href="{{route('drivein.index', $Rolados->quotation_id)}}" class="btn btn-blue mb-2">
                            <i class="fa-solid fa-right-left fa-xl"></i>&nbsp; Corregir
                        </a>
                        <a href="{{route('drive_in_arriostrados.add_carrito', $Rolados->quotation_id)}}" class="btn btn-black mb-2">
                            <i class="fa-solid fa-rotate-left fa-xl"></i>&nbsp; Guardar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop