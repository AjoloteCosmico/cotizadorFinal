<?php

namespace App\Http\Controllers;
use App\Models\Quotation;
use App\Models\PriceList;
use App\Models\drive_in_soporte;
use App\Models\quotation_drive_in_soporte;
use Illuminate\Http\Request;

class DriveInPiezasController extends Controller
{
    public function soportes_menu($id){
        $Quotation_Id=$id;
        return view('quotes.drivein.soportes.menu',compact('Quotation_Id'));

    }
    public function soportes_index($id,$calibre){
        $Quotation_Id=$id;
        $Calibre=$calibre;
        return view('quotes.drivein.soportes.index',compact('Quotation_Id','Calibre'));


    }
    
    public function soportes_store(Request $request){
        $rules=[ 'amount' => 'required',
        'length' => 'required',];
        $request->validate($rules);
        //buscar los datos de soporte que concidan con los parametros de usuario(en este caso solo largo)
        $Soporte=drive_in_soporte::where('length','=',(float)$request->length)->first();
        //buscar los precios de lamina y factores
        //TODO: ACOMODAR LAMINAS EN PRICELIST
        $PrecioLamina=PriceList::where('description','LAMINA NEGRA RC')->where('caliber',$request->caliber)->first();
        dd($request->length,$Soporte);
        $UnitPrice=$Soporte->weight * $PrecioLamina->cost*$PrecioLamina->ftotal; 
        $QuotSoporte=quotation_drive_in_soporte::where('quotation_id','=',$Quotation_Id)->first();
        if(!$QuotSoporte){
            $QuotSoporte = new  quotation_drive_in_soporte();
            $QuotSoporte->quotation_id=$Quotation_Id;
        }
        $QuotSoporte->quotation_id=$Quotation_Id;

    }
}
