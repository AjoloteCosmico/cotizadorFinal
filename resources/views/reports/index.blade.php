@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Reportes del Sistema de Cotización Tyrsa</x-header-cot>
@stop

@section('content')
    <div class="container w-full bg-white p-3 rounded-xl shadow-xl">
       
    
    <div class="row m-3">
            
        <div class="col-lg-12 text-left">
        <table>
        <tr>
            <td><a href="{{route('reports.generate',[0,'consecutivo',0])}}">
              <button class="button"> <span class="badge badge-success" style="size:20px;">Consecutivo &nbsp; 
                <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i></span> </button>
             </a> <br> </td>
            <td>
            <a href="{{route('reports.generate',[0,'consecutivo',0])}}">
               <button class="button"> <span class="badge badge-danger">Consecutivo &nbsp; <i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></span> </button>
            </a> <br></td>
        </tr>
        <tr>
            <td><a href="{{route('reports.generate',[0,'kpis',0])}}">
              <button class="button"> <span class="badge badge-success">KPI's &nbsp; <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i></span> </button>
             </a><br>  </td>
            <td><a href="{{route('reports.generate',[0,'kpis',0])}}">
               <button class="button"> <span class="badge badge-danger">KPI's&nbsp; <i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></span> </button>
            </a><br> </td>
        </tr>
        <tr>
            <td>
             <a href="{{route('reports.generate',[0,'kpis2',0])}}">
              <button class="button"> <span class="badge badge-success">KPI2's &nbsp; <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i></span> </button>
             </a><br> </td>
            <td>
            <a href="{{route('reports.generate',[0,'kpis2',0])}}">
               <button class="button"> <span class="badge badge-danger">KPI2's&nbsp; <i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></span> </button>
            </a><br> </td>
        </tr>
    </table>
    
            </div>
            <div class="w-100">&nbsp;</div>
            <div class="col-sm-12 table-responsive">
                <table class="table tablequotations table-striped text-xs">
                    <thead>
                        <tr class="text-center">
                            <th>Folio</th>
                            <th>Sistema</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Excel</th>
                            <th>PDF</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Quotations as $row)
                            <tr class="uppercase">
                                <td>{{$row->invoice}}</td>
                                <td>{{$row->system}}</td>
                                <td>{{ date('d-m-Y', strtotime($row->created_at)) }}</td>
                                <td>{{$row->customer->customer}}</td>
                                <td>
                                <a href="{{route('reports.generate',[$row->id,'administrativo',0])}}">
                                  <button class="button"> <span class="badge badge-success">Admin &nbsp; <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i></span> </button>
                                  </a>  
                                  <a href="{{route('reports.generate',[$row->id,'ventas',0])}}">
                                  <button class="button"> <span class="badge badge-success">Ventas &nbsp; <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i></span> </button>
                                  </a>
                                  <a href="{{route('reports.generate',[$row->id,'margen',0])}}">
                                  <button class="button"> <span class="badge badge-success">Margen &nbsp; <i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i></span> </button>
                                  </a> 
                               </td> 
                               <td>
                               <a href="{{route('reports.generate',[$row->id,'administrativo',1])}}">
                                  <button class="button"> <span class="badge badge-danger">Admin &nbsp;<i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></span> </button>
                                  </a>  
                                  <a href="{{route('reports.generate',[$row->id,'ventas',1])}}">
                                  <button class="button"> <span class="badge badge-danger">Ventas &nbsp;<i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></span> </button>
                                  </a> 
                                  <a href="{{route('reports.generate',[$row->id,'margen',1])}}">
                                  <button class="button"> <span class="badge badge-danger">Margen &nbsp;<i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></span> </button>
                                  </a> 
                               </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')


<script type="text/javascript" src="{{ asset('vendor/mystylesjs/js/tablequotations.js') }}"></script>

@stop
