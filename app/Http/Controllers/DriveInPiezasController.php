<?php

namespace App\Http\Controllers;
use App\Models\Quotation;
use App\Models\PriceList;
use App\Models\drive_in_soporte;
use App\Models\quotation_drive_in_soporte;
use Illuminate\Http\Request;
use App\Models\Cart_product;
use Illuminate\Support\Facades\Auth;

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
        'length' => 'required|min:0.01|max:12',];
        $request->validate($rules);
        //buscar los datos de soporte que concidan con los parametros de usuario(en este caso solo largo)
        $Soporte=drive_in_soporte::where('caliber',$request->caliber)->where('length','<=',$request->length+0.0001)->orderBy('drive_in_soportes.length', 'desc')->first();
        //buscar los precios de lamina y factores
        //TODO: ACOMODAR LAMINAS EN PRICELIST
        $PrecioLamina=PriceList::where('description','LAMINA NEGRA RC')->where('caliber',$request->caliber)->first();
        // dd($Soporte->weight,$PrecioLamina);
        $UnitPrice=$Soporte->weight* $PrecioLamina->cost*$PrecioLamina->f_total; 
        $QuotSoporte=quotation_drive_in_soporte::where('quotation_id','=',$request->Quotation_Id)->first();
        if(!$QuotSoporte){
            $QuotSoporte = new  quotation_drive_in_soporte();
            $QuotSoporte->quotation_id=$request->Quotation_Id;
        }
        $QuotSoporte->unit_price=$UnitPrice;
        $QuotSoporte->total_price=$UnitPrice * $request->amount;
        $QuotSoporte->amount=$request->amount;
        $QuotSoporte->sku=$Soporte->sku;
        $QuotSoporte->save();

        return view('quotes.drivein.soportes.store',compact('QuotSoporte','Soporte'));
    }
    public function soportes_add_carrito($id,$caliber){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','DSop'.$caliber)->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = quotation_drive_in_soporte::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='SOPORTE PARA TARIMA';
        $Cart_product->type='DSop'.$caliber;
        $Cart_product->unit_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->sku=$SJL2->sku;
        $Cart_product->save();
        
        return redirect()->route('selectivo.show',$Quotation_Id);
    }

    public function guias_index($id){
        $Quotation_Id=$id;
        return view('quotes.drivein.guias.index',compact('Quotation_Id'));
    }
    public function guias_store(Request $request){
        $rules=[ 'amount' => 'required',
        'length' => 'required|min:0.01|max:12',];
        $request->validate($rules);
        //buscar los datos de soporte que concidan con los parametros de usuario(en este caso solo largo)
        $Soporte=drive_in_soporte::where('caliber',$request->caliber)->where('length','<=',$request->length+0.0001)->orderBy('drive_in_soportes.length', 'desc')->first();
        //buscar los precios de lamina y factores
        //TODO: ACOMODAR LAMINAS EN PRICELIST
        $PrecioLamina=PriceList::where('description','LAMINA NEGRA RC')->where('caliber',$request->caliber)->first();
        // dd($Soporte->weight,$PrecioLamina);
        $UnitPrice=$Soporte->weight* $PrecioLamina->cost*$PrecioLamina->f_total; 
        $QuotSoporte=quotation_drive_in_soporte::where('quotation_id','=',$request->Quotation_Id)->first();
        if(!$QuotSoporte){
            $QuotSoporte = new  quotation_drive_in_soporte();
            $QuotSoporte->quotation_id=$request->Quotation_Id;
        }
        $QuotSoporte->unit_price=$UnitPrice;
        $QuotSoporte->total_price=$UnitPrice * $request->amount;
        $QuotSoporte->amount=$request->amount;
        $QuotSoporte->sku=$Soporte->sku;
        $QuotSoporte->save();

        return view('quotes.drivein.soportes.store',compact('QuotSoporte','Soporte'));
    }
    public function guias_add_carrito($id,$caliber){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','DSop'.$caliber)->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = quotation_drive_in_soporte::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='SOPORTE PARA TARIMA';
        $Cart_product->type='DSop'.$caliber;
        $Cart_product->unit_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->sku=$SJL2->sku;
        $Cart_product->save();
        
        return redirect()->route('selectivo.show',$Quotation_Id);
    }

    public function brazos_index($id){
        $Quotation_Id=$id;
        return view('quotes.drivein.brazos.index',compact('Quotation_Id'));
    }
    public function arriostrados_index($id){
        $Quotation_Id=$id;
        return view('quotes.drivein.arriostrados.index',compact('Quotation_Id'));
    }

}
