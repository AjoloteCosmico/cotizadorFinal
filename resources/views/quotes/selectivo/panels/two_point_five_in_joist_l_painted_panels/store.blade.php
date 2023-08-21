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
                    <h2><i class="fa-solid fa-crop-simple"></i>&nbsp;PANELES PINTADOS VIGA L DE 2.5 IN</h2>
                    <span>Los datos de su cotización se muestran a continuación.</span>
                    <div class="card-body text-center">
                        <div class="container">
                            <img src="{{asset('vendor/img/postes/logo.png')}}" class="img-thumbnail img-fluid max-h-80 rounded mx-auto d-block" alt="">
                            <div class="row mt-2 flex-col items-center">
                                {!! DNS1D::getBarcodeHTML($Sku, "C128",2,30) !!}
                                {{$Sku}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group p-2 text-sm font-semibold table-responsive">
                        <table class="table">
                            <tr class="text-center">
                                <th colspan="2">Datos de Cotización</th>
                            </tr>
                            <tr class="text-right">
                                <td>Cantidad: {{$Amount}}</td>
                                <td>Calibre: {{$Caliber}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>Fondo: {{$FrameBackgrounds}}</td>
                                <td>Largo: {{$LengthDimension}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>Peso: {{$Weight}}</td>
                                <td>Peso Total: {{$TotalWeight}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>m<sup>2</sup> {{$M2}}</td>
                                
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="2">Costo x Unidad: ${{number_format($PriceUnit, 2)}}</td>
                            </tr>
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="3">Costo Total: ${{number_format($TotalPrice, 2)}}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="form-group p-2 gap-2 flex items-center">
                        <a href="{{route('selectivo_two_point_five_in_joist_l_painted_panels', $Quotation_Id)}}" class="btn btn-blue mb-2">
                            <i class="fa-solid fa-right-left fa-xl"></i>&nbsp; Corregir
                        </a>
                        <a href="{{route('selectivo_panels', $Quotation_Id)}}" class="btn btn-black mb-2">
                            <i class="fa-solid fa-rotate-left fa-xl"></i>&nbsp; Guardar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop