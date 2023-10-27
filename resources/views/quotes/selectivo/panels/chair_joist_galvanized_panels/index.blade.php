@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop

@section('content')
    <div class="container w-full bg-white p-3 rounded-xl shadow-xl">
        <div class="row m-3">
            {!! Form::open(['method'=>'POST','route'=>['selectivo_chair_joist_galvanized_panels.store']]) !!}
            <input type="hidden" name="Quotation_Id" value="{{$Quotation_Id}}">
            <div  class="row bg-white p-4 shadow-lg rounded-lg">
                <div class="col-sm-6 col-xs-12">
                    <h2><i class="fa-solid fa-crop-simple"></i>&nbsp;PANELES GALVANIZADOS SILLA</h2>
                    <span>Favor de Seleccionar y llenar los campos solicitados para realizar la cotización.</span>
                    <div class="card-body text-center">
                        <img src="{{asset('vendor/img/postes/logo.png')}}" class="img-thumbnail img-fluid max-h-80 rounded mx-auto d-block" alt="">
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group p-2">
                        <x-jet-label value="* Cantidad" />
                        <input type="number" min=1 name="amount" class="inputjet w-full text-xs uppercase" value="{{ old('amount') }}" />
                        <x-jet-input-error for='amount' /><br>

                        <x-jet-label value="* Seleccione el Calibre" />
                        <select name="caliber" class="inputjet w-full text-xs uppercase" id='calibre' onchange='change_load()'>
                            @foreach ($Calibers as $row)
                                <option value="{{$row->caliber}}"@if (old('caliber')==$row->caliber) selected @endif>{{$row->caliber}}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for='caliber' /><br>

                        <x-jet-label value="* Fondo de Marco (M)" />
                        <select name="frame_background" class="inputjet w-full text-xs uppercase">
                            @foreach ($FrameBackgrounds as $row)
                                <option value="{{$row->frame_background}}"@if (old('frame_background')==$row->frame_background) selected @endif>{{$row->frame_background}}</option>
                            @endforeach
                        </select>
                        <x-jet-input-error for='frame_background' /><br>
                        
                        <x-jet-label value="* Ancho" />
                        <select name="background_dimension" class="inputjet w-full text-xs uppercase" id='ancho' onchange='set_capacidad()'>
                            @foreach ($BackgroundDimensions as $row)
                                <option value="{{$row->background_dimension}}"@if (old('background_dimension')==$row->background_dimension) selected @endif>{{$row->background_dimension}}</option>
                            @endforeach
                        </select>
           
                        <x-jet-label value="Capacidad de carga" />
                        <input type="number" id='capacidad' class="inputjet w-full text-xs uppercase" disabled />
                        
                    </div>
                    <div class="form-group p-2 gap-2 flex items-center">
                        <button type="submit" class="btn btn-blue mb-2">
                            <i class="fa-solid fa-calculator fa-xl"></i>&nbsp; Calcular
                        </button>
                        <a href="{{route('selectivo_panels', $Quotation_Id)}}" class="btn btn-black mb-2">
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
    <script>
        var capacidades_array=[];
        change_load();
        set_capacidad();
        function change_load(){
        calibre=document.getElementById('calibre').value;
        console.log(calibre);
        switch(calibre){
            case '14':
                capacidades_array=[170,200];
                console.log('caso 14');
            break;
            case '18':
                capacidades_array=[120,150];
            break;
            case '20':
                capacidades_array=[80,100];
            break;
            case '22':
                capacidades_array=[70,80];
            break;
            case '24':
                capacidades_array=[30,40];
            break;
        
           }
    }

    function set_capacidad(){
        ancho=document.getElementById('ancho').value;
        if(ancho<=0.1){
            document.getElementById('capacidad').value=capacidades_array[0]
        }else{

            document.getElementById('capacidad').value=capacidades_array[1]
        }
    }
    </script>
@stop