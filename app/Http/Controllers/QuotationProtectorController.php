<?php

namespace App\Http\Controllers;

use App\Models\PriceListBar;
use App\Models\PriceList;
use App\Models\PriceListProtector;
use App\Models\Protector;
use App\Models\QuotationProtector;
use Session;
use DB;
use App\Models\Costo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class QuotationProtectorController extends Controller
{
    /* Selectivo */
    public function selectivo_protectors_index($id)
    {
        if(Auth::user()->can('VER LOGS CALCULOS')){
        echo Session::get('protector_logs');}
        $Quotation_Id = $id;
        $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->get();
        if(count($QuotationProtectors)>0){
            $TotalProtectors = 0;
            foreach($QuotationProtectors as $row){
                $TotalProtectors = ($TotalProtectors + $row->total_price);
            }
            $TotalProtectors = $TotalProtectors;
        }else{
            $TotalProtectors = "";
        }

        return view('quotes.selectivo.protectors.index', compact(
            'Quotation_Id',
            'QuotationProtectors',
            'TotalProtectors'
        ));
    }

    public function selectivo_protectors_create($id)
    {
        $Quotation_Id = $id;
        $Protectors = Protector::all();

        return view('quotes.selectivo.protectors.create', compact(
            'Quotation_Id',
            'Protectors'
        ));
    }

    public function selectivo_protectors_store(Request $request)
    {
        $Quotation_Id = $request->Quotation_Id;
        $Protector = $request->protector;
        $Amount = $request->amount;
        $ProtectorComponents = PriceListProtector::all();
        $PostProtectorsCost = PriceListProtector::sum('cost');
        $PosCosto=0;
        $PostProtectorsSalePrice = PriceListProtector::sum('sale_price');
        $PostProtectorsWeight = PriceListProtector::sum('weight');  
        $user_id=Auth::user()->id;
        $Logs="";
        foreach($ProtectorComponents as $row){
            $PriceList = PriceList::where('system', 'ACCESORIOS')->where('piece', 'PROTECTOR')->where('caliber', $row->caliber)->first();
            $Logs=$Logs.$row->piece." //Costo acero ".$PriceList->description.$PriceList->caliber.": $".$PriceList->cost." //Factor: ".$row->f_total."//Peso: ".$row->weight."<br>";
            $PosCosto=$PosCosto+($row->f_total*$row->cost);

        }
        Session::put('protector_logs',$Logs);
        if($Protector == 'PROTECTOR DE POSTE')
        {
            $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->where('protector', $Protector)->first();
            if($QuotationProtectors)
            {
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $PostProtectorsCost;
                $QuotationProtectors->total_weight = $Amount * $PostProtectorsWeight;
                $QuotationProtectors->unit_price = $PosCosto;
                $QuotationProtectors->total_price = $Amount * $PosCosto;
                $QuotationProtectors->costo_sn_factor=$Amount * $PostProtectorsCost ;
                $QuotationProtectors->sku='TC0000117249';
                $QuotationProtectors->save();

                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('update_reg', 'ok');
            }else{
                $QuotationProtectors = new QuotationProtector();
                $QuotationProtectors->quotation_id = $Quotation_Id;
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $PostProtectorsCost;
                $QuotationProtectors->total_weight = $Amount * $PostProtectorsWeight;
                $QuotationProtectors->unit_price = $PosCosto;
                $QuotationProtectors->total_price = $Amount * $PosCosto;
                
                $QuotationProtectors->costo_sn_factor=$Amount * $PostProtectorsCost ;
                $QuotationProtectors->sku='TC0000117249';
                $QuotationProtectors->save();
                 $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                //(new CartController)->add($user_id,'PROTECTOR DE POSTE',$PostProtectorsSalePrice,$Amount,$Quotation_Id);
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('create_reg', 'ok');
            }
        }elseif($Protector == 'PROTECTOR DE BATERIA SENCILLA')
        {
            $PriceListBars = PriceListBar::where('front_development', '1.2000')->first();
            $Cost = $PostProtectorsCost * 2;
            $TotalWeight = $Amount * $PostProtectorsWeight * 2;
            $UnitPrice = ($PosCosto) + $PriceListBars->sale_price;
            $TotalPrice = $Amount * $UnitPrice;
               
            $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->where('protector', $Protector)->first();
            if($QuotationProtectors)
            {
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount *(($PostProtectorsCost ) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117250';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('update_reg', 'ok');
            }else{
                $QuotationProtectors = new QuotationProtector();
                $QuotationProtectors->quotation_id = $Quotation_Id;
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117250';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('create_reg', 'ok');
            }
        }elseif($Protector == 'PROTECTOR DE BATERIA DOBLE')
        {
            $PriceListBars = PriceListBar::where('front_development', '2.4000')->first();
            $Cost = $PostProtectorsCost * 2;
            $TotalWeight = $Amount * $PostProtectorsWeight * 2;
            $UnitPrice = ($PosCosto * 2) + $PriceListBars->sale_price;
            $TotalPrice = $Amount * $UnitPrice;

            $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->where('protector', $Protector)->first();
            if($QuotationProtectors)
            {
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 2) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117251';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('update_reg', 'ok');
            }else{
                $QuotationProtectors = new QuotationProtector();
                $QuotationProtectors->quotation_id = $Quotation_Id;
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 2) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117251';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('create_reg', 'ok');
            }
        }elseif($Protector == 'PROTECTOR DE BATERIA TRIPLE')
        {
            $PriceListBars = PriceListBar::where('front_development', '4.0000')->first();
            $Cost = $PostProtectorsCost * 3;
            $TotalWeight = $Amount * $PostProtectorsWeight * 3;
            $UnitPrice = ($PosCosto * 2) + $PriceListBars->sale_price;
            $TotalPrice = $Amount * $UnitPrice;

            $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->where('protector', $Protector)->first();
            if($QuotationProtectors)
            {
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 3) + $PriceListBars->cost);
                
                $QuotationProtectors->sku='TC0000117252';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('update_reg', 'ok');
            }else{
                $QuotationProtectors = new QuotationProtector();
                $QuotationProtectors->quotation_id = $Quotation_Id;
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 3) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117252';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('create_reg', 'ok');
            }
        }elseif($Protector == 'PROTECTOR DE BATERIA CUADRUPLE')
        {
            $PriceListBars = PriceListBar::where('front_development', '2.4000')->first();
            $Cost = $PostProtectorsCost * 4;
            $TotalWeight = $Amount * $PostProtectorsWeight * 4;
            $UnitPrice = ($PostProtectorsSalePrice * 4) + ($PriceListBars->sale_price * 2);
            $TotalPrice = $Amount * $UnitPrice;

            $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->where('protector', $Protector)->first();
            if($QuotationProtectors)
            {
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 4) + $PriceListBars->cost);
                
                $QuotationProtectors->sku='TC0000117253';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('update_reg', 'ok');
            }else{
                $QuotationProtectors = new QuotationProtector();
                $QuotationProtectors->quotation_id = $Quotation_Id;
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 4) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117253';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('create_reg', 'ok');
            }
        }
    }

    public function selectivo_protectors_edit($id)
    {
        $QuotationProtectorId = $id;
        $Protectors = Protector::all();
        $QuotationProtectors = QuotationProtector::find($id);
        $Quotation_Id = $QuotationProtectors->quotation_id;

        return view('quotes.selectivo.protectors.show', compact(
            'QuotationProtectorId',
            'Protectors',
            'QuotationProtectors',
            'Quotation_Id',
        ));
    }

    public function selectivo_protectors_update(Request $request, $id)
    {
        $Quotation_Id = $request->Quotation_Id;
        $Protector = QuotationProtector::find($id);
        Costo::where('quotation_id',$Quotation_Id)->where('type','SPR')->where('description',$Protector->protector)->delete();
        if($Protector->protector!= $request->protector){
            $Protector->delete();
        }       
        
        $Protector = $request->protector;
        $Amount = $request->amount;
        $PostProtectorsCost = PriceListProtector::sum('cost');
        $PostProtectorsSalePrice = PriceListProtector::sum('sale_price');
        $PostProtectorsWeight = PriceListProtector::sum('weight');
        
        $ProtectorComponents = PriceListProtector::all();
        $Logs="";
        foreach($ProtectorComponents as $row){
            $PriceList = PriceList::where('system', 'ACCESORIOS')->where('piece', 'PROTECTOR')->where('caliber', $row->caliber)->first();
            $Logs=$Logs.$row->piece." //Costo acero ".$PriceList->description.$PriceList->caliber.": $".$PriceList->cost." //Factor: ".$row->f_total."//Peso: ".$row->weight."<br>";
        }
        Session::put('protector_logs',$Logs);
        if($Protector == 'PROTECTOR DE POSTE')
        {
            $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->where('protector', $Protector)->first();
            if($QuotationProtectors)
            {
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $PostProtectorsCost;
                $QuotationProtectors->total_weight = $Amount * $PostProtectorsWeight;
                $QuotationProtectors->unit_price = $PostProtectorsSalePrice;
                $QuotationProtectors->total_price = $Amount * $PostProtectorsSalePrice;
                $QuotationProtectors->costo_sn_factor=$Amount * $PostProtectorsCost ;
                $QuotationProtectors->sku='TC0000117249';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('update_reg', 'ok');
            }else{
                $QuotationProtectors = new QuotationProtector();
                $QuotationProtectors->quotation_id = $Quotation_Id;
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $PostProtectorsCost;
                $QuotationProtectors->total_weight = $Amount * $PostProtectorsWeight;
                $QuotationProtectors->unit_price = $PostProtectorsSalePrice;
                $QuotationProtectors->total_price = $Amount * $PostProtectorsSalePrice;
                $QuotationProtectors->costo_sn_factor=$Amount * $PostProtectorsCost ;
                $QuotationProtectors->sku='TC0000117249';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('create_reg', 'ok');
            }
        }elseif($Protector == 'PROTECTOR DE BATERIA SENCILLA')
        {
            $PriceListBars = PriceListBar::where('front_development', '1.2000')->first();
            $Cost = $PostProtectorsCost * 2;
            $TotalWeight = $Amount * $PostProtectorsWeight * 2;
            $UnitPrice = ($PostProtectorsSalePrice * 2) + $PriceListBars->sale_price;
            $TotalPrice = $Amount * $UnitPrice;

            $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->where('protector', $Protector)->first();
            if($QuotationProtectors)
            {
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount *(($PostProtectorsCost ) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117250';
                $QuotationProtectors->save();

                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('update_reg', 'ok');
            }else{
                $QuotationProtectors = new QuotationProtector();
                $QuotationProtectors->quotation_id = $Quotation_Id;
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount *(($PostProtectorsCost ) + $PriceListBars->cost);
                 $QuotationProtectors->sku='TC0000117250';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('create_reg', 'ok');
            }
        }elseif($Protector == 'PROTECTOR DE BATERIA DOBLE')
        {
            $PriceListBars = PriceListBar::where('front_development', '2.4000')->first();
            $Cost = $PostProtectorsCost * 2;
            $TotalWeight = $Amount * $PostProtectorsWeight * 2;
            $UnitPrice = ($PostProtectorsSalePrice * 2) + $PriceListBars->sale_price;
            $TotalPrice = $Amount * $UnitPrice;

            $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->where('protector', $Protector)->first();
            if($QuotationProtectors)
            {
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 2) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117251';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('update_reg', 'ok');
            }else{
                $QuotationProtectors = new QuotationProtector();
                $QuotationProtectors->quotation_id = $Quotation_Id;
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 2) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117251';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('create_reg', 'ok');
            }
        }elseif($Protector == 'PROTECTOR DE BATERIA TRIPLE')
        {
            $PriceListBars = PriceListBar::where('front_development', '4.0000')->first();
            $Cost = $PostProtectorsCost * 3;
            $TotalWeight = $Amount * $PostProtectorsWeight * 3;
            $UnitPrice = ($PostProtectorsSalePrice * 3) + $PriceListBars->sale_price;
            $TotalPrice = $Amount * $UnitPrice;

            $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->where('protector', $Protector)->first();
            if($QuotationProtectors)
            {
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 3) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117252';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('update_reg', 'ok');
            }else{
                $QuotationProtectors = new QuotationProtector();
                $QuotationProtectors->quotation_id = $Quotation_Id;
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 3) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117252';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('create_reg', 'ok');
            }
        }elseif($Protector == 'PROTECTOR DE BATERIA CUADRUPLE')
        {
            $PriceListBars = PriceListBar::where('front_development', '2.4000')->first();
            $Cost = $PostProtectorsCost * 4;
            $TotalWeight = $Amount * $PostProtectorsWeight * 4;
            $UnitPrice = ($PostProtectorsSalePrice * 4) + ($PriceListBars->sale_price * 2);
            $TotalPrice = $Amount * $UnitPrice;

            $QuotationProtectors = QuotationProtector::where('quotation_id', $Quotation_Id)->where('protector', $Protector)->first();
            if($QuotationProtectors)
            {
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 4) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117253';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('update_reg', 'ok');
            }else{
                $QuotationProtectors = new QuotationProtector();
                $QuotationProtectors->quotation_id = $Quotation_Id;
                $QuotationProtectors->amount = $Amount;
                $QuotationProtectors->protector = $Protector;
                $QuotationProtectors->cost = $Cost;
                $QuotationProtectors->total_weight = $TotalWeight;
                $QuotationProtectors->unit_price = $UnitPrice;
                $QuotationProtectors->total_price = $TotalPrice;
                $QuotationProtectors->costo_sn_factor=$Amount * (($PostProtectorsCost * 4) + $PriceListBars->cost);
                $QuotationProtectors->sku='TC0000117253';
                $QuotationProtectors->save();
                $Type='SPR';
                $Quotation_Id=$request->Quotation_Id;
                $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->where('description',$Protector)->delete();
                // PROTECTOR
                //GUARDAR COSTOS DE PROTECTOR
                DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=>'NA' ,
                     'sku'=>$QuotationProtectors->sku,'cant'=>$QuotationProtectors->amount,'description'=>$Protector,
                    'precio_unit'=>$QuotationProtectors->unit_price,'precio_total'=>$QuotationProtectors->total_price, 'factor'=>$ProtectorComponents->sum('f_total')/$ProtectorComponents->count(),
                    'costo_unit'=>$QuotationProtectors->costo_sn_factor/$Amount ,'costo_total'=>$QuotationProtectors->costo_sn_factor,
                    'kg_unit'=>$QuotationProtectors->total_weight/$Amount, 'm2_unit'=>0
                    ]
                );
                return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('create_reg', 'ok');
            }
        }
    }

    public function selectivo_protectors_destroy($id)
    {
        
    $Protector=QuotationProtector::find($id);
    $Quotation_Id=$Protector->quotation_id;
    Costo::where('quotation_id',$Quotation_Id)->where('type','SPR')->where('description',$Protector->protector)->delete();
    QuotationProtector::destroy($id);            
    return redirect()->route('selectivo_protectors.index', $Quotation_Id)->with('eliminar', 'ok');
    }

    /* Double Deep */
    public function double_deep_protectors_index($id)
    {

    }

    public function double_deep_protectors_create($id)
    {

    }

    public function double_deep_protectors_store(Request $request)
    {

    }

    /* Resources */
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
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
