<?php

namespace App\Http\Controllers;

use App\Models\estanteria_entrepanio;
use App\Models\quotation_estanteria_entrepanio;
use App\Models\PriceList;
use App\Models\Quotation;
use App\Models\Cart_product;

use App\Models\QuotationRespaldo;

use App\Models\quotation_escuadra;

use App\Models\Respaldo;
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
        if($Ent->type='GALVANIZADO'){
            $F_total=PriceList::where('piece','ENTREPAÑO')
            ->where('type','Galvanizada')
            ->where('caliber',$Ent->caliber)
            ->first()->f_total;    
        }else{
            
            $F_total=PriceList::where('piece','ENTREPAÑO')
            ->where('type','Negra')
            ->where('caliber',$Ent->caliber)
            ->first()->f_total;
        }
        $UnitPrice=$Ent->weight* $PrecioLamina->cost*$F_total;
        
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
        $F_total=PriceList::where('piece','ESCUADRA')
             ->first()->f_total;
        $PrecioEscuadra= 0.06*$PrecioLamina->cost*$F_total;
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


    public function respaldo_show($id){
        $Quotation_Id=$id;
        $Frentes=Respaldo::all()->unique('front');
        $Anchos=Respaldo::all()->unique('deep');
       
        return view('quotes.estanteria.respaldos.index',compact('Quotation_Id','Frentes','Anchos'));
    }
    public function respaldo_store(Request $request,$id){
        $rules=[ 'amount' => 'required',
        'front' => 'required',
        'deep' => 'required',
        'caliber' => 'required',];
        $request->validate($rules);
        //buscar los datos de anguloque concidan con los parametros de usuario(en este caso solo largo)
        $Respaldo=Respaldo::where('front','<=',(float)$request->front+0.0001)
            ->where('deep','<=',(float)$request->deep+0.0001)
            ->orderBy('respaldos.front', 'desc')
            
            ->orderBy('respaldos.deep', 'desc')
             ->first();
        // dd($Respaldo);
        
        $PrecioLamina=PriceList::where('description','LAMINA')
        // ->where('caliber',$request->caliber)
        ->where('caliber','24')
        ->where('type','RC')->first(); 
        $F_total=PriceList::where('piece','RESPALDO')
        ->first()->f_total;
        $UnitPrice=$Respaldo->weight* $PrecioLamina->cost*$F_total;
        
        // dd($Ent); 
        $QuotEnt=QuotationRespaldo::where('quotation_id','=',$request->Quotation_Id)->first();
        if(!$QuotEnt){
            $QuotEnt = new QuotationRespaldo();
            $QuotEnt->quotation_id=$request->Quotation_Id;
        }
        $QuotEnt->unit_price=$UnitPrice;
        $QuotEnt->total_price=$UnitPrice * $request->amount;
        $QuotEnt->amount=$request->amount;
        $QuotEnt->sku=$Respaldo->sku;
        $QuotEnt->save();


        return view('quotes.estanteria.respaldos.store',compact('QuotEnt','Respaldo',));
    
    }


    public function respaldo_add_carrito($id){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','Eresp')->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = QuotationRespaldo::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='RESPALDO ESTANTERIA';
        $Cart_product->type='Eresp';
        $Cart_product->unit_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->sku=$SJL2->sku;
        $Cart_product->save();
        
        return redirect()->route('selectivo.show',[$Quotation_Id,'ESTANTERIA']);
    

    } 
    public function escuadras_show($id){
        $Quotation_Id=$id;
       
        return view('quotes.estanteria.escuadras.index',compact('Quotation_Id'));
    }

    public function escuadras_store(Request $request,$id){
        $rules=[ 'amount' => 'required',
        ];
        $request->validate($rules);
        //buscar los datos de anguloque concidan con los parametros de usuario(en este caso solo largo)
        
        // dd($Respaldo);
        
        $PrecioLamina=PriceList::where('description','LAMINA')
        // ->where('caliber',$request->caliber)
        ->where('caliber','24')
        ->where('type','RC')->first(); 
        $F_total=PriceList::where('piece','ESCUADRA')
             ->first()->f_total;
       
        $UnitPrice= 0.06*$PrecioLamina->cost*$PrecioLamina->F_total;

        // dd($Ent); 
        $QuotEnt=quotation_escuadra::where('quotation_id','=',$request->Quotation_Id)->first();
        if(!$QuotEnt){
            $QuotEnt = new quotation_escuadra();
            $QuotEnt->quotation_id=$request->Quotation_Id;
        }
        $QuotEnt->unit_price=$UnitPrice;
        $QuotEnt->total_price=$UnitPrice * $request->amount;
        $QuotEnt->amount=$request->amount;
        $QuotEnt->sku='TC0000127669';
        $QuotEnt->save();


        return view('quotes.estanteria.escuadras.store',compact('QuotEnt',));
    
    }


    public function escuadras_add_carrito($id){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','Eescref')->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = quotation_escuadra::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='ESCUADRAS DE REFUERZO';
        $Cart_product->type='Eescref';
        $Cart_product->unit_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->sku='TC0000127669';
        $Cart_product->save();
        
        return redirect()->route('selectivo.show',[$Quotation_Id,'ESTANTERIA']);
    

    }


    

}
