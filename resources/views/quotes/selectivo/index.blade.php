@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop




@section('content')
    <div class="container bg-white p-3 rounded-xl shadow-xl">
        <div class="row m-3">
            <div class="row mb-3">
                <nav class="navbar bg-light rounded-full">
                    <div class="container-fluid items-center">
                
          
    
                <span class="navbar-brand mb-0 h3 font-bold">{{$System}}</span>
                    </div>
                </nav>
            </div>
 

            
            <div class="row">
               @include('quotes.selectivo.'.$System)
            </div>
        </div>
        <div class="row text-end">
            <div class="container">
                {!! Form::open(['method'=>'POST','route'=>['product_menu']]) !!}
                <input type="hidden" name="quotations_id" value="{{$Quotation_Id}}">
                <button type="submit" class="btn btn-black mb-2">
                    <i class="fa-solid fa-rotate-left fa-xl"></i>&nbsp; Menú
                </button>
                {!! Form::close() !!}
            </div>
       
       
        </div>

    </div>

   
@stop