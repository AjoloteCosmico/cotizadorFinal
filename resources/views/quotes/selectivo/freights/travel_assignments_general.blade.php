@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop

@section('content')
    <div class="container w-full bg-white p-3 rounded-xl shadow-xl">
        <div class="row m-3">
            <div  class="row p-4">
                {!! Form::open(['method'=>'POST','route'=>['selectivo_travel_assignments_general_update']]) !!}
                <div class="col-xs-12 p-2 gap-2">
                    <input type="hidden" name="Quotation_Id" value="{{$Quotation_Id}}">
                    <div class="form-group">
                        <x-jet-label value="* Dias" />
                        <x-jet-input type="number" name="dias" value="{{$Quotation->dias}}" class="text-xs uppercase"/>
                        <x-jet-input-error for='dias' />
                    </div>
                    <div class="form-group">
                        <x-jet-label value="* Numero de operarios" />
                        <x-jet-input type="number" name="operarios" value="{{$Quotation->operarios}}" class="text-xs uppercase"/>
                        <x-jet-input-error for='operarios' />
                    </div>
                    <div class="form-group">
                        <x-jet-label value="* Cantidad de posiciones" />
                        <x-jet-input type="number" name="npos" value="{{$Quotation->npos}}" class="text-xs uppercase"/>
                        <x-jet-input-error for='npos' />
                    </div>
                    <div class="form-group">
                        <x-jet-label value="* Posiciones por dia " />
                        <x-jet-input type="number" name="posxdia" value="{{$Quotation->posxdia}}" class="text-xs uppercase"/>
                        <x-jet-input-error for='posxdia' />
                    </div>
                </div>
                <div class="col-12 text-right p-2 gap-2">
                    <a href="{{ route('selectivo_quotation_travel_assignments', $Quotation_Id)}}" class="btn btn-green mb-2">
                        <i class="fas fa-times fa-2x"></i>&nbsp;&nbsp; Cancelar
                    </a>
                    <button type="submit" class="btn btn-red mb-2">
                        <i class="fas fa-save fa-2x"></i>&nbsp; &nbsp; Guardar
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop

@section('js')

@stop