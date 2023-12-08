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
                    <p class="card-text mb-3">CANTIDAD DE DIBUJOS</p>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">&nbsp;</div>
                            <div class="col-xs-12 col-sm-9">
                                <div class="form-group">
                                    <textarea name="a5" rows="5" class="inputjet text-sm w-full uppercase">{{old('a5')}}</textarea>
                                    <x-jet-input-error for='a5' />
                                </div>
                            </div>
                        </div>
                        <p class="card-text mb-3">¿QUÉ PRODUCTO SE ALMACENA?</p>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">&nbsp;</div>
                            <div class="col-xs-12 col-sm-9">
                                <div class="form-group">
                                    <textarea name="a5" rows="5" class="inputjet text-sm w-full uppercase">{{old('a5')}}</textarea>
                                    <x-jet-input-error for='a5' />
                                </div>
                            </div>
                        </div>
                        <p class="card-text mb-3">ESTE PRODUCTO TIENE LAS DIMENSIONES :  (NIVEL)</p>
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col gap-1 inline-flex">
                                        <div class="form-group">
                                            <x-jet-label value="Frente" />
                                            <x-jet-input type="text" name="a8" value="{{old('a8')}}" class="w-flex uppercase"/>
                                            <x-jet-input-error for='a8' />
                                        </div>
                                        <div class="form-group">
                                            <x-jet-label value="Fondo" />
                                            <x-jet-input type="text" name="a9" value="{{old('a9')}}" class="w-flex uppercase"/>
                                            <x-jet-input-error for='a9' />
                                        </div>
                                        <div class="form-group">
                                            <x-jet-label value="Alto" />
                                            <x-jet-input type="text" name="a10" value="{{old('a10')}}" class="w-flex uppercase"/>
                                            <x-jet-input-error for='a10' />
                                        </div>
                                        <div class="form-group">
                                            <x-jet-label value="Peso" />
                                            <x-jet-input type="text" name="a11" value="{{old('a11')}}" class="w-flex uppercase"/>
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
                                                    <x-jet-input type="text" name="a18" value="{{old('a18')}}" class="w-flex uppercase"/>
                                                    <x-jet-input-error for='a18' />
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <x-jet-label value="TIENE UN FONDO DE:" />
                                                    <x-jet-input type="text" name="a19" value="{{old('a19')}}" class="w-flex uppercase"/>
                                                    <x-jet-input-error for='a19' />
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <x-jet-label value="TIENE UN ALTO DE:" />
                                                    <x-jet-input type="text" name="a20" value="{{old('a20')}}" class="w-flex uppercase"/>
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
                                        <option @if(old('a25')=="AMBIENTE (10º A 30ª)") selected @endif value="AMBIENTE (10º A 30ª)">AMBIENTE (10º A 30ª)</option>
                                        <option @if(old('a25')=="REFFRIGERACION (-3 A 10ª)") selected @endif value="REFFRIGERACION (-3 A 10ª)">REFFRIGERACION (-3 A 10ª)</option>
                                        <option @if(old('a25')=="CONGELACION (-20º A -4º)") selected @endif value="CONGELACION (-20º A -4º)">CONGELACION (-20º A -4º)</option>
                                        <option @if(old('a25')=="EXTREMO (+ DE 40º)") selected @endif value="EXTREMO (+ DE 40º)">EXTREMO (+ DE 40º)</option>
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
                                        <option @if(old('a26')=="SI") selected @endif value="SI">SI</option>
                                        <option @if(old('a26')=="NO") selected @endif value="NO">NO</option>
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
                                        <option @if(old('a27')=="SI") selected @endif value="SI">SI</option>
                                        <option @if(old('a27')=="NO") selected @endif value="NO">NO</option>
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
                                        <option @if(old('a28')=="SI") selected @endif value="SI">SI</option>
                                        <option @if(old('a28')=="NO") selected @endif value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    

                        <p class="card-text mb-3">CANTIDAD DE POSICIONES:</p>
                        <div class="form-group">
                                            <x-jet-label value="Frente" />
                                            <x-jet-input type="text" name="a8" value="{{old('a8')}}" class="w-flex uppercase"/>
                                            <x-jet-input-error for='a8' />
                                        </div>

                                        <p class="card-text mb-3">TODO SOBRE VIGAS:</p>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3">&nbsp;</div>
                            <div class="col-xs-12 col-sm-9">
                                <div class="form-group">
                                    <select class="inputjet text-sm w-full uppercase" name="a27" id="a27_1">
                                        <option @if(old('a27')=="SI") selected @endif value="SI">SI</option>
                                        <option @if(old('a27')=="NO") selected @endif value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                                        <p class="card-text mb-3">TIEMPO DE ENTREGA:</p>
                        <div class="form-group">
                                            <x-jet-label value="Frente" />
                                            <x-jet-input type="text" name="a8" value="{{old('a8')}}" class="w-flex uppercase"/>
                                            <x-jet-input-error for='a8' />
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