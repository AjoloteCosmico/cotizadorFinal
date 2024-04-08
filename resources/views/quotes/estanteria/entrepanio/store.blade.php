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
                    <h2><i class="fa-solid fa-crop-simple"></i>&nbsp;ENTREPAÑO {{$QuotEnt->type}} ESTANTERIA</h2>
                    <span>Los datos de su cotización se muestran a continuación.</span>
                    <div class="card-body text-center">
                        <div class="container">
                            <img src="{{asset('vendor/img/postes/logo.png')}}" class="img-thumbnail img-fluid max-h-80 rounded mx-auto d-block" alt="">
                              
                            <div class="row mt-2 flex-col items-center">
                                {!! DNS1D::getBarcodeHTML($Ent->sku, "C128",2,30) !!}
            
                                {{$QuotEnt->sku}}
                            
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
                                <td>sku: {{$QuotEnt->sku}}</td>
                                <td>cantidad: {{$QuotEnt->amount}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>largo: {{$QuotEnt->length}}</td>
                                <td>peso: {{$QuotEnt->peso}}</td>
                            </tr>
                            <tr class="text-right">
                                <td>KG/M2: {{$QuotEnt->kgm2}}</td>
                                <td>calibre: {{$QuotEnt->caliber}} </td>
                            </tr>
                            <tr class="text-right">
                                <td>Capacidad de carga: 
                                    @if($QuotEnt->refuerzos==0) {{$QuotEnt->loading_capacity}} @endif
                                    @if($QuotEnt->refuerzos==1) {{$QuotEnt->reforcement3}} @endif
                                    @if($QuotEnt->refuerzos==2) {{$QuotEnt->reforcement2}} @endif
                                </td>
                                <td>Refuerzos: {{$QuotEnt->refuerzos}} </td>
                            </tr>
                            
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="2">Costo x Unidad: ${{number_format($QuotEnt->unit_price, 2)}}</td>
                            </tr>
                            @if($QuotEnt->refuerzos>0)
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="2">Costo x Refuerzo: ${{number_format($PrecioEscuadra, 2)}}</td>
                            </tr>
                            @endif
                            <tr class="font-bold text-right text-1xl">
                                <td colspan="3">Costo Total: ${{number_format($QuotEnt->total_price+$PrecioEscuadra*$QuotEnt->refuerzos, 2)}}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="form-group p-2">
                    {!! Form::open(['method'=>'POST','route'=>['entrepanios.update_reforcement']]) !!}
                    <span>Desea Actualizar la cantidad de refuerzos?</span>
                    <br>
                       <input type="hidden" name="Quotation_Id" value="{{$QuotEnt->quotation_id}}">
                        <x-jet-label value="* Cantidad de refuerzos" />
                        <select name="amount" class="inputjet w-full text-xs uppercase">
                         
                            <option value="0" @if($QuotEnt->refuerzos==0) selected @endif>0</option>
                            <option value="1" @if($QuotEnt->refuerzos==1) selected @endif>1</option>
                            <option value="2" @if($QuotEnt->refuerzos==2) selected @endif>2</option>
                          
                        </select>
                        <x-jet-input-error for='amount' /><br>
                        
                        <button type="submit" class="btn btn-blue mb-2"> Actualziar refuerzos</button>
                        {!! Form::close() !!}
                    </div>
                    <div class="form-group p-2 gap-2 flex items-center">
                        <a href="{{route('entrepanios.index', [$QuotEnt->quotation_id,$Ent->type])}}" class="btn btn-blue mb-2">
                            <i class="fa-solid fa-right-left fa-xl"></i>&nbsp; Corregir
                        </a>
                        <a href="{{route('entrepanios.add_carrito', [$QuotEnt->quotation_id,$QuotEnt->type])}}" class="btn btn-black mb-2">
                            <i class="fa-solid fa-rotate-left fa-xl"></i>&nbsp; Guardar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop