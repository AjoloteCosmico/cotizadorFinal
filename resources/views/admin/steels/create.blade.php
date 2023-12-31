@extends('adminlte::page')

@section('title', 'ACEROS')

@section('content_header')
    <h1 class="font-bold"><i class="fas fa-plus-circle"></i>&nbsp; Agregar Acero</h1>
@stop

@section('content')
    <div class="container bg-gray-300 shadow-lg rounded-lg">
        <div class="row p-4 rounded-b-none rounded-t-lg shadow-xl bg-white">
            <h5 class="card-title p-2">
                <i class="fas fa-plus-circle"></i>&nbsp; Agregar Acero
            </h5>
        </div>
        {!! Form::open(['method'=>'POST','route'=>['steels.store']]) !!}
        <div class="row p-4 rounded-b-lg rounded-t-none mb-4 shadow-xl bg-gray-300">
            <div class="col-xs-12 p-2 gap-2">
                <div class="form-group">
                    <x-jet-label value="* Calibre" />
                    {!! Form::text('caliber',old('caliber'), ['class'=>'inputjet w-full text-xs uppercase']) !!}
                    <x-jet-input-error for='caliber' />
                </div>
                <div class="form-group">
                    <x-jet-label value="* Tipo" />
                    {!! Form::text('type',old('type'), ['class'=>'inputjet w-full text-xs uppercase']) !!}
                    <x-jet-input-error for='type' />
                </div>
                <div class="form-group">
                    <x-jet-label value="* Costo" />
                    {!! Form::number('cost',old('cost'), ['class'=>'inputjet w-full text-xs', 'step'=>'0.01']) !!}
                    <x-jet-input-error for='cost' />
                </div>
            </div>
            <div class="col-12 text-right p-2 gap-2">
                <a href="{{ route('steels.index')}}" class="btn btn-green mb-2">
                    <i class="fas fa-times fa-2x"></i>&nbsp;&nbsp; Cancelar
                </a>
                <button type="submit" class="btn btn-red mb-2" onclick="this.disabled=true;">
                    <i class="fas fa-save fa-2x"></i>&nbsp; &nbsp; Guardar
                </button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@stop

@section('css')
    
@stop

@section('js')
<script type="text/javascript" src="{{ asset('vendor/mystylesjs/js/submit_disable') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/mystylesjs/js/disableSubmits.js') }}"></script>
@stop