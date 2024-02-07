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
                    <h2><i class="fa-solid fa-crop-simple"></i>&nbsp;BRAZOS DRIVE IN</h2>
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
                            @if($Est3In->amount>0)
                            <div class="row mt-2 flex-col items-center">
                                 {!! DNS1D::getBarcodeHTML($Est3In->sku, "C128",2,30) !!}
                                {{$Est3In->sku}} 
                            </div>
                            @endif
                            <br>
                            @if($Est4In->amount>0)
                            <div class="row mt-2 flex-col items-center">
                                 {!! DNS1D::getBarcodeHTML($Est4In->sku, "C128",2,30) !!}
                                {{$Est4In->sku}} 
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
                                <td>M2: {{$Rolados->m2}}</td>
                                <td>Peso por pieza: {{$Rolados->piece_weight}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>Piezas: {{$Rolados->piezas_nec}}</td>
                                <td></td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="2">Costo x Unidad: ${{number_format($Rolados->unit_price, 2)}}</td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="3">Costo Total: ${{number_format($Rolados->total_price, 2)}}</td>
                            </tr> @endif
                        </table>
                        <br>
                            @if($Est3In->amount>0)
                        <table class="table">
                            
                            <tr class="text-right">
                                <td>Cantidad: {{$Est3In->amount}}</td>
                                <td>Tipo: {{$Est3In->description}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>M2: {{$Est3In->m2}}</td>
                                <td>Peso por pieza: {{$Est3In->piece_weight}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>Piezas: {{$Est3In->piezas_nec}}</td>
                                <td></td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="2">Costo x Unidad: ${{number_format($Est3In->unit_price, 2)}}</td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="3">Costo Total: ${{number_format($Est3In->total_price, 2)}}</td>
                            </tr>
                        </table>@endif
                        @if($Est4In->amount>0)
                        <table class="table">
                            
                            <tr class="text-right">
                                <td>Cantidad: {{$Est4In->amount}}</td>
                                <td>Tipo: {{$Est4In->description}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>M2: {{$Est4In->m2}}</td>
                                <td>Peso por pieza: {{$Est4In->piece_weight}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>Piezas: {{$Est4In->piezas_nec}}</td>
                                <td></td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="2">Costo x Unidad: ${{number_format($Est4In->unit_price, 2)}}</td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="3">Costo Total: ${{number_format($Est4In->total_price, 2)}}</td>
                            </tr>
                        </table>@endif

                        <h1>
                            PRECIO TOTAL : ${{number_format($Est4In->total_price + $Est3In->total_price + $Rolados->total_price, 2)}}
                        </h1>
                    </div>
                    <div class="form-group p-2 gap-2 flex items-center">
                        <a href="{{route('drive_in_brazos.index', $Rolados->quotation_id)}}" class="btn btn-blue mb-2">
                            <i class="fa-solid fa-right-left fa-xl"></i>&nbsp; Corregir
                        </a>
                        <a href="{{route('drive_in_brazos.add_carrito', $Rolados->quotation_id)}}" class="btn btn-black mb-2">
                            <i class="fa-solid fa-rotate-left fa-xl"></i>&nbsp; Guardar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop