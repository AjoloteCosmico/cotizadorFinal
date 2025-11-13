<?php

namespace App\Http\Controllers;

use App\Models\Joist;
use App\Models\PriceList;
use App\Models\PriceListScrew;
use App\Models\SelectiveJoistL25;
use App\Models\SelectiveJoistL25Caliber14;
use App\Models\TypeL25Joist;
use App\Models\TypeL25JoistCaliber;
use App\Models\TypeL25JoistCamber;
use App\Models\TypeL25JoistCrossbarLength;
use App\Models\TypeL25JoistLength;
use App\Models\TypeL25JoistLoadingCapacity;
use DB;
use App\Models\Costo;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Cart_product;
use Illuminate\Support\Facades\Auth;

class TypeL25JoistController extends Controller
{
    public function caliber14_show($id)
    {
        $Quotation_Id = $id;
        $Joists = Joist::where('joist', 'Tipo L 2.5')->first();
        $Calibers = TypeL25JoistCaliber::where('caliber', '14')->get();
        $Lengths = TypeL25JoistLength::all();
        $Cambers = TypeL25JoistCamber::all();
        $CrossbarLengths = TypeL25JoistCrossbarLength::all();
        $LoadingCapacities = TypeL25JoistLoadingCapacity::all();
        $TypeLJoists = TypeL25Joist::where('caliber', '14')->get();


        return view('quotes.selectivo.joists.typel25joists.caliber14.index', compact(
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
        $Cambers = TypeL25JoistLoadingCapacity::where('crossbar_length', '>=', $Length)->where('loading_capacity', '>=', $WeightIncrement)->first();
        if($Cambers){
            $TypeLJoists = TypeL25Joist::where('caliber','14')->where('camber', $Cambers->camber)->where('length', $Length)->first();
            //Optimized
            $PriceList = PriceList::where('system', 'SELECTIVO')->where('piece', 'VIGA')->where('caliber', '14')->first();
           
            $Import =  $PriceList->cost * $PriceList->f_total * $TypeLJoists->weight;
            $Clavijas = PriceListScrew::where('description', 'CLAVIJA DE SEGURIDAD PARA VIGAS')->first();
            $CostoClavija = $Clavijas->cost * $Clavijas->f_total;
            $CantidadClavijas = $Amount * 2;
            $CostoTotalClavijas = $CantidadClavijas * $CostoClavija;

            $SJL25C14 = SelectiveJoistL25Caliber14::where('quotation_id', $Quotation_Id)->first();
            if($SJL25C14){
                $SJL25C14->amount = $Amount;
                $SJL25C14->caliber = $Caliber;
                $SJL25C14->loading_capacity = $Weight;
                $SJL25C14->type_joist = $JoistType;
                $SJL25C14->length_meters = $Length;
                $SJL25C14->camber = $TypeLJoists->camber;
                $SJL25C14->weight_kg = $TypeLJoists->weight;
                $SJL25C14->m2 = $TypeLJoists->m2;
                $SJL25C14->length = $TypeLJoists->length;
                $SJL25C14->sku = $TypeLJoists->sku;
                $SJL25C14->unit_price = $Import;
                $SJL25C14->total_price = $Import*$Amount + $CostoTotalClavijas;
                $SJL25C14->save();
            }else{
                $SJL25C14 = new SelectiveJoistL25Caliber14();
                $SJL25C14->quotation_id = $Quotation_Id;
                $SJL25C14->amount = $Amount;
                $SJL25C14->caliber = $Caliber;
                $SJL25C14->loading_capacity = $Weight;
                $SJL25C14->type_joist = $JoistType;
                $SJL25C14->length_meters = $Length;
                $SJL25C14->camber = $TypeLJoists->camber;
                $SJL25C14->weight_kg = $TypeLJoists->weight;
                $SJL25C14->m2 = $TypeLJoists->m2;
                $SJL25C14->length = $TypeLJoists->length;
                $SJL25C14->sku = $TypeLJoists->sku;
                $SJL25C14->unit_price = $Import;
                $SJL25C14->total_price = $Import*$Amount + $CostoTotalClavijas;
                $SJL25C14->save();
            }
            echo "  //Factor: ".$PriceList->f_total.' '.$PriceList->description.$PriceList->type.$PriceList->caliber; 
            echo " //precio acero: $".$PriceList->cost;
            echo " //precio unit sin f_total: $".$Import / $PriceList->f_total ;
            echo '<br> //Peso: '.$TypeLJoists->weight;
            echo "<br> //Costo clavija $". $Clavijas->cost."// Factor clavija: ".$Clavijas->f_total; 
            $Precio_sin_factor=($Import / $PriceList->f_total)*$Amount;
            $Type='SJL2514';
            $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->delete();
               
            // VIGA
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=> $Caliber,
                     'sku'=>$TypeLJoists->sku,'cant'=>$Amount,'description'=>'VIGA TIPO 2L CALIBRE'.$Caliber,
                    'precio_unit'=>$Import,'precio_total'=>$Import*$Amount, 'factor'=>$PriceList->f_total,
                    'costo_unit'=>$Import / $PriceList->f_total ,'costo_total'=>($Import / $PriceList->f_total)*$Amount ,
                    'kg_unit'=>$TypeLJoists->weight, 'm2_unit'=>$TypeLJoists->m2
                    ]  
                );

                DB::table('costos')->insert(
                    [['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=> 'GALVANIZADAS','factor'=>$Clavijas->f_total,
                     'sku'=>$Clavijas->sku ,'cant'=>2*$Amount,'description'=>'CLAVIJA DE SEGURIDAD',
                    'precio_unit'=>$Clavijas->cost * $Clavijas->f_total,'precio_total'=>$Clavijas->cost * $Clavijas->f_total*2*$Amount,
                    'costo_unit'=>$Clavijas->cost,'costo_total'=>$Clavijas->cost * 2*$Amount,
                    ]]
                );
            return view('quotes.selectivo.joists.typel25joists.caliber14.store', compact(
                'Precio_sin_factor',
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
            'caliber' => 'required',
            'length' => 'required',
            'camber' => 'required',
            'skate' => 'required',
            'weight' => 'required',
            'joist_type' => 'required',
        ];
        $messages = [
            'amount.required' => 'Capture una cantidad válida',
            'caliber.required' => 'Seleccione el Calibre de la Viga',
            'lenght.required' => 'Seleccione el Largo',
            'camber.required' => 'Seleccione el Peralte',
            'skate.required' => 'Capture el Patín',
            'weight.required' => 'Capture la Capacidad de carga',            
            'joist_type.required' => 'Seleccione el tipo de Viga',
        ];
        $request->validate($rules, $messages);

        $Quotation_Id = $request->Quotation_Id;
        $Amount = $request->amount;
        $Caliber = $request->caliber;
        $Length = $request->length;
        $Camber = $request->camber;
        $Skate = $request->skate;
        $Weight = $request->weight;
        $JoistType = $request->joist_type;
        $Increment = $Weight * 0.07;
        $WeightIncrement = $Weight + $Increment;
        
        $TypeLJoists = TypeL25Joist::where('caliber',$Caliber)->where('camber', $Camber)->where('length', $Length)->first();
        //Optimized
        $PriceList = PriceList::where('system', 'SELECTIVO')->where('piece', 'VIGA')->where('caliber', $Caliber)->first();
        $Import =  $PriceList->cost * $PriceList->f_total * $TypeLJoists->weight;
        $Clavijas = PriceListScrew::where('description', 'CLAVIJA DE SEGURIDAD PARA VIGAS')->first();
        $CostoClavija = $Clavijas->cost * $Clavijas->f_total;
        $CantidadClavijas = $Amount * 2;
        $CostoTotalClavijas = $CantidadClavijas * $CostoClavija;

        $SJL25 = SelectiveJoistL25::where('quotation_id', $Quotation_Id)->first();
        if($SJL25){
            $SJL25->amount = $Amount;
            $SJL25->caliber = $Caliber;
            $SJL25->skate = $Skate;
            $SJL25->loading_capacity = $Weight;
            $SJL25->type_joist = $JoistType;
            $SJL25->length_meters = $Length;
            $SJL25->camber = $TypeLJoists->camber;
            $SJL25->weight_kg = $TypeLJoists->weight;
            $SJL25->m2 = $TypeLJoists->m2;
            $SJL25->length = $TypeLJoists->length;
            $SJL25->sku = $TypeLJoists->sku;
            $SJL25->unit_price = $Import;
            $SJL25->total_price = $Import*$Amount + $CostoTotalClavijas;
            $SJL25->save();
        }else{
            $SJL25 = new SelectiveJoistL25();
            $SJL25->quotation_id = $Quotation_Id;
            $SJL25->amount = $Amount;
            $SJL25->caliber = $Caliber;
            $SJL25->skate = $Skate;
            $SJL25->loading_capacity = $Weight;
            $SJL25->type_joist = $JoistType;
            $SJL25->length_meters = $Length;
            $SJL25->camber = $TypeLJoists->camber;
            $SJL25->weight_kg = $TypeLJoists->weight;
            $SJL25->m2 = $TypeLJoists->m2;
            $SJL25->length = $TypeLJoists->length;
            $SJL25->sku = $TypeLJoists->sku;
            $SJL25->unit_price = $Import;
            $SJL25->total_price = $Import*$Amount + $CostoTotalClavijas;
            $SJL25->save();
        }
        echo "  //Factor: ".$PriceList->f_total.' '.$PriceList->description.$PriceList->type.$PriceList->caliber; 
        echo " //precio acero: $".$PriceList->cost;
        echo " //precio unit sin f_total: $".$Import / $PriceList->f_total ;
        echo '<br> //Peso: '.$TypeLJoists->weight;
        echo "<br> //Costo clavija $". $Clavijas->cost."// Factor clavija: ".$Clavijas->f_total; 
        $Precio_sin_factor=($Import / $PriceList->f_total)*$Amount;
        $Type='SJL25';
            $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->delete();
               
            // VIGA
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=> $Caliber,
                     'sku'=>$TypeLJoists->sku,'cant'=>$Amount,'description'=>'VIGA TIPO 2L CALIBRE'.$Caliber,
                    'precio_unit'=>$Import,'precio_total'=>$Import*$Amount, 'factor'=>$PriceList->f_total,
                    'costo_unit'=>$Import / $PriceList->f_total ,'costo_total'=>($Import / $PriceList->f_total)*$Amount ,
                    'kg_unit'=>$TypeLJoists->weight, 'm2_unit'=>$TypeLJoists->m2
                    ]  
                );

                DB::table('costos')->insert(
                    [['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=> 'GALVANIZADAS','factor'=>$Clavijas->f_total,
                     'sku'=>$Clavijas->sku ,'cant'=>2*$Amount,'description'=>'CLAVIJA DE SEGURIDAD',
                    'precio_unit'=>$Clavijas->cost * $Clavijas->f_total,'precio_total'=>$Clavijas->cost * $Clavijas->f_total*2*$Amount,
                    'costo_unit'=>$Clavijas->cost,'costo_total'=>$Clavijas->cost * 2*$Amount,
                    ]]
                );
        return view('quotes.selectivo.joists.typel25joists.store', compact(
            'Precio_sin_factor',
            'Amount',
            'Caliber',
            'Length',
            'Camber',
            'Skate',
            'Weight',
            'JoistType',
            'Increment',
            'WeightIncrement',
            'TypeLJoists',
            'Import',
            'Quotation_Id',
            'CantidadClavijas',
            'CostoTotalClavijas',
        ));
    }

    public function show($id)
    {
        $Quotation_Id = $id;
        $Joists = Joist::where('joist', 'Tipo L 2.5')->first();
        $Calibers = TypeL25JoistCaliber::where('caliber', '<>', '14')->get();
        $Lengths = TypeL25JoistLength::all();
        $Cambers = TypeL25JoistCamber::all();
        $CrossbarLengths = TypeL25JoistCrossbarLength::all();
        $LoadingCapacities = TypeL25JoistLoadingCapacity::all();
        $TypeLJoists = TypeL25Joist::all();

        return view('quotes.selectivo.joists.typel25joists.index', compact(
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

    public function drive_store(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'caliber' => 'required',
            'length' => 'required',
            'camber' => 'required',
            'skate' => 'required',
            'weight' => 'required',
            'joist_type' => 'required',
        ];
        $messages = [
            'amount.required' => 'Capture una cantidad válida',
            'caliber.required' => 'Seleccione el Calibre de la Viga',
            'lenght.required' => 'Seleccione el Largo',
            'camber.required' => 'Seleccione el Peralte',
            'skate.required' => 'Capture el Patín',
            'weight.required' => 'Capture la Capacidad de carga',            
            'joist_type.required' => 'Seleccione el tipo de Viga',
        ];
        $request->validate($rules, $messages);

        $Quotation_Id = $request->Quotation_Id;
        $Amount = $request->amount;
        $Caliber = $request->caliber;
        $Length = $request->length;
        $Camber = $request->camber;
        $Skate = $request->skate;
        $Weight = $request->weight;
        $JoistType = $request->joist_type;
        $Increment = $Weight * 0.07;
        $WeightIncrement = $Weight + $Increment;
        
        $TypeLJoists = TypeL25Joist::where('caliber',$Caliber)->where('camber', $Camber)->where('length', $Length)->first();
        //Optimized
        $PriceList = PriceList::where('system', 'SELECTIVO')->where('piece', 'VIGA')->where('caliber', $Caliber)->first();
        $Import =  $PriceList->cost * $PriceList->f_total * $TypeLJoists->weight;
        
        $SJL25 = SelectiveJoistL25::where('quotation_id', $Quotation_Id)->first();
        if($SJL25){
            $SJL25->amount = $Amount;
            $SJL25->caliber = $Caliber;
            $SJL25->skate = $Skate;
            $SJL25->loading_capacity = $Weight;
            $SJL25->type_joist = $JoistType;
            $SJL25->length_meters = $Length;
            $SJL25->camber = $TypeLJoists->camber;
            $SJL25->weight_kg = $TypeLJoists->weight;
            $SJL25->m2 = $TypeLJoists->m2;
            $SJL25->length = $TypeLJoists->length;
            $SJL25->sku = $TypeLJoists->sku;
            $SJL25->unit_price = $Import;
            $SJL25->total_price = $Import*$Amount;
            $SJL25->save();
        }else{
            $SJL25 = new SelectiveJoistL25();
            $SJL25->quotation_id = $Quotation_Id;
            $SJL25->amount = $Amount;
            $SJL25->caliber = $Caliber;
            $SJL25->skate = $Skate;
            $SJL25->loading_capacity = $Weight;
            $SJL25->type_joist = $JoistType;
            $SJL25->length_meters = $Length;
            $SJL25->camber = $TypeLJoists->camber;
            $SJL25->weight_kg = $TypeLJoists->weight;
            $SJL25->m2 = $TypeLJoists->m2;
            $SJL25->length = $TypeLJoists->length;
            $SJL25->sku = $TypeLJoists->sku;
            $SJL25->unit_price = $Import;
            $SJL25->total_price = $Import*$Amount;
            $SJL25->save();
        }
        echo "  //Factor: ".$PriceList->f_total.' '.$PriceList->description.$PriceList->type.$PriceList->caliber; 
        echo " //precio acero: $".$PriceList->cost;
        echo " //precio unit sin f_total: $".$Import / $PriceList->f_total ;
        echo '<br> //Peso: '.$TypeLJoists->weight;
        echo "<br> //Costo clavija $". $Clavijas->cost."// Factor clavija: ".$Clavijas->f_total; 
        $Precio_sin_factor=($Import / $PriceList->f_total)*$Amount;
        return view('quotes.drivein.joists.typel25joists.store', compact(
            'Precio_sin_factor',
            'Amount',
            'Caliber',
            'Length',
            'Camber',
            'Skate',
            'Weight',
            'JoistType',
            'Increment',
            'WeightIncrement',
            'TypeLJoists',
            'Import',
            'Quotation_Id'
        ));
    }

    public function drive_show($id)
    {
        $Quotation_Id = $id;
        $Joists = Joist::where('joist', 'Tipo L 2.5')->first();
        $Calibers = TypeL25JoistCaliber::where('caliber', '<>', '14')->get();
        $Lengths = TypeL25JoistLength::all();
        $Cambers = TypeL25JoistCamber::all();
        $CrossbarLengths = TypeL25JoistCrossbarLength::all();
        $LoadingCapacities = TypeL25JoistLoadingCapacity::all();
        $TypeLJoists = TypeL25Joist::all();

        return view('quotes.drivein.joists.typel25joists.index', compact(
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
    public function add_carrito($id,$Costo){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','SJL25')->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = SelectiveJoistL25::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='VIGA TIPO L25'.$SJL2->model;
        $Cart_product->type='SJL25';
        $Cart_product->unit_price=$SJL2->unit_price;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        
        $Cart_product->costo_sn_factor=$Costo;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->save();
        //ligar las instancias
        $SJL2->cart_id=$Cart_product->id;
        $SJL2->save();
        return redirect()->route('menujoists.show',$Quotation_Id);
    
    }
    
    public function add_carrito14($id,$Costo){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','SJL2514')->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = SelectiveJoistL25Caliber14::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='VIGA TIPO L25 cal 14'.$SJL2->model;
        $Cart_product->type='SJL2514';
        $Cart_product->unit_price=$SJL2->unit_price;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->costo_sn_factor=$Costo;
        $Cart_product->save();
        //ligar las instancias
        $SJL2->cart_id=$Cart_product->id;
        $SJL2->save();
        return redirect()->route('menujoists.show',$Quotation_Id);
    
    }
}
