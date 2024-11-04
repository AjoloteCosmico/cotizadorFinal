<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\PriceFrame;
use App\Models\Buckling;
use App\Models\Depth;
use App\Models\Height;
use App\Models\PriceList;
use App\Models\PriceListScrew;
use App\Models\SelectiveHeavyLoadFrame;
use App\Models\Quotation;
use App\Models\Cart_product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class FramesController extends Controller
{
    public function show($id)
    {
        $Quotation_Id = $id;
        $depth = Depth::all();
        $buckling = Buckling::all();
        $height = Height::all();

        return view('quotes.selectivo.frames.heavyloads.index', compact(
            'depth',
            'buckling',
            'height',
            'Quotation_Id'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'weight' => 'required',
            'caliber' => 'required',
            'buckling' => 'required',
            'depth' => 'required',
            'height' => 'required',
        ];

        $messages = [
            'amount.required' => 'Favor de capturar el número de marcos a cotizar',
            'weight.required' => 'Favor de capturar el Peso',
        ];

        $request->validate($rules, $messages);

        $Quotation_Id = $request->Quotation_Id;
        $Cantidad = $request->amount;
        $Calibre = $request->caliber;
        $Pandeo = $request->buckling;
        $Peso = $request->weight;
        $PesoA = ($Peso * 0.1) + $Peso;

        $Models = Frame::where('caliber', $Calibre)->where('buckling', $Pandeo)->where('weight', '>=', $PesoA)->first();
        if($Models){
            $Modelo = $Models->model;
            $Profundidad = $request->depth;
            $Altura = $request->height;

            $Data = PriceFrame::where('caliber', $Calibre)->where('model', $Modelo)->where('depth', $Profundidad)->where('height', $Altura)->first();
            $PriceList = PriceList::where('system', 'SELECTIVO')->where('piece', 'MARCO')->where('caliber', $Calibre)->first();
            // dd($PriceList);
             
            if($Data){
                $Price = $PriceList->cost * $PriceList->f_total;
                $TotalPrice = $Data->total_kg * $Price;
            
                $Postes = $Data->poles;
                $Travesanos = $Data->crossbars;
                $Diagonales = $Data->diagonals;
                $Placas = $Data->plates;
                $TornTravDiag = ($Travesanos + $Diagonales) * 4;
                $TornPlacas = $Placas * 2;
                $PriceListScrewsTravDiag = PriceListScrew::where('description', 'TORNILLO Y TUERCA 5/16 I X 3/4 IN G5 GALV')->first();
                $PriceListScrewCostTravDiag = $PriceListScrewsTravDiag->cost * $PriceListScrewsTravDiag->f_total;
                $CostTornTravDiag = $TornTravDiag * $PriceListScrewCostTravDiag;
                $TotTornTravDiag = $Cantidad * $TornTravDiag;
                $TotCostTornTravDiag = $Cantidad * $CostTornTravDiag;
                $PriceListScrewsPlacas = PriceListScrew::where('description', 'TORNILLO Y TUERCA 3/8 IN X 1 IN G5 GALV')->first();
                $PriceListScrewCostPlacas = $PriceListScrewsPlacas->cost * $PriceListScrewsPlacas->f_total;
                $CostTornPlacas = $TornPlacas * $PriceListScrewCostPlacas;
                $TotTornPlacas = $Cantidad * $TornPlacas;
                $TotCostTornPlacas = $Cantidad * $CostTornPlacas;
                $Precio = $TotalPrice ; 
                // $Precio = $TotalPrice + $CostTornPlacas + $CostTornTravDiag;
                $Precio_Total = $Cantidad * $Precio;
                $Calzas = 4;
                $CostoCalzas = PriceList::where('piece', 'CALZAS')->first();
                $CostoCalza = $CostoCalzas->cost * $CostoCalzas->f_total;
                $Taquetes = 4;
                $CostoTaquetes = PriceListScrew::where('description', 'TAQUETE')->first();
                $CostoTaquete = $CostoTaquetes->cost * $CostoTaquetes->f_total;

                $Total_Peso = $Cantidad * $Peso;
                $Total_Postes = $Cantidad * $Data->poles;
                $Total_Travesanos = $Cantidad * $Data->crossbars;
                $Total_Diagonales = $Cantidad * $Data->diagonals;
                $Total_Placas = $Cantidad * $Data->plates;                
                $Total_Acero_Kg = $Cantidad * $Data->steel_weight_kg;
                $Total_Solera_Kg = $Cantidad * $Data->solera_weight_kg;
                $Total_Kg = $Cantidad * $Data->total_kg;
                $Total_m2 = $Cantidad * $Data->total_m2;
                $Sku = $Data->sku;
                $TotalCalzas = $Cantidad * 4;
                $TotalTaquetes = $Cantidad * 4;
                $CostoTotalCalza = ($CostoCalza * 4) * $Cantidad;
                $CostoTotalTaquete = ($CostoTaquete * 4) * $Cantidad;

                $SHLF = SelectiveHeavyLoadFrame::where('quotation_id', $Quotation_Id)->first();
                if($SHLF){
                    $SHLF->amount = $Cantidad;
                    $SHLF->model = $Modelo;
                    $SHLF->caliber = $Calibre;
                    $SHLF->depth = $Profundidad;
                    $SHLF->total_load_kg = $Total_Peso;
                    $SHLF->total_poles = $Total_Postes;
                    $SHLF->total_crossbars = $Total_Travesanos;
                    $SHLF->total_diagonals = $Total_Diagonales;
                    $SHLF->total_plates = $Total_Placas;
                    $SHLF->total_kg = $Total_Kg;
                    $SHLF->total_m2 = $Total_m2;
                    $SHLF->sku = $Sku;  
                    // $SHLF->total_price = $Precio_Total + $CostoTotalCalza + $CostoTotalTaquete;
                    $SHLF->total_price = $Precio_Total + $CostoTotalCalza + $CostoTotalTaquete;
                    $SHLF->save();
                    
                    
                }else{
                    $SHLF = new SelectiveHeavyLoadFrame();
                    $SHLF->quotation_id = $Quotation_Id;
                    $SHLF->amount = $Cantidad;
                    $SHLF->model = $Modelo;
                    $SHLF->caliber = $Calibre;
                    $SHLF->depth = $Profundidad;
                    $SHLF->total_load_kg = $Total_Peso;
                    $SHLF->total_poles = $Total_Postes;
                    $SHLF->total_crossbars = $Total_Travesanos;
                    $SHLF->total_diagonals = $Total_Diagonales;
                    $SHLF->total_plates = $Total_Placas;
                    $SHLF->total_kg = $Total_Kg;
                    $SHLF->total_m2 = $Total_m2;
                    $SHLF->sku = $Sku;
                    
                    // $SHLF->total_price = $Precio_Total + $CostoTotalCalza + $CostoTotalTaquete;
                    $SHLF->total_price = $Precio_Total + $CostoTotalCalza + $CostoTotalTaquete;
                    $SHLF->save();
                }
                
                $Precio_unit_sn_factor = ($Data->total_kg * $PriceList->cost)+ $CostTornPlacas + $CostTornTravDiag;
                $Precio_sin_factor = $Cantidad * $Precio_unit_sn_factor ;
                echo "  //factor: ".$PriceList->f_total.' '.$PriceList->description.$PriceList->type.$PriceList->caliber; 
                echo " //precio acero: $".$PriceList->cost;
                echo " //peso total: ".$Data->total_kg ;
                echo " //total sn f_total: $".$Precio_sin_factor ;
                echo "<br> //Costo calzas: $".$CostoTotalCalza;
                echo "/CostoTaquetes: $".$CostoTotalTaquete;
                return view('quotes.selectivo.frames.heavyloads.store', compact(
                    'Cantidad',
                    'Calibre',
                    'Pandeo',
                    'Peso',
                    'Modelo',
                    'Profundidad',
                    'Altura',
                    'Data',
                    'Total_Peso',
                    'Total_Postes',
                    'Total_Travesanos',
                    'Total_Diagonales',
                    'Total_Placas',
                    'Precio_Total',
                    'Total_Acero_Kg',
                    'Total_Solera_Kg',
                    'Total_Kg',
                    'Total_m2',
                    'Quotation_Id',
                    'Postes',
                    'Travesanos',
                    'Diagonales',
                    'Placas',
                    'TornTravDiag',
                    'TornPlacas',
                    'PriceListScrewsTravDiag',
                    'PriceListScrewCostTravDiag',
                    'CostTornTravDiag',
                    'TotTornTravDiag',
                    'TotCostTornTravDiag',
                    'PriceListScrewsPlacas',
                    'PriceListScrewCostPlacas',
                    'CostTornPlacas',
                    'TotTornPlacas',
                    'TotCostTornPlacas',
                    'Precio',
                    'Calzas',
                    'CostoCalza',
                    'Taquetes',
                    'CostoTaquete',
                    'CostoTotalCalza',
                    'CostoTotalTaquete',
                    'TotalCalzas',
                    'TotalTaquetes',
                ));
            }else{
                return redirect()->route('frames.show',$Quotation_Id)->with('no_existe', 'ok');
            }
        }
        else{
            return redirect()->route('frames.show',$Quotation_Id)->with('no_existe', 'ok');
        }
    }
    public function drive_show($id)
    {
        $Quotation_Id = $id;
        $depth = Depth::all();
        $buckling = Buckling::all();
        $height = Height::all();

        return view('quotes.drivein.frames.heavyloads.index', compact(
            'depth',
            'buckling',
            'height',
            'Quotation_Id'
        ));
    }

    public function drive_store(Request $request)
    {
        
        $rules = [
            'amount' => 'required',
            'weight' => 'required',
            'caliber' => 'required',
            'buckling' => 'required',
            'depth' => 'required',
            'height' => 'required',
        ];

        $messages = [
            'amount.required' => 'Favor de capturar el número de marcos a cotizar',
            'weight.required' => 'Favor de capturar el Peso',
        ];

        $request->validate($rules, $messages);

        $Quotation_Id = $request->Quotation_Id;
        $Cantidad = $request->amount;
        $Calibre = $request->caliber;
        $Pandeo = $request->buckling;
        $Peso = $request->weight;
        $PesoA = ($Peso * 0.1) + $Peso;

        $Models = Frame::where('caliber', $Calibre)->where('buckling', $Pandeo)->where('weight', '>=', $PesoA)->first();
        if($Models){
            // $Modelo = $Models->model;
            $Modelo="TC2";
            $Profundidad = $request->depth;
            $Altura = $request->height;

            $Data = PriceFrame::where('caliber', $Calibre)->where('model', $Modelo)->where('depth', $Profundidad)->where('height', $Altura)->first();
            $PriceList = PriceList::where('system', 'SELECTIVO')->where('piece', 'MARCO')->where('caliber', $Calibre)->first();
            
            if($Data){
                $Total_Peso = $Cantidad * $Peso;
                $Total_Postes = $Cantidad * $Data->poles;
                $Total_Travesanos = $Cantidad * $Data->crossbars;
                $Total_Diagonales = $Cantidad * $Data->diagonals;
                $Total_Placas = $Cantidad * $Data->plates;
                $Precio_Total = $Cantidad * $PriceList->cost * $PriceList->f_total;
                $Total_Acero_Kg = $Cantidad * $Data->steel_weight_kg;
                $Total_Solera_Kg = $Cantidad * $Data->solera_weight_kg;
                $Total_Kg = $Cantidad * $Data->total_kg;
                $Total_m2 = $Cantidad * $Data->total_m2;
                $Sku = $Data->sku;

                $SHLF = SelectiveHeavyLoadFrame::where('quotation_id', $Quotation_Id)->first();
                if($SHLF){
                    $SHLF->amount = $Cantidad;
                    $SHLF->model = $Modelo;
                    $SHLF->caliber = $Calibre;
                    $SHLF->total_load_kg = $Total_Peso;
                    $SHLF->total_poles = $Total_Postes;
                    $SHLF->total_crossbars = $Total_Travesanos;
                    $SHLF->total_diagonals = $Total_Diagonales;
                    $SHLF->total_plates = $Total_Placas;
                    $SHLF->total_kg = $Total_Kg;
                    $SHLF->total_m2 = $Total_m2;
                    $SHLF->sku = $Sku;
                    $SHLF->total_price = $Precio_Total;
                    $SHLF->save();
                }else{
                    $SHLF = new SelectiveHeavyLoadFrame();
                    $SHLF->quotation_id = $Quotation_Id;
                    $SHLF->amount = $Cantidad;
                    $SHLF->model = $Modelo;
                    $SHLF->caliber = $Calibre;
                    $SHLF->total_load_kg = $Total_Peso;
                    $SHLF->total_poles = $Total_Postes;
                    $SHLF->total_crossbars = $Total_Travesanos;
                    $SHLF->total_diagonals = $Total_Diagonales;
                    $SHLF->total_plates = $Total_Placas;
                    $SHLF->total_kg = $Total_Kg;
                    $SHLF->total_m2 = $Total_m2;
                    $SHLF->sku = $Sku;
                    $SHLF->total_price = $Precio_Total;
                    $SHLF->save();
                }

                return view('quotes.drivein.frames.heavyloads.store', compact(
                    'Cantidad',
                    'Calibre',
                    'Pandeo',
                    'Peso',
                    'Modelo',
                    'Profundidad',
                    'Altura',
                    'Data',
                    'Total_Peso',
                    'Total_Postes',
                    'Total_Travesanos',
                    'Total_Diagonales',
                    'Total_Placas',
                    'Precio_Total',
                    'Total_Acero_Kg',
                    'Total_Solera_Kg',
                    'Total_Kg',
                    'Total_m2',
                    'Quotation_Id'
                ));
            }else{
                return redirect()->route('frames.show',$Quotation_Id)->with('no_existe', 'ok');
            }
        }
        else{
            return redirect()->route('frames.show',$Quotation_Id)->with('no_existe', 'ok');
        }
    }


    public function drive_add_carrito($id){
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartSHLF = Cart_product::where('quotation_id', $Quotation_Id)->where('type','DHLF')->first();
        if($cartSHLF){
            Cart_product::destroy($cartSHLF->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SHLF = SelectiveHeavyLoadFrame::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='MARCO DRIVE IN CARGA PESADA'.$SHLF->model;
        $Cart_product->type='DHLF';
        $Cart_product->unit_price=$SHLF->total_price / $SHLF->amount ;
        $Cart_product->total_price=$SHLF->total_price;
        $Cart_product->sku=$SHLF->sku;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SHLF->amount;
        $Cart_product->save();
        
        return redirect()->route('drivein.show',$Quotation_Id);
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
}
