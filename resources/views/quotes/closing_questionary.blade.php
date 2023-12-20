@extends('adminlte::page')

@section('title', 'COTIZADOR')

@section('content_header')
    <x-header-cot>Sistema de Cotización Tyrsa</x-header-cot>
@stop

@section('content')
    <div class="container w-full bg-white p-3 rounded-xl shadow-xl">
        <div class="row m-2">
        {!! Form::open(['method'=>'POST','route'=>['close_quotation',$Quotation->id]]) !!}
            <input type="hidden" name="quotations_id" value="{{$Quotation->id}}">
            <div class="container bg-gray-300 shadow-sm rounded-xl p-3">
                <div class="card p-3">
                    <div class="card-title bg-gray-300 m-2 p-2">
                        <h3 class="text-center font-bold h-5">CUESTIONARIO PARA CERRAR</h3>
                    </div>
                    <div class="card-body text-sm">
                    <p class="card-text mb-3">CANTIDAD DE DIBUJOS (separe por comas) </p>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">&nbsp;</div>
                            <div class="col-xs-12 col-sm-9">
                                <div class="form-group">
                                    <textarea name="ndib" rows="5" class="inputjet text-sm w-flex uppercase">  {{old('ndib') }} </textarea>
                                    <x-jet-input-error for='ndib' />
                                </div>
                            </div>
                        </div>
                        <p class="card-text mb-3">¿QUÉ PRODUCTO SE ALMACENA?</p>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">&nbsp;</div>
                            <div class="col-xs-12 col-sm-9">
                                <div class="form-group">
                                    <textarea name="a5" rows="5" class="inputjet text-sm w-full uppercase"> @if($Questionary->a5) {{$Questionary->a5}} @else {{old('a5') }} @endif</textarea>
                                    <x-jet-input-error for='a5' />
                                </div>
                            </div>
                        </div>
                        <p class="card-text mb-3">SECCION:</p>
                        <div class="form-group">
                                            <x-jet-input type="text" name="section" value="{{old('section')}}" class="w-flex uppercase"/>
                                            <x-jet-input-error for='section' />
                                        </div>
                        <p class="card-text mb-3">ESTE PRODUCTO TIENE LAS DIMENSIONES :  (NIVEL)</p>
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col gap-1 inline-flex">
                                        <div class="form-group">
                                            <x-jet-label value="Frente" />
                                            <x-jet-input type="text" name="a8"  class="w-flex uppercase"   value="{{$Questionary->a8}}"  />
                                            <x-jet-input-error for='a8' />
                                        </div>
                                        <div class="form-group">
                                            <x-jet-label value="Fondo" />
                                            <x-jet-input type="text" name="a9" value="{{$Questionary->a9}}" class="w-flex uppercase"/>
                                            <x-jet-input-error for='a9' />
                                        </div>
                                        <div class="form-group">
                                            <x-jet-label value="Alto" />
                                            <x-jet-input type="text" name="a10" value="{{$Questionary->a10}}" class="w-flex uppercase"/>
                                            <x-jet-input-error for='a10' />
                                        </div>
                                        <div class="form-group">
                                            <x-jet-label value="Peso" />
                                            <x-jet-input type="text" name="a11" value="{{$Questionary->a11}}"  class="w-flex uppercase"/>
                                            <x-jet-input-error for='a11' />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="card-text mb-3">LA TARIME TIENE DIMENSIONES:</p>
                        
                        <div class="col">
                                                <div class="form-group">
                                                    <x-jet-label value="TIENE UN FRENTE DE:" />
                                                    <x-jet-input type="text" name="a18" value="{{$Questionary->a18}}" class="w-flex uppercase"/>
                                                    <x-jet-input-error for='a18' />
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <x-jet-label value="TIENE UN FONDO DE:" />
                                                    <x-jet-input type="text" name="a19" value="{{$Questionary->a19}}" class="w-flex uppercase"/>
                                                    <x-jet-input-error for='a19' />
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <x-jet-label value="TIENE UN ALTO DE:" />
                                                    <x-jet-input type="text" name="a20" value="{{$Questionary->a20}}" class="w-flex uppercase"/>
                                                    <x-jet-input-error for='a20' />
                                                </div>
                                                <div class="card-title bg-gray-300 m-2 p-2">
                        <h3 class="text-center font-bold h-5">DATOS DEL AMBIENTE </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-text mb-3">TEMPERATURA:</p>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">&nbsp;</div>
                            <div class="col-xs-12 col-sm-9">
                                <div class="form-group">
                                    <select class="inputjet text-sm w-full uppercase" name="a25" id="a25_1">
                                        <option @if($Questionary->a25 =="AMBIENTE (10º A 30ª)") selected @endif value="AMBIENTE (10º A 30ª)">AMBIENTE (10º A 30ª)</option>
                                        <option @if($Questionary->a25 =="REFFRIGERACION (-3 A 10ª)") selected @endif value="REFFRIGERACION (-3 A 10ª)">REFFRIGERACION (-3 A 10ª)</option>
                                        <option @if($Questionary->a25 =="CONGELACION (-20º A -4º)") selected @endif value="CONGELACION (-20º A -4º)">CONGELACION (-20º A -4º)</option>
                                        <option @if($Questionary->a25 =="EXTREMO (+ DE 40º)") selected @endif value="EXTREMO (+ DE 40º)">EXTREMO (+ DE 40º)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <x-jet-input-error for='a25' />
                        <p class="card-text mb-3">INFLAMABLE:</p>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">&nbsp;</div>
                            <div class="col-xs-12 col-sm-9">
                                <div class="form-group">
                                    <select class="inputjet text-sm w-full uppercase" name="a26" id="a26_1">
                                        <option @if($Questionary->a26 =="SI") selected @endif value="SI">SI</option>
                                        <option @if($Questionary->a26 =="NO") selected @endif value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <x-jet-input-error for='a26' />
                        <p class="card-text mb-3">EXPLOSIVO:</p>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">&nbsp;</div>
                            <div class="col-xs-12 col-sm-9">
                                <div class="form-group">
                                    <select class="inputjet text-sm w-full uppercase" name="a27" id="a27_1">
                                        <option @if($Questionary->a27 =="SI") selected @endif value="SI">SI</option>
                                        <option @if($Questionary->a27 =="NO") selected @endif value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <x-jet-input-error for='a27' />
                        <p class="card-text mb-3">CORROSIVO:</p>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">&nbsp;</div>
                            <div class="col-xs-12 col-sm-9">
                                <div class="form-group">
                                    <select class="inputjet text-sm w-full uppercase" name="a28" id="a28_1">
                                        <option @if($Questionary->a28 =="SI") selected @endif value="SI">SI</option>
                                        <option @if($Questionary->a28 =="NO") selected @endif value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    

                        <p class="card-text mb-3">CANTIDAD DE POSICIONES:</p>
                        <div class="form-group">
                                            <x-jet-input type="text" name="npos" value="{{old('npos')}}" class="w-flex uppercase"/>
                                            <x-jet-input-error for='npos' />
                                        </div>

                                        <p class="card-text mb-3">TODO SOBRE VIGAS:</p>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">&nbsp;</div>
                            <div class="col-xs-12 col-sm-9">
                                <div class="form-group">
                                    <select class="inputjet text-sm w-full uppercase" name="vigas" id="vigas">
                                        <option @if(old('vigas')=="SI") selected @endif value="SI">SI</option>
                                        <option @if(old('vigas')=="NO") selected @endif value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                                        <p class="card-text mb-3">TIEMPO DE ENTREGA:</p>
                        <div class="form-group">
                                            <x-jet-input type="text" name="tiempo" value="{{old('tiempo')}}" class="w-flex uppercase"/>
                                            <x-jet-input-error for='tiempo' />
                                        </div>
                              
                    </div>
                </div>
            </div>                
            <div class="card-footer">
                <div class="col-xs-12 col-sm-6 m-1 gap-2">
                    <button type="submit" class="btn btn-green mb-2">
                        <i class="fa-solid fa-circle-arrow-right"></i>&nbsp; &nbsp; Continuar
                    </button>
                </div>
            </div>
        {!! Form::close() !!}
        </div>
    </div>
@stop