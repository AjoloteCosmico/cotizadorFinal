<?php

namespace App\Http\Controllers;

use App\Models\estanteria_entrepanio;
use App\Models\quotation_estanteria_entrepanio;
use App\Models\PriceList;
use App\Models\Quotation;
use App\Models\Cart_product;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class EstanteriaController extends Controller
{
    public function entrepanios_index($id,$type){
        $Quotation_Id=$id;
        $Largos=estanteria_entrepanio::all()->unique('length');
        $Fondos=estanteria_entrepanio::all()->unique('deep');
        $Calibres=estanteria_entrepanio::all()->unique('caliber');
         $Type=$type;
       
        // dd($Largos);
        return view('quotes.estanteria.entrepanio.index',compact('Quotation_Id','Largos','Fondos','Calibres','Type'));


    }
    
    public function entrepanios_store(Request $request){
        $rules=[ 'amount' => 'required',
        'length' => 'required',
        'deep' => 'required',
        'caliber' => 'required',];
        $request->validate($rules);
        //buscar los datos de anguloque concidan con los parametros de usuario(en este caso solo largo)
        $Ent=estanteria_entrepanio::where('caliber',$request->caliber)
             ->where('length','<=',(float)$request->length+0.0001)
             ->where('deep','<=',(float)$request->deep+0.0001)
             ->where('type',$request->type)
             ->orderBy('estanteria_entrepanios.length', 'desc')->first();
        
        
        $PrecioLamina=PriceList::where('description','LAMINA')
        // ->where('caliber',$request->caliber)
        ->where('caliber','24')
        ->where('type','RC')->first(); 
       
        $UnitPrice=$Ent->weight* $PrecioLamina->cost*$PrecioLamina->f_total;
        
        // dd($Ent); 
        $QuotEnt=quotation_estanteria_entrepanio::where('quotation_id','=',$request->Quotation_Id)->first();
        if(!$QuotEnt){
            $QuotEnt = new quotation_estanteria_entrepanio();
            $QuotEnt->quotation_id=$request->Quotation_Id;
        }
        $QuotEnt->unit_price=$UnitPrice;
        $QuotEnt->total_price=$UnitPrice * $request->amount;
        $QuotEnt->amount=$request->amount;
        $QuotEnt->sku=$Ent->sku;
        $QuotEnt->caliber=$Ent->caliber;
        $QuotEnt->type=$Ent->type;
        $QuotEnt->refuerzos=0;
        $QuotEnt->save();

        $PrecioEscuadra= 0.06*$PrecioLamina->cost*$PrecioLamina->f_total;

        return view('quotes.estanteria.entrepanio.store',compact('QuotEnt','Ent','PrecioEscuadra'));
    }
    public function entrepanios_refuerzos(Request $request){
        $QuotEnt=quotation_estanteria_entrepanio::where('quotation_id','=',$request->Quotation_Id)->first();
        // dd($request);
        $QuotEnt->refuerzos=$request->amount;
        $QuotEnt->save();
        
        $Ent=estanteria_entrepanio::where('sku',$QuotEnt->sku)
             ->where('type',$QuotEnt->type)
             ->first();
        $PrecioLamina=PriceList::where('description','LAMINA')
             // ->where('caliber',$request->caliber)
             ->where('caliber','24')
             ->where('type','RC')->first(); 
        $PrecioEscuadra= 0.06*$PrecioLamina->cost*$PrecioLamina->f_total;
        return view('quotes.estanteria.entrepanio.store',compact('QuotEnt','Ent','PrecioEscuadra'));
   
    }
    public function entrepanio_add_carrito($id,$type){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','Eent'.$type)->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = quotation_estanteria_entrepanio::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='ENTREPAÑO.'.$type.'ESTANTERIA CAL. '.$SJL2->caliber.' con '.$SJL2->refuerzos.'refuerzos' ;
        $Cart_product->type='Eent'.$type;
        $Cart_product->unit_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->sku=$SJL2->sku;
        $Cart_product->save();
        
        return redirect()->route('selectivo.show',[$Quotation_Id,'ESTANTERIA']);
    }

    

}
