@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop

@section('content')
    <div class="container w-full bg-white p-3 rounded-xl shadow-xl">
        <div class="row m-3">
            <div  class="row p-4">
                {!! Form::open(['method'=>'POST','route'=>['selectivo_travel_assignments_add']]) !!}
                <div class="col-xs-12 p-2 gap-2">
                    <input type="hidden" name="Quotation_Id" value="{{$Quotation_Id}}">
                    <div class="form-group">
                        <x-jet-label value="* Cantidad de dias" />
                        <x-jet-input type="number" name="dias" value="{{old('dias')}}" class="text-xs uppercase"/>
                        <x-jet-input-error for='dias' />
                    </div>
                    <div class="form-group">
                        <x-jet-label value="* Cantidad de personas" />
                        <x-jet-input type="number" name="operarios" value="{{old('operarios')}}" class="text-xs uppercase"/>
                        <x-jet-input-error for='operarios' />
                    </div>
                    <div class="form-group">
                        <x-jet-label value="* Descripción" />
                        <select class="form-capture uppercase w-full text-xs" name="description">
                            @foreach ($Descriptions as $row)
                                <option value="{{$row->description}}" @if ($row->description == old('description')) selected @endif >{{$row->description}}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for='description' />
                    </div>
                    <div class="form-group">
                        <x-jet-label value="* Costo x operacion" />
                        <x-jet-input type="number" name="cost" value="{{old('cost')}}" class="text-xs uppercase"/>
                        <x-jet-input-error for='cost' />
                    </div>

                </div>
                <div class="col-12 text-right p-2 gap-2">
                    <a href="{{ route('selectivo_freights.show', $Quotation_Id)}}" class="btn btn-green mb-2">
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