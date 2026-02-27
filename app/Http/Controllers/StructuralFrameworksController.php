<?php

namespace App\Http\Controllers;

use App\Models\StructuralFrameworks;
use App\Models\PriceStructuralFrameworks;
use App\Models\BucklingStructural;
use App\Models\DepthStructural;
use App\Models\HeightStructural;
use App\Models\PriceList;
use App\Models\PriceListScrew;
use App\Models\SelectiveStructuralFrame;
use App\Models\Cart_product;
use DB;
use App\Models\Costo;
use Illuminate\Http\Request;

class StructuralFrameworksController extends Controller
{
    public function show($id)
    {
        $Quotation_Id = $id;
        $depth_structural = DepthStructural::all();
        $buckling_structural = BucklingStructural::all();
        $height_structural = HeightStructural::all();

        return view('quotes.selectivo.frames.structuralframeworks.index', compact(
            'depth_structural',
            'buckling_structural',
            'height_structural',
            'Quotation_Id'
        ));
    }
    
    public function store(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'weight' => 'required',
            'caliber' => 'required',
            'buckling_structural' => 'required',
            'depth_structural' => 'required',
            'height_structural' => 'required',
        ];

        $messages = [
            'amount.required' => 'Favor de capturar el número de marcos a cotizar',
            'weight.required' => 'Favor de capturar el Peso',
        ];

        $request->validate($rules, $messages);

        $Quotation_Id = $request->Quotation_Id;
        $Cantidad = $request->amount;
        $Calibre = $request->caliber;
        $Pandeo = $request->buckling_structural;
        $Peso = $request->weight;
        $PesoA = ($Peso * 0.1) + $Peso;

