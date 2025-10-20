@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop

@section('content')
    <div class="container w-full bg-gray-300 p-3 rounded-xl shadow-xl">
        <div class="row p-3 m-2 rounded-lg shadow-xl bg-white">
             <h3>Fotos de Cotización #{{ $id }}</h3>
    <div style="display:flex; flex-wrap:wrap; gap:10px;">
        @foreach($photos as $photo)
            @if($photo)
                <div>
                    <img src="{{ $photo }}" style="width:200px; border:1px solid #ccc; padding:3px;">
                </div>
            @endif
        @endforeach
        @if(count($photos) == 0)
            <p>No hay fotos disponibles para esta cotización.</p>
        @endif
    </div>
        </div>
    </div>
@stop
