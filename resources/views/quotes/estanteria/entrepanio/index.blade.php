@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop

@section('content')
    <div class="container w-full bg-white p-3 rounded-xl shadow-xl">
        <div class="row m-3">
            {!! Form::open(['method'=>'POST','route'=>['entrepanios.store',$Quotation_Id]]) !!}
            <input type="hidden" name="Quotation_Id" value="{{$Quotation_Id}}">
            <input type="hidden" name="type" value="{{$Type}}">
            
            <div  class="row bg-white p-4 shadow-lg rounded-lg">
                <div class="col-sm-6 col-xs-12">
                    <h2><i class="fa-solid fa-crop-simple"></i>&nbsp;ENTREÁÑO PINTADO PAPRA ESTANTERIA</h2>
                    <span>Favor de Seleccionar y llenar los campos solicitados para realizar la cotización.</span>
                    <div class="card-body text-center">
                        <img src="{{asset('vendor/img/postes/logo.png')}}" class="img-thumbnail img-fluid max-h-80 rounded mx-auto d-block" alt="">
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                <div class="form-group p-2">
                        <x-jet-label value="* Cantidad" />
                        <input type="number" name="amount" class="inputjet w-full text-xs uppercase" value="{{ old('amount') }}" />
                        <x-jet-input-error for='amount' /><br>

                        <x-jet-label value="* Seleccione el Calibre" />
                        <select name="caliber" class="inputjet w-full text-xs uppercase">
                          @foreach($Calibres as $c)
                            <option value="{{$c->caliber}}" @if(old('caliber')==$c->caliber) selected @endif>{{$c->caliber}}</option>
                          @endforeach
                        </select>
                        <x-jet-input-error for='caliber' /><br>
                    
                        <x-jet-label value="* Seleccione el Largo" />
                        <select name="length" class="inputjet w-full text-xs uppercase">
                            @foreach($Largos as $l)
                             <option value="{{$l->length}}" @if(old('length')==$l->length) selected @endif>{{$l->length}}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for='length' /><br>
                    
                        <x-jet-label value="* Seleccione el Fondo" />
                        <select name="deep" class="inputjet w-full text-xs uppercase">
                          @foreach($Fondos as $f)
                            <option value="{{$f->deep}}" @if(old('deep')==$f->deep) selected @endif>{{$f->deep}}</option>
                          @endforeach
                        </select>
                        <x-jet-input-error for='deep' /><br>
                    
                    </div>
                    
                    <div class="form-group p-2 gap-2 flex items-center">
                        <button type="submit" class="btn btn-blue mb-2">
                            <i class="fa-solid fa-calculator fa-xl"></i>&nbsp; Calcular
                        </button>
                        <a href="{{route('selectivo.show', $Quotation_Id)}}" class="btn btn-black mb-2">
                            <i class="fa-solid fa-rotate-left fa-xl"></i>&nbsp; Cancelar
                        </a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('js')
    @if (session('no_existe') == 'ok')
        <script type="text/javascript" src="{{ asset('vendor/mystylesjs/js/no_existe.js') }}"></script>
    @endif
@stop