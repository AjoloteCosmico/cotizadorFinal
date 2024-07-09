<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\PriceList;

use App\Models\quotation_galleta;
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
        $Soporte=gangplank_angle::where('caliber',$request->caliber)
        ->where('length','<=',(float)$request->length+0.0001)
        ->where('deep','<=',(float)$request->deep+0.0001)
        ->orderBy('gangplank_angles.length', 'desc')->first();
        
        // dd(number_format((float)$request->deep,2),$Soporte);
        $PrecioLamina=PriceList::where('description','LAMINA')->where('caliber',$request->caliber)->where('type','RC')->first();
        $System=Quotation::find($request->Quotation_Id)->type;
        $F_total=PriceList::where('piece','ANGULO')->where('system',$System)->first()->f_total;
        $UnitPrice=$Soporte->weight* $PrecioLamina->cost*$F_total;
        
        // dd($Soporte->weight,$PrecioLamina->cost,$PrecioLamina->f_total,$UnitPrice); 
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
        $Soporte=gangplank_angle::where('sku',$SJL2->sku)->first();
        $Cart_product= new Cart_product();
        $Cart_product->name='ANGULO RANURADO CAL. '.$caliber.' 0.38x'. $Soporte->deep;;
        $Cart_product->type='Pang'.$caliber;
        $Cart_product->unit_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->sku=$SJL2->sku;
        $Cart_product->save();
        
        return redirect()->route('selectivo.show',[$Quotation_Id,$Quotation->type]);
    }


    //galleta
    public function galleta_show($id){
     $Quotation=Quotation::find($id);
     $Quotation_Id=$id;
     return view('quotes.pasarela.galleta.show',compact('Quotation_Id'));
    }

    public function galleta_store(Request $request){
        $rules=[ 'amount' => 'required'];
        $request->validate($rules);

        $PrecioLaminaRC14=PriceList::where('description','LAMINA')->where('caliber','14')->where('type','RC')->first();
        $F_total=PriceList::where('piece','GALLETA')->first()->f_total;
        
        //  dd($PrecioLamina);
        $UnitPrice=0.12* $PrecioLaminaRC14->cost*$F_total; 
        $QuotGalleta=quotation_galleta::where('quotation_id','=',$request->Quotation_Id)->first();
        if(!$QuotGalleta){
            $QuotGalleta = new  quotation_galleta();
            $QuotGalleta->quotation_id=$request->Quotation_Id;
        }
        $QuotGalleta->unit_price=$UnitPrice;
        $QuotGalleta->total_price=$UnitPrice * $request->amount;
        $QuotGalleta->amount=$request->amount;
        $QuotGalleta->sku='TC0000127668';
        $QuotGalleta->development=0.069;
        $QuotGalleta->length=0.085;
        $QuotGalleta->caliber='12';
        $QuotGalleta->m2=0.01;
        $QuotGalleta->peso=0.12;
        $QuotGalleta->kg_m2=21.25;
        $QuotGalleta->save();

        return view('quotes.pasarela.galleta.store',compact('QuotGalleta'));
    }

    public function galleta_add_carrito($id){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','Pgall'.$caliber)->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = quotation_galleta::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        // $Soporte=gangplank_angle::where('sku',$SJL2->sku)->first();
        $Cart_product= new Cart_product();
        $Cart_product->name='GALLETA';
        $Cart_product->type='Pgall';
        $Cart_product->unit_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->sku=$SJL2->sku;
        $Cart_product->save();
        
        return redirect()->route('selectivo.show',[$Quotation_Id,$Quotation->type]);
   

    }
}


