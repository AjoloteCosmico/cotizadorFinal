<?php

namespace App\Http\Controllers;

use App\Models\Joist;
use App\Models\PriceList;
use App\Models\PriceListScrew;
use App\Models\SelectiveJoistLr;
use App\Models\TypeLRJoist;
use App\Models\TypeLRJoistCaliber;
use App\Models\TypeLRJoistCamber;
use App\Models\TypeLRJoistCrossbarLength;
use App\Models\TypeLRJoistLength;
use App\Models\TypeLRJoistLoadingCapacity;
use App\Models\Cart_product;
use Illuminate\Support\Facades\Auth;
use App\Models\Quotation;
use Illuminate\Http\Request;

class TypeLRJoistController extends Controller
{
    public function caliber14_show($id)
    {
        $Quotation_Id = $id;
        $Joists = Joist::where('joist', 'Tipo LR')->first();
        $Calibers = TypeLRJoistCaliber::where('caliber', '14')->get();
        $Lengths = TypeLRJoistLength::all();
        $Cambers = TypeLRJoistCamber::all();
        $CrossbarLengths = TypeLRJoistCrossbarLength::all();
        $LoadingCapacities = TypeLRJoistLoadingCapacity::all();
        $TypeLJoists = TypeLRJoist::where('caliber', '14')->get();

        return view('quotes.selectivo.joists.typelrjoists.caliber14.index', compact(
            'Joists',
            'Calibers',
            'Lengths',
            'Cambers',
            'CrossbarLengths',
            'LoadingCapacities',
            'TypeLJoists',
            'Quotation_Id'
        ));
    }

    public function caliber14_calc(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'weight' => 'required',
            'joist_type' => 'required',
            'caliber' => 'required',
        ];
        $messages = [
            'amount.required' => 'Capture una cantidad válida',
            'weight.required' => 'Capture la carga requerida',
            'joist_type.required' => 'Elija el tipo de Viga',
            'caliber.required' => 'Elija el Calibre de la Viga',
        ];
        $request->validate($rules, $messages);