        $Models = StructuralFrameworks::where('caliber', $Calibre)->where('buckling', $Pandeo)->where('weight', '>=', $PesoA)->first();
        if($Models){
            $Modelo = $Models->model;
            $Profundidad = $request->depth_structural;
            $Altura = $request->height_structural;

            $Data = PriceStructuralFrameworks::where('caliber', $Calibre)->where('model', $Modelo)->where('depth', $Profundidad)->where('height', $Altura)->first();
            $PriceList = PriceList::where('system', 'SELECTIVO')->where('piece', 'MARCO')->where('type', 'Negra')->where('caliber', str_replace("-", " ", $Calibre) . " IN")->first();
            if($Data){
                $Postes = $Data->poles;
                $Travesanos = $Data->crossbars;
                $Diagonales = $Data->diagonals;
                $Placas = $Data->plates;
                $TornTravDiag = ($Travesanos + $Diagonales) * 2;
                $PriceListScrewsTravDiag = PriceListScrew::where('description', 'TORNILLO Y TUERCA 3/8 IN X 1 IN G5 GALV')->first();
                $PriceListScrewCostTravDiag = $PriceListScrewsTravDiag->cost * $PriceListScrewsTravDiag->f_total;
                $CostTornTravDiag = $TornTravDiag * $PriceListScrewCostTravDiag;
                $TotTornTravDiag = $Cantidad * $TornTravDiag;
                $TotCostTornTravDiag = $Cantidad * $CostTornTravDiag;
                $Precio = ($PriceList->f_total*$PriceList->cost*$Data->total_kg ) + $CostTornTravDiag;
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
                // $Precio_Total = $Cantidad * $Data->price;
                $Total_Acero_Kg = $Cantidad * $Data->steel_weight_kg;
                $Total_Solera_Kg = $Cantidad * $Data->solera_weight_kg;
                $Total_Kg = $Cantidad * $Data->total_kg;
                $Total_m2 = $Cantidad * $Data->total_m2;
                $Sku = $Data->sku;
                $CostoTotalCalza = ($CostoCalza * 4) * $Cantidad;
                $CostoTotalTaquete = ($CostoTaquete * 4) * $Cantidad;
                $TotalCalzas = $Cantidad * 4;
                $TotalTaquetes = $Cantidad * 4;

                $SF = SelectiveStructuralFrame::where('quotation_id', $Quotation_Id)->first();
                if($SF){
                    $SF->amount = $Cantidad;
                    $SF->model = $Modelo;
                    $SF->caliber = $Calibre;
                    $SF->depth = $Profundidad;
                    $SF->length=$Altura;
                    $SF->total_load_kg = $Total_Peso;
                    $SF->total_poles = $Total_Postes;
                    $SF->total_crossbars = $Total_Travesanos;
                    $SF->total_diagonals = $Total_Diagonales;
                    $SF->total_plates = $Total_Placas;
                    $SF->total_kg = $Total_Kg;
                    $SF->total_m2 = $Total_m2;
                    $SF->sku = $Sku;
                    $SF->total_price = $Precio_Total + $CostoTotalCalza + $CostoTotalTaquete;
                    $SF->save();
                }else{
                    $SF = new SelectiveStructuralFrame();
                    $SF->quotation_id = $Quotation_Id;
                    $SF->amount = $Cantidad;
                    $SF->model = $Modelo;
                    $SF->caliber = $Calibre;
                    $SF->depth = $Profundidad;
                    $SF->length=$Altura;
                    $SF->total_load_kg = $Total_Peso;
                    $SF->total_poles = $Total_Postes;
                    $SF->total_crossbars = $Total_Travesanos;
                    $SF->total_diagonals = $Total_Diagonales;
                    $SF->total_plates = $Total_Placas;
                    $SF->total_kg = $Total_Kg;
                    $SF->total_m2 = $Total_m2;
                    $SF->sku = $Sku;
                    $SF->total_price = $Precio_Total + $CostoTotalCalza + $CostoTotalTaquete;
                    $SF->save();
                }
                $Precio_unit_sn_factor = ($Data->total_kg * $Data->cost)+ $CostTornTravDiag;
                $Precio_sin_factor = $Cantidad * $Precio_unit_sn_factor ;

                //guardar COMPONENTES para reportes
                $Type='SSF';
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->delete();
                // MARCO
              
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=> $Calibre,
                     'sku'=>$Sku ,'cant'=>$Cantidad,'description'=>'MARCO ESTRUCTURAL '.$Modelo,
                    'precio_unit'=>$Precio_Total/$Cantidad,'precio_total'=>$Precio_Total, 'factor'=>$PriceList->f_total,
                    'costo_unit'=>$Precio_unit_sn_factor,'costo_total'=>$Precio_sin_factor,
                    'kg_unit'=>$Total_Kg/$Cantidad, 'm2_unit'=>$Total_m2/$Cantidad
                    ]
                    
                );
                //Piezas
                DB::table('costos')->insert(
                    [['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=> 'GALVANIZADAS','factor'=>$CostoCalzas->f_total,
                     'sku'=>$CostoCalzas->sku ,'cant'=>$Calzas*$Cantidad,'description'=>'CALZAS PARA MARCO',
                    'precio_unit'=>$CostoCalzas->cost * $CostoCalzas->f_total,'precio_total'=>$CostoCalzas->cost * $CostoCalzas->f_total*$Calzas*$Cantidad,
                    'costo_unit'=>$CostoCalzas->cost,'costo_total'=>$CostoCalzas->cost * $Calzas*$Cantidad,
                    ],
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=> 'TORNILLERIA','factor'=>$CostoTaquetes->f_total,
                     'sku'=>$CostoTaquetes->sku ,'cant'=>$TotalTaquetes,'description'=>$CostoTaquetes->description,
                    'precio_unit'=>$CostoTaquetes->cost * $CostoTaquetes->f_total,'precio_total'=>$CostoTaquetes->cost * $CostoTaquetes->f_total*$Taquetes*$Cantidad,
                    'costo_unit'=>$CostoTaquetes->cost,'costo_total'=>$CostoTaquetes->cost * $Calzas*$Cantidad,
                    ],
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=> '5/16 I X 3/4','factor'=>$PriceListScrewsTravDiag->f_total,
                     'sku'=>$PriceListScrewsTravDiag->sku ,'cant'=>$TotTornTravDiag,'description'=>'TORNILLO Y TUERCA PARA DIAGONALES',
                    'precio_unit'=>$PriceListScrewsTravDiag->cost * $PriceListScrewsTravDiag->f_total,'precio_total'=>$PriceListScrewsTravDiag->cost * $PriceListScrewsTravDiag->f_total*$TotTornTravDiag,
                    'costo_unit'=>$PriceListScrewsTravDiag->cost,'costo_total'=>$PriceListScrewsTravDiag->cost * $TotTornTravDiag,
                    ]]
                );
                if(Auth::user()->can('VER LOGS CALCULOS')){
                echo "  //factor: ".$PriceList->f_total.' '.$Data->model.$Data->caliber; 
                echo " //precio acero: $".$PriceList->cost;
                echo " //peso total: ".$Data->total_kg ;
                echo " //total sn f_total: $".$Precio_sin_factor ;
                echo "<br> //Costo calzas: $".$CostoTotalCalza;
                echo "/CostoTaquetes: $".$CostoTotalTaquete;}
             
                return view('quotes.selectivo.frames.structuralframeworks.store', compact(
                    'Cantidad',
                    'Precio_sin_factor',
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
                    'PriceListScrewsTravDiag',
                    'PriceListScrewCostTravDiag',
                    'CostTornTravDiag',
                    'TotTornTravDiag',
                    'TotCostTornTravDiag',
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
                return redirect()->route('structuralframeworks.show', $Quotation_Id)->with('no_existe', 'ok');
            }
        }
        else{
            return redirect()->route('structuralframeworks.show', $Quotation_Id)->with('no_existe', 'ok');
        }
    }
    public function drive_show($id)
    {
        $Quotation_Id = $id;
        $depth_structural = DepthStructural::all();
        $buckling_structural = BucklingStructural::all();
        $height_structural = HeightStructural::all();

        return view('quotes.drivein.frames.structuralframeworks.index', compact(
            'depth_structural',
            'buckling_structural',
            'height_structural',
            'Quotation_Id'
        ));
    }
    
    public function drive_store(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'weight' => 'required',
            'caliber' => 'required',
            'buckling_structural' => 'required',
            'depth_structural' => 'required',
            'height_structural' => 'required',
        ];

        $messages = [
            'amount.required' => 'Favor de capturar el número de marcos a cotizar',
            'weight.required' => 'Favor de capturar el Peso',
        ];

        $request->validate($rules, $messages);

        $Quotation_Id = $request->Quotation_Id;
        $Cantidad = $request->amount;
        $Calibre = $request->caliber;
        $Pandeo = $request->buckling_structural;
        $Peso = $request->weight;
        $PesoA = ($Peso * 0.1) + $Peso;

        $Models = StructuralFrameworks::where('caliber', $Calibre)->where('buckling', $Pandeo)->where('weight', '>=', $PesoA)->first();
        if($Models){
            //$Modelo = $Models->model;
            $Modelo='TC2';
            $Profundidad = $request->depth_structural;
            $Altura = $request->height_structural;

            $Data = PriceStructuralFrameworks::where('caliber', $Calibre)->where('model', $Modelo)->where('depth', $Profundidad)->where('height', $Altura)->first();
            if($Data){
                $Total_Peso = $Cantidad * $Peso;
                $Total_Postes = $Cantidad * $Data->poles;
                $Total_Travesanos = $Cantidad * $Data->crossbars;
                $Total_Diagonales = $Cantidad * $Data->diagonals;
                $Total_Placas = $Cantidad * $Data->plates;
                $Precio_Total = $Cantidad * $Data->price;
                $Total_Acero_Kg = $Cantidad * $Data->steel_weight_kg;
                $Total_Solera_Kg = $Cantidad * $Data->solera_weight_kg;
                $Total_Kg = $Cantidad * $Data->total_kg;
                $Total_m2 = $Cantidad * $Data->total_m2;
                $Sku = $Data->sku;

                $SF = SelectiveStructuralFrame::where('quotation_id', $Quotation_Id)->first();
                if($SF){
                    $SF->amount = $Cantidad;
                    $SF->model = $Modelo;
                    $SF->caliber = $Calibre;
                    $SF->total_load_kg = $Total_Peso;
                    $SF->total_poles = $Total_Postes;
                    $SF->total_crossbars = $Total_Travesanos;
                    $SF->total_diagonals = $Total_Diagonales;
                    $SF->total_plates = $Total_Placas;
                    $SF->total_kg = $Total_Kg;
                    $SF->total_m2 = $Total_m2;
                    $SF->sku = $Sku;
                    $SF->total_price = $Precio_Total;
                    $SF->save();
                }else{
                    $SF = new SelectiveStructuralFrame();
                    $SF->quotation_id = $Quotation_Id;
                    $SF->amount = $Cantidad;
                    $SF->model = $Modelo;
                    $SF->caliber = $Calibre;
                    $SF->total_load_kg = $Total_Peso;
                    $SF->total_poles = $Total_Postes;
                    $SF->total_crossbars = $Total_Travesanos;
                    $SF->total_diagonals = $Total_Diagonales;
                    $SF->total_plates = $Total_Placas;
                    $SF->total_kg = $Total_Kg;
                    $SF->total_m2 = $Total_m2;
                    $SF->sku = $Sku;
                    $SF->total_price = $Precio_Total;
                    $SF->save();
                }

                return view('quotes.drivein.frames.structuralframeworks.store', compact(
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
                return redirect()->route('structuralframeworks.show', $Quotation_Id)->with('no_existe', 'ok');
            }
        }
        else{
            return redirect()->route('structuralframeworks.show', $Quotation_Id)->with('no_existe', 'ok');
        }
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
