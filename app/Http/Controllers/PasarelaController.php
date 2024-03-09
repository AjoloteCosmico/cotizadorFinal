<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\PriceList;
use App\Models\gangplank_angle;
use App\Models\quotation_gangplank_angle;
use App\Models\Cart_product;
use Illuminate\Support\Facades\Auth;
class PasarelaController extends Controller
{
    public function angulos_menu($id){
        $Quotation_Id=$id;
        return view('quotes.selectivo.angulos.menu',compact('Quotation_Id'));

    }
    public function angulos_index($id,$calibre){
        $Quotation_Id=$id;
        $Calibre=$calibre;
        return view('quotes.selectivo.angulos.index',compact('Quotation_Id','Calibre'));


    }
    
    public function angulos_store(Request $request){
        $rules=[ 'amount' => 'required',
        'length' => 'required|min:0.01|max:12',];
        $request->validate($rules);
        //buscar los datos de anguloque concidan con los parametros de usuario(en este caso solo largo)
        $Soporte=gangplank_angle::where('caliber',$request->caliber)->where('length','<=',(float)$request->length+0.0001)->orderBy('gangplank_angles.length', 'desc')->first();
        
        
        $PrecioLamina=PriceList::where('description','LAMINA')->where('caliber',$request->caliber)->where('type','RC')->first();
       
        $UnitPrice=$Soporte->weight* $PrecioLamina->cost*$PrecioLamina->f_total; 
        $QuotSoporte=quotation_gangplank_angle::where('quotation_id','=',$request->Quotation_Id)->first();
        if(!$QuotSoporte){
            $QuotSoporte = new  quotation_gangplank_angle();
            $QuotSoporte->quotation_id=$request->Quotation_Id;
        }
        $QuotSoporte->unit_price=$UnitPrice;
        $QuotSoporte->total_price=$UnitPrice * $request->amount;
        $QuotSoporte->amount=$request->amount;
        $QuotSoporte->sku=$Soporte->sku;
        $QuotSoporte->save();

        return view('quotes.selectivo.angulos.store',compact('QuotSoporte','Soporte'));
    }
    public function angulos_add_carrito($id,$caliber){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','Pang'.$caliber)->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = quotation_gangplank_angle::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='ANGULO RANURADO CAL. '.$caliber;
        $Cart_product->type='Pang'.$caliber;
        $Cart_product->unit_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->sku=$SJL2->sku;
        $Cart_product->save();
        
        return redirect()->route('selectivo.show',[$Quotation_Id,'PASARELA']);
    }
}