        $Quotation_Id = $request->Quotation_Id;
        $Amount = $request->amount;
        $Weight = $request->weight;
        $JoistType = $request->joist_type;
        $Length = $request->length;
        $Caliber = $request->caliber;
        $Increment = $Weight * 0.07;
        $WeightIncrement = $Weight + $Increment;
        $Cambers = TypeLRJoistLoadingCapacity::where('crossbar_length', '>=', $Length)->where('loading_capacity', '>=', $WeightIncrement)->first();
        if($Cambers){
            $TypeLJoists = TypeLRJoist::where('caliber','14')->where('camber', $Cambers->camber)->where('length', $Length)->first();
            //Optimized
            $PriceList = PriceList::where('system', 'SELECTIVO')->where('piece', 'VIGA')->where('caliber', '14')->first();
            $Import =  $PriceList->cost * $PriceList->f_total * $TypeLJoists->weight;
            $Clavijas = PriceListScrew::where('description', 'CLAVIJA DE SEGURIDAD PARA VIGAS')->first();
            $CostoClavija = $Clavijas->cost * $Clavijas->f_total;
            $CantidadClavijas = $Amount * 2;
            $CostoTotalClavijas = $CantidadClavijas * $CostoClavija;

            $SJLR = SelectiveJoistLr::where('quotation_id', $Quotation_Id)->first();
            if($SJLR){
                $SJLR->amount = $Amount;
                $SJLR->caliber = $Caliber;
                $SJLR->loading_capacity = $Weight;
                $SJLR->type_joist = $JoistType;
                $SJLR->length_meters = $Length;
                $SJLR->camber = $TypeLJoists->camber;
                $SJLR->weight_kg = $TypeLJoists->weight;
                $SJLR->m2 = $TypeLJoists->m2;
                $SJLR->length = $TypeLJoists->length;
                $SJLR->sku = $TypeLJoists->sku;
                $SJLR->unit_price = $Import;
                $SJLR->total_price = $Import*$Amount + $CostoTotalClavijas;
                $SJLR->save();
            }else{
                $SJLR = new SelectiveJoistLr();
                $SJLR->quotation_id = $Quotation_Id;
                $SJLR->amount = $Amount;
                $SJLR->caliber = $Caliber;
                $SJLR->loading_capacity = $Weight;
                $SJLR->type_joist = $JoistType;
                $SJLR->length_meters = $Length;
                $SJLR->camber = $TypeLJoists->camber;
                $SJLR->weight_kg = $TypeLJoists->weight;
                $SJLR->m2 = $TypeLJoists->m2;
                $SJLR->length = $TypeLJoists->length;
                $SJLR->sku = $TypeLJoists->sku;
                $SJLR->unit_price = $Import;
                $SJLR->total_price = $Import*$Amount + $CostoTotalClavijas;
                $SJLR->save();
            }
            echo "  //Factor: ".$PriceList->f_total.' '.$PriceList->description.$PriceList->type.$PriceList->caliber; 
            echo " //precio acero: $".$PriceList->cost;
            echo " //precio unit sin f_total: $".$Import / $PriceList->f_total ;
            echo '<br> //Peso: '.$TypeLJoists->weight;
            echo "<br> //Costo clavija $". $Clavijas->cost."// Factor clavija: ".$Clavijas->f_total; 
            
            return view('quotes.selectivo.joists.typelrjoists.caliber14.store', compact(
                'Amount',
                'Weight',
                'JoistType',
                'Length',
                'Caliber',
                'WeightIncrement',
                'Cambers',
                'TypeLJoists',
                'Import',
                'Quotation_Id',
                'CantidadClavijas',
                'CostoTotalClavijas',
            ));
        }else{
            return redirect()->route('menujoists.show')->with('no_existe', 'ok');
        }        
    }

    public function store(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'weight' => 'required',
            'joist_type' => 'required',
            'caliber' => 'required',
        ];
        $messages = [
            'amount.required' => 'Capture una cantidad válida',
            'weight.required' => 'Capture la carga requerida',
            'joist_type.required' => 'Elija el tipo de Viga',
            'caliber.required' => 'Elija el Calibre de la Viga',
        ];
        $request->validate($rules, $messages);
        $request->validate($rules, $messages);

        $Quotation_Id = $request->Quotation_Id;
        $Amount = $request->amount;
        $Caliber = $request->caliber;
        $Length = $request->length;
        $Weight = $request->weight;
        $JoistType = $request->joist_type;
        $Increment = $Weight * 0.07;
        $WeightIncrement = $Weight + $Increment;
        $Cambers = TypeLRJoistLoadingCapacity::where('crossbar_length', '>=', $Length)->where('loading_capacity', '>=', $WeightIncrement)->first();

        if($Cambers){
            $Camber=$Cambers->camber;
           
            $TypeLJoists = TypeLRJoist::where('caliber',$Caliber)->where('camber', $Cambers->camber)->where('length', $Length)->first();
            // dd($Cambers,$WeightIncrement,$Length,$TypeLJoists);
            //Optimized
            $PriceList = PriceList::where('system', 'SELECTIVO')->where('piece', 'VIGA')->where('caliber', $Caliber)->first();
            $Import =  $PriceList->cost * $PriceList->f_total * $TypeLJoists->weight;
            
            $Clavijas = PriceListScrew::where('description', 'CLAVIJA DE SEGURIDAD PARA VIGAS')->first();
            $CostoClavija = $Clavijas->cost * $Clavijas->f_total;
            $CantidadClavijas = $Amount * 2;
            $CostoTotalClavijas = $CantidadClavijas * $CostoClavija;
            $CostoTotal = $CostoTotalClavijas + $Import;
            $SJLR = SelectiveJoistLr::where('quotation_id', $Quotation_Id)->first();
            if($SJLR){
                $SJLR->amount = $Amount;
                $SJLR->caliber = $Caliber;
                $SJLR->loading_capacity = $Weight;
                $SJLR->type_joist = $JoistType;
                $SJLR->length_meters = $Length;
                $SJLR->camber = $TypeLJoists->camber;
                $SJLR->weight_kg = $TypeLJoists->weight;
                $SJLR->m2 = $TypeLJoists->m2;
                $SJLR->length = $TypeLJoists->length;
                $SJLR->sku = $TypeLJoists->sku;
                $SJLR->unit_price = $Import;
                $SJLR->total_price = $Import*$Amount + $CostoTotalClavijas/2;
                $SJLR->save();
            }else{
                $SJLR = new SelectiveJoistLr();
                $SJLR->quotation_id = $Quotation_Id;
                $SJLR->amount = $Amount;
                $SJLR->caliber = $Caliber;
                $SJLR->loading_capacity = $Weight;
                $SJLR->type_joist = $JoistType;
                $SJLR->length_meters = $Length;
                $SJLR->camber = $TypeLJoists->camber;
                $SJLR->weight_kg = $TypeLJoists->weight;
                $SJLR->m2 = $TypeLJoists->m2;
                $SJLR->length = $TypeLJoists->length;
                $SJLR->sku = $TypeLJoists->sku;
                $SJLR->unit_price = $Import;
                $SJLR->total_price = $Import*$Amount + $CostoTotalClavijas/2;
                $SJLR->save();
            }
            echo "  //Factor: ".$PriceList->f_total.' '.$PriceList->description.$PriceList->type.$PriceList->caliber; 
            echo " //precio acero: $".$PriceList->cost;
            echo " //precio unit sin f_total: $".$Import / $PriceList->f_total ;
            echo '<br> //Peso: '.$TypeLJoists->weight;
            echo "<br> //Costo clavija $". $Clavijas->cost."// Factor clavija: ".$Clavijas->f_total; 
            
            return view('quotes.selectivo.joists.typelrjoists.store', compact(
                'Amount',
                'Caliber',
                'Length',
                'Camber',
                'Weight',
                'JoistType',
                'Increment',
                'WeightIncrement',
                'TypeLJoists',
                'Import',
                'Quotation_Id',
                'CantidadClavijas',
                'CostoTotalClavijas',
                'CostoTotal','SJLR'
            ));
        }else{
            return redirect()->route('menujoists.show')->with('no_existe', 'ok');
        } 
        
    }

    public function show($id)
    {
        $Quotation_Id = $id;
        $Joists = Joist::where('joist', 'Tipo LR')->first();
        $Calibers = TypeLRJoistCaliber::where('caliber', '<>', '14')->get();
        $Lengths = TypeLRJoist::select('length')->distinct()->get();
        $Cambers = TypeLRJoistCamber::all();
        $CrossbarLengths = TypeLRJoistCrossbarLength::all();
        $LoadingCapacities = TypeLRJoistLoadingCapacity::all();
        $TypeLJoists = TypeLRJoist::all();

        return view('quotes.selectivo.joists.typelrjoists.index', compact(
            'Joists',
            'Calibers',
            'Lengths',
            'Cambers',
            'CrossbarLengths',
            'LoadingCapacities',
            'TypeLJoists',
            'Quotation_Id'
        ));
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
    public function add_carrito($id){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','SJLR')->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = SelectiveJoistLR::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='VIGA TIPO LR'.$SJL2->model;
        $Cart_product->type='SJLR';
        $Cart_product->unit_price=$SJL2->unit_price;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->save();
        //ligar las instancias
        $SJL2->cart_id=$Cart_product->id();
        $SJL2->save();
        return redirect()->route('menujoists.show',$Quotation_Id);
    
    }
    
    public function add_carrito14($id){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','SJLR14')->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = SelectiveJoistLRCaliber14::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='VIGA TIPO LR cal 14'.$SJL2->model;
        $Cart_product->type='SJLR14';
        $Cart_product->unit_price=$SJL2->unit_price;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->save();
        //ligar las instancias
        $SJL2->cart_id=$Cart_product->id();
        $SJL2->save();
        return redirect()->route('menujoists.show',$Quotation_Id);
    
    }
}
