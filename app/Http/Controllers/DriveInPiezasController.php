<?php

namespace App\Http\Controllers;
use App\Models\Quotation;
use App\Models\PriceList;
use App\Models\drive_in_soporte;
use App\Models\quotation_drive_in_soporte;
use App\Models\quotation_drive_in_arriostrado;
use App\Models\drive_in_guia;
use App\Models\quotation_drive_in_guia;
use App\Models\quotation_drive_in_brazo;
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
        $Soporte=drive_in_soporte::where('caliber',$request->caliber)->where('length','<=',(float)$request->length+0.0001)->orderBy('drive_in_soportes.length', 'desc')->first();
        //buscar los precios de lamina y factores
        //TODO: ACOMODAR LAMINAS EN PRICELIST
        if($request->caliber=='12'|$request->caliber=='10'){
            $PrecioLamina=PriceList::where('description','LAMINA')->where('caliber',$request->caliber)->where('type','RC')->first(); 
        }else{
            $PrecioLamina=PriceList::where('description','LAMINA')->where('caliber',$request->caliber)->where('type','ESTRUCTURAL')->first();
        }
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
        $Cart_product->name='SOPORTE PARA TARIMA CAL. '.$caliber;
        $Cart_product->type='DSop'.$caliber;
        $Cart_product->unit_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->sku=$SJL2->sku;
        $Cart_product->save();
        
        return redirect()->route('drivein.show',$Quotation_Id);
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
        $Guia=drive_in_guia::where('length','<=',(float)$request->length+0.0001)->orderBy('drive_in_guias.length', 'desc')->first();
        
        //buscar los precios de lamina y factores
        //TODO: ACOMODAR LAMINAS EN PRICELIST
        $PrecioLamina=PriceList::where('description','LAMINA')->where('caliber','EST 3 IN')->where('type','ESTRUCTURAL')->first();
        
        //  dd($PrecioLamina);
        $UnitPrice=$Guia->weight* $PrecioLamina->cost*$PrecioLamina->f_total; 
        $QuotGuia=quotation_drive_in_guia::where('quotation_id','=',$request->Quotation_Id)->first();
        if(!$QuotGuia){
            $QuotGuia = new  quotation_drive_in_guia();
            $QuotGuia->quotation_id=$request->Quotation_Id;
        }
        $QuotGuia->unit_price=$UnitPrice;
        $QuotGuia->total_price=$UnitPrice * $request->amount;
        $QuotGuia->amount=$request->amount;
        $QuotGuia->sku=$Guia->sku;
        $QuotGuia->save();

        return view('quotes.drivein.guias.store',compact('QuotGuia','Guia'));
    }
    public function guias_add_carrito($id){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','Dguia')->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = quotation_drive_in_soporte::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='GUIA PARA MONTACARGAS';
        $Cart_product->type='Dguia';
        $Cart_product->unit_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->sku=$SJL2->sku;
        $Cart_product->save();
        return redirect()->route('drivein.show',$Quotation_Id);
    }

    // brazos------------------------
    public function brazos_index($id){
        $Quotation_Id=$id;
        $Rolados=quotation_drive_in_brazo::where('quotation_id','=',$Quotation_Id)->where('description','ROLADO')->first();
        $Est3In=quotation_drive_in_brazo::where('quotation_id','=',$Quotation_Id)->where('description','EST 3 IN')->first();
        $Est4In=quotation_drive_in_brazo::where('quotation_id','=',$Quotation_Id)->where('description','EST 4 IN')->first();
       
        return view('quotes.drivein.brazos.index',compact('Quotation_Id','Rolados','Est3In','Est4In'));
    }
    public function brazos_store(Request $request){
        $rules=[ 'rolados_amount' => 'required',
        'est3_amount' => 'required',
        'est4_amount' => 'required'];
        $request->validate($rules);
        //traer precio de laminas: rc 14, rc 10, rc 3/16 rc 3 in rc 4 in
        $PrecioLaminaRC14=PriceList::where('description','LAMINA')->where('caliber','14')->where('type','RC')->first();
        $PrecioLaminaRC10=PriceList::where('description','LAMINA')->where('caliber','10')->where('type','RC')->first();
        $PrecioLaminaRC316=PriceList::where('description','LAMINA')->where('caliber','3/16')->where('type','RC')->first();
        $PrecioLaminaEST3=PriceList::where('description','LAMINA')->where('caliber','EST 3 IN')->where('type','RC ESTRUCTURAL')->first();
        $PrecioLaminaEST4=PriceList::where('description','LAMINA')->where('caliber','EST 3 IN')->where('type','RC ESTRUCTURAL')->first();
        // dd($PrecioLaminaRC10,$PrecioLaminaRC14,$PrecioLaminaRC316,$PrecioLaminaEST3,$PrecioLaminaEST4); 
        $Rolados=quotation_drive_in_brazo::where('quotation_id','=',$request->Quotation_Id)->where('description','ROLADO')->first();
        $Est3In=quotation_drive_in_brazo::where('quotation_id','=',$request->Quotation_Id)->where('description','EST 3 IN')->first();
        $Est4In=quotation_drive_in_brazo::where('quotation_id','=',$request->Quotation_Id)->where('description','EST 4 IN')->first();
        if(!$Rolados){
            $Rolados = new  quotation_drive_in_brazo();
            $Rolados->quotation_id=$request->Quotation_Id;
            $Rolados->description='ROLADO ';
            $Rolados->sku=$request->Quotation_Id;
            
            $Rolados->caliber='14';
            $Rolados->piezas_nec=3;
            $Rolados->amount=$request->rolados_amount;
            $Rolados->piece_weight=2.1371;
            $Rolados->m2=0.1138;
            $Rolados->sku='TC0000117254';
        }
        $PrecioCuerpo=1.43*$PrecioLaminaRC14->f_total*$PrecioLaminaRC14->cost;
        $PrecioConectores=0.57*$PrecioLaminaRC316->f_total*$PrecioLaminaRC316->cost;
        $PrecioAngulo=0.14*$PrecioLaminaRC10->f_total*$PrecioLaminaRC10->cost;

        $Rolados->unit_price=$PrecioAngulo+$PrecioConectores+$PrecioCuerpo;
        $Rolados->total_price=($PrecioAngulo+$PrecioConectores+$PrecioCuerpo)* $request->rolados_amount;
        $Rolados->amount=$request->rolados_amount;
        $Rolados->save();
        if(!$Est3In){
            $Est3In = new  quotation_drive_in_brazo();
            $Est3In->quotation_id=$request->Quotation_Id;
            $Est3In->description='EST 3 IN';
            // $Est3In->desarrollo=0.76;
            // $Est3In->length=1.75;
            $Est3In->amount=$request->est3_amount;
            
            $Est3In->caliber='EST 3 IN';
            $Est3In->piezas_nec=2;
            $Est3In->piece_weight=2.0988;
            $Est3In->m2=0.1226;
            $Est3In->sku='TC0000117255';
        }
        $PrecioCuerpo=1.53*$PrecioLaminaEST3->cost*$PrecioLaminaEST3->f_total;
        $PrecioConectores=0.57*$PrecioLaminaRC316->cost*$PrecioLaminaRC316->f_total;
        $Est3In->unit_price=$PrecioCuerpo+$PrecioConectores;
        $Est3In->total_price=($PrecioCuerpo+$PrecioConectores)*$request->est3_amount;
        $Est3In->amount=$request->est3_amount;
        $Est3In->save();
        if(!$Est4In){
            $Est4In = new  quotation_drive_in_brazo();
            $Est4In->quotation_id=$request->Quotation_Id;
            $Est4In->description='EST 4 IN';
            // $Est3In->desarrollo=0.76;
            $Est3In->amount=$request->est4_amount;
            // $Est3In->weight=1.83;
            $Est4In->caliber='EST 4 IN';
            $Est4In->piezas_nec=2;
            $Est4In->piece_weight=2.5838;
            $Est4In->m2=0.1346;
            $Est4In->sku='TC0000117256';
        }
        $PrecioCuerpo=2.01*$PrecioLaminaEST4->cost*$PrecioLaminaEST4->f_total;
        $PrecioConectores=0.57*$PrecioLaminaRC316->cost*$PrecioLaminaRC316->f_total;
        $Est4In->unit_price=$PrecioCuerpo+$PrecioConectores;
        $Est4In->total_price=($PrecioCuerpo+$PrecioConectores)*$request->est4_amount;
        $Est4In->amount=$request->est4_amount;
        $Est4In->save();


        return view('quotes.drivein.brazos.store',compact('Rolados','Est3In','Est4In'));
    }

    
    public function brazos_add_carrito($id){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $carts = Cart_product::where('quotation_id', $Quotation_Id)->where('type','Dbra')->get();
        foreach($carts as $c){
            Cart_product::destroy($c->id);
        }
       
        //agregar el nuevo al carrito, lo que este en 
        // $Rolados=quotation_drive_in_brazo::where('quotation_id','=',$request->Quotation_Id)->where('description','ROLADO')->first();
        // $Est3In=quotation_drive_in_brazo::where('quotation_id','=',$request->Quotation_Id)->where('description','EST 3 IN')->first();
        // $Est4In=quotation_drive_in_brazo::where('quotation_id','=',$request->Quotation_Id)->where('description','EST 4 IN')->first();
        $Brazos=quotation_drive_in_brazo::where('quotation_id','=',$Quotation_Id)->get();
        foreach($Brazos as $b){
            if($b->amount>0){
            $Cart_product= new Cart_product();
            $Cart_product->name='BRAZO DRIVE IN '.$b->description;
            $Cart_product->type='Dbra';
            $Cart_product->unit_price=$b->total_price/$b->amount;
            $Cart_product->total_price=$b->total_price;
            $Cart_product->quotation_id=$b->quotation_id;
            $Cart_product->user_id=Auth::user()->id;
            $Cart_product->amount=$b->amount;
            $Cart_product->sku=$b->sku;
            $Cart_product->save();
            }
    
        }
        
        return redirect()->route('drivein.show',$Quotation_Id);
    }

    //arriostrados---------------
    public function arriostrados_index($id){
        $Rolados=quotation_drive_in_arriostrado::where('quotation_id','=',$id)->where('description','ROLADO C-12')->first();
        
        $Estructurales=quotation_drive_in_arriostrado::where('quotation_id','=',$id)->where('description','ESTRUCTURAL')->first();
        
        if($Rolados){
            $n_rolados=$Rolados->amount;
        }else{
            $n_rolados=0;
        }
        
        if($Estructurales){
            $n_est=$Estructurales->amount;
        }else{
            $n_est=0;
        }
        $Quotation_Id=$id;
        
        return view('quotes.drivein.arriostrados.index',compact('Quotation_Id','n_est','n_rolados'));
    }
    public function arriostrados_store(Request $request){
        $rules=[ 'rolados_amount' => 'required',
        'est_amount' => 'required'];
        // $request->validate($rules);
        $PrecioLaminaRC=PriceList::where('description','LAMINA')->where('caliber','12')->where('type','RC')->first();
        
        $PrecioLaminaEst=PriceList::where('description','LAMINA')->where('caliber','1.5')->where('type','RC ESTRUCTURAL ANGULO')->first();
         
        $Rolados=quotation_drive_in_arriostrado::where('quotation_id','=',$request->Quotation_Id)->where('description','ROLADO C-12')->first();
        $Estructurales=quotation_drive_in_arriostrado::where('quotation_id','=',$request->Quotation_Id)->where('description','ESTRUCTURAL')->first();
        if(!$Rolados){
            $Rolados = new  quotation_drive_in_arriostrado();
            $Rolados->quotation_id=$request->Quotation_Id;
            $Rolados->description='ROLADO C-12';
            $Rolados->desarrollo=0.76;
            $Rolados->length=1.75;
            $Rolados->caliber='12';
            $Rolados->piezas_nec=1;
            $Rolados->weight=21.25;
            $Rolados->piece_weight=2.83;
            $Rolados->m2=0.13;
            $Rolados->sku='TC0000122057';
        }
        $Rolados->unit_price=$PrecioLaminaRC->cost*$PrecioLaminaRC->f_total;
        $Rolados->total_price=$PrecioLaminaRC->cost*$PrecioLaminaRC->f_total * $request->rolados_amount;
        if($request->filled('rolados_amount')){
        $Rolados->amount=$request->rolados_amount;}
        else{
            $Rolados->amount=0;
        }
        $Rolados->save();
        if(!$Estructurales){
            $Estructurales = new  quotation_drive_in_arriostrado();
            $Estructurales->quotation_id=$request->Quotation_Id;
            $Estructurales->description='ESTRUCTURAL';
            $Estructurales->desarrollo=0.76;
            $Estructurales->length=1.75;
            $Estructurales->weight=1.83;
            $Estructurales->caliber='1/8';
            $Estructurales->piezas_nec=1;
            $Estructurales->piece_weight=3.20;
            $Estructurales->m2=0.27;
            $Estructurales->sku='TC0000122058';
        }
        $Estructurales->unit_price=$PrecioLaminaEst->cost*$PrecioLaminaEst->f_total;
        $Estructurales->total_price=$PrecioLaminaEst->cost*$PrecioLaminaEst->f_total * $request->est_amount;
        if($request->filled('est_amount')){
            $Estructurales->amount=$request->est_amount;}
            else{
                $Estructurales->amount=0;
            }
        
        $Estructurales->save();

        return view('quotes.drivein.arriostrados.store',compact('Rolados','Estructurales'));
    }

    
    public function arriostrados_add_carrito($id){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartc12 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','Darr-c12')->first();
        $cartest = Cart_product::where('quotation_id', $Quotation_Id)->where('type','Darr-est')->first();
        
        if($cartc12){
            Cart_product::destroy($cartC12->id);
        }
        if($cartest){
            Cart_product::destroy($cartest->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $Rolados=quotation_drive_in_arriostrado::where('quotation_id','=',$Quotation_Id)->where('description','ROLADO C-12')->first();
        $Estructurales=quotation_drive_in_arriostrado::where('quotation_id','=',$Quotation_Id)->where('description','ESTRUCTURAL')->first();
        
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='ARRIOSTRADO ROLADO C-12';
        $Cart_product->type='Darr-c12';
        $Cart_product->unit_price=$Rolados->total_price/$Rolados->amount;
        $Cart_product->total_price=$Rolados->total_price;
        $Cart_product->quotation_id=$Rolados->quotation_id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$Rolados->amount;
        $Cart_product->sku=$Rolados->sku;
        $Cart_product->save();

        $Cart_product= new Cart_product();
        $Cart_product->name='ARRIOSTRADO ESTRUCTURAL';
        $Cart_product->type='Darr-est';
        $Cart_product->unit_price=$Estructurales->total_price/$Rolados->amount;
        $Cart_product->total_price=$Estructurales->total_price;
        $Cart_product->quotation_id=$Estructurales->quotation_id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$Estructurales->amount;
        $Cart_product->sku=$Estructurales->sku;
        $Cart_product->save();
        
        return redirect()->route('drivein.show',$Quotation_Id);
    }

}
