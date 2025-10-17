@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop

@section('content')
    <div class="container w-full bg-gray-300 p-3 rounded-xl shadow-xl">
        <div class="row p-3 m-2 rounded-lg shadow-xl bg-white">
        <form action="{{route('addphotos',$Quotation_Id)}}" " method="POST" enctype="multipart/form-data" style="width:80vw;">
                    
                        @csrf
                      
                <input type="file" name="photo[]" id="foto-input" multiple style="width:80vw;">

           <div class="card-body">
               
            </div>
            <div class="card-footer">
                <div class="col-xs-12 col-sm-6 m-1 gap-2">
                       <input type="hidden" name="quotations_id" value="{{$Quotation_Id}}">
                       <button type="submit" class="btn btn-green mb-2">
                            <i class="fa-solid fa-circle-arrow-right"></i>&nbsp; &nbsp; Continuar
                        </button>
                    
                </div>
                </form>
            </div>
        </div>
    </div>
@stop

@push('css')
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endpush

@push('js')

<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
 {{-- 
 <script>
    // Asegúrate de que el input file en tu HTML tenga el ID 'foto-input'

document.addEventListener('DOMContentLoaded', function() {
    
    // Registra los plugins (opcional, pero mejora la experiencia)
    FilePond.registerPlugin(FilePondPluginImagePreview);
    
    const inputElement = document.querySelector('input[id="foto-input"]');
    
    // Crea una instancia de FilePond
    const pond = FilePond.create(inputElement, {
        allowMultiple: true,
        maxFiles: 10, // LIMITE CLAVE: Máximo 10 archivos
        labelIdle: 'Arrastra y suelta tus fotos o haz clic <br> (Máximo 10 archivos)',
        
        // Configuración para enviar múltiples archivos con el nombre 'fotos[]'
        name: 'photo',
        acceptedFileTypes: ['image/jpeg', 'image/png', 'image/gif'], // Acepta solo fotos
       
        server: {
        process: '{{route('addphotos',$Quotation_Id)}}',
        revert: '',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }
    });
});
</script>  --}}
@endpush