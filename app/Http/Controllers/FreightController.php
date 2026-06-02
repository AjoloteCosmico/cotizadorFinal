<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Freight;
use App\Models\Packaging;
use App\Models\Transport;
use App\Models\Factor;
use App\Models\TravelAssignment;
use App\Models\Installation;
use App\Models\QuotationInstall;
use App\Models\QuotationTravelAssignment;

use App\Models\QuotationUninstall;
use App\Models\Uninstall;
use App\Models\Costo;
use DB;
use Illuminate\Http\Request;
use App\Models\Cart_product;
use Illuminate\Support\Facades\Auth;
use App\Models\Quotation;

class FreightController extends Controller
{

    public function selectivo_show($id)
    {
        $Quotation_Id = $id;
        $Packagings = Packaging::where('quotation_id', $Quotation_Id)->get();
        
        if(count($Packagings)>0){
            $TotalTransports = 0;
            foreach($Packagings as $row){
                $TotalTransports = ($TotalTransports + $row->import);
            }
        }else{
            $TotalTransports = "";
        }
        
        return view('quotes.selectivo.freights.index', compact(
            'Quotation_Id',
            'Packagings',
            'TotalTransports',
        ));
    }

    public function selectivo_transports($id)
    {
        $Quotation_Id = $id;
        $Destinations = Destination::distinct()->get('destination');
        $Units = Destination::distinct()->get('unit');

        return view('quotes.selectivo.freights.transports', compact(
            'Quotation_Id',
            'Destinations',
            'Units',
        ));
    }

    public function selectivo_transports_add(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'destination' => 'required',
            'unit' => 'required',
        ];
        $messages = [
            'amount.required' => 'Por favor capture la cantidad',
            'destination.required' => 'Por favor seleccione un destino',
            'unit.required' => 'Por favor seleccione el tipo de unidad',
        ];
        $request->validate($rules,$messages);

        $Destinations = Destination::where('destination', $request->destination)->where('unit', $request->unit)->first();
        if($Destinations){
            $Cost = $Destinations->cost * $Destinations->f_total;
            $Import = $request->amount * $Cost;

            $Packagings = new Packaging();
            $Packagings->quotation_id = $request->Quotation_Id;
            $Packagings->amount = $request->amount;
            $Packagings->destination = $Destinations->destination;
            $Packagings->state = $Destinations->state;
            $Packagings->unit = $Destinations->unit;
            $Packagings->cost = $Cost;
            $Packagings->import = $Import;
            $Packagings->save();
        }
        $Type='SFLETE';
        // $Componentes=Costo::where('quotation_id',$request->Quotation_Id)->where('type',$Type)->where()->delete();
            
          //FLETE COSTOS 
        DB::table('costos')->insert(
                ['quotation_id' => $request->Quotation_Id, 'type' => $Type,'calibre'=> 'TRANSPORTE',
                    'sku'=>' ','cant'=>$request->amount ,'description'=>'FLETE '.$Destinations->destination,
                'precio_unit'=>$Cost,'precio_total'=>$Import, 'factor'=>$Destinations->f_total,
                'costo_unit'=>$Destinations->cost ,'costo_total'=>$Destinations->cost* $request->amount ,
                'kg_unit'=>0, 'm2_unit'=>0,]
            );
        return redirect()->route('selectivo_freights.show', $request->Quotation_Id);
    }

    public function selectivo_quotation_travel_assignments($id)
    {
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        // $Descriptions = TravelAssignment::distinct()->get('description');
        $Descriptions = TravelAssignment::select('description', 'unit')->get();
        $QuotationTravelAssignments = QuotationTravelAssignment::where('quotation_id', $Quotation_Id)->get();
        if(count($QuotationTravelAssignments)>0){
            $TotalTravelAssignments = 0;
            foreach($QuotationTravelAssignments as $row){
                $TotalTravelAssignments = ($TotalTravelAssignments + $row->import);
            }
            $TotalTravelAssignments = $TotalTravelAssignments;
        }else{
            $TotalTravelAssignments = "";
        }

        return view('quotes.selectivo.travel_assignments.index', compact(
            'Quotation_Id',
            'QuotationTravelAssignments',
            'TotalTravelAssignments',
            'Quotation','Descriptions',
        ));
    }

    public function selectivo_travel_assignments($id)
    {
        $Quotation_Id = $id;
        $Descriptions = TravelAssignment::distinct()->get('description');

        return view('quotes.selectivo.freights.travel_assignments', compact(
            'Quotation_Id',
            'Descriptions'
        ));
    }
    public function selectivo_travel_assignments_general($id)
    {
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);

        return view('quotes.selectivo.freights.travel_assignments_general', compact(
            'Quotation_Id',
            'Quotation'
        ));
    }
  public function selectivo_travel_assignments_calcular(Request $request)
{
    $filas        = $request->input('filas', []);
    $resultados   = [];
    $totalGeneral = 0;
 
    foreach ($filas as $fila) {
        $description = $fila['description'] ?? '';
        $cost        = floatval($fila['cost']      ?? 0);
        $dias        = floatval($fila['dias']       ?? 0);
        $operarios   = floatval($fila['operarios']  ?? 0);
 
        if (!$description || $cost <= 0) {
            // Fila incompleta — devolver null para que el frontend muestre "—"
            $resultados[] = ['import' => null];
            continue;
        }
 
        $TravelAssignment = TravelAssignment::where('description', $description)->first();
 
        if (!$TravelAssignment) {
            $resultados[] = ['import' => null];
            continue;
        }
 
        $import = $cost * $dias * $operarios * $TravelAssignment->f_total;
 
        $resultados[]  = ['import' => round($import, 2)];
        $totalGeneral += $import;
    }
 
    return response()->json([
        'total' => round($totalGeneral, 2),
        'filas' => $resultados,
    ]);
}
 
 
// ══════════════════════════════════════════════════════════════════════════════
// POST PRINCIPAL — Guardar datos generales + todas las filas de viáticos
// Reemplaza el antiguo selectivo_travel_assignments_general_update
// y elimina la necesidad de selectivo_travel_assignments_add
// ══════════════════════════════════════════════════════════════════════════════
public function selectivo_travel_assignments_general_update(Request $request)
{
    $rules = [
        'dias'      => 'required|numeric|min:1',
        'npos'      => 'required|numeric|min:1',
        'operarios' => 'required|numeric|min:1',
        'posxdia'   => 'required|numeric|min:1',
    ];
 
    $messages = [
        'dias.required'      => 'Por favor capture el número de días.',
        'dias.min'           => 'El número de días debe ser mayor a cero.',
        'npos.required'      => 'Por favor capture el número de posiciones.',
        'npos.min'           => 'Las posiciones deben ser mayor a cero.',
        'operarios.required' => 'Por favor capture el número de operarios.',
        'operarios.min'      => 'Los operarios deben ser mayor a cero.',
        'posxdia.required'   => 'Por favor capture las posiciones por día.',
        'posxdia.min'        => 'Las posiciones por día deben ser mayor a cero.',
    ];
 
    $request->validate($rules, $messages);
 
    // ── 1. Guardar datos generales de la cotización ────────────────────────
    $Quotation           = Quotation::findOrFail($request->Quotation_Id);
    $Quotation->npos     = $request->npos;
    $Quotation->dias     = $request->dias;
    $Quotation->posxdia  = $request->posxdia;
    $Quotation->operarios = $request->operarios;
    $Quotation->save();
 
    // ── 2. Borrar viáticos y costos anteriores de este presupuesto ────────
    $Type = 'SVIAT';
    Costo::where('quotation_id', $request->Quotation_Id)->where('type', $Type)->delete();
    QuotationTravelAssignment::where('quotation_id', $request->Quotation_Id)->delete();
 
    // ── 3. Insertar filas nuevas ───────────────────────────────────────────
    if ($request->has('dia')) {
        foreach (array_keys($request->dia) as $i) {
            $description = $request->description[$i] ?? null;
            $cost        = floatval($request->cost[$i]      ?? 0);
            $dias        = floatval($request->dia[$i]       ?? 0);
            $operarios   = floatval($request->operario[$i]  ?? 0);
 
            // Saltar filas con costo cero o sin descripción (validación de seguridad)
            if (!$description || $cost <= 0) {
                continue;
            }
 
            $TravelAssignment = TravelAssignment::where('description', $description)->first();
 
            if (!$TravelAssignment) {
                continue;
            }
 
            $costConFactor = $cost * $TravelAssignment->f_total;
            $import        = $cost * $dias * $operarios * $TravelAssignment->f_total;
 
            // Guardar en quotation_travel_assignments
            $qta               = new QuotationTravelAssignment();
            $qta->quotation_id = $request->Quotation_Id;
            $qta->dias         = $dias;
            $qta->amount       = $dias;
            $qta->operarios    = $operarios;
            $qta->description  = $TravelAssignment->description;
            $qta->unit         = $TravelAssignment->unit;
            $qta->cost         = $costConFactor;
            $qta->import       = $import;
            $qta->save();
 
            // Guardar en tabla costos
            DB::table('costos')->insert([
                'quotation_id'  => $request->Quotation_Id,
                'type'          => $Type,
                'calibre'       => 'TRANSPORTE',
                'sku'           => ' ',
                'cant'          => $dias * $operarios,
                'description'   => 'VIATICOS ' . $TravelAssignment->description,
                'precio_unit'   => $TravelAssignment->cost * $TravelAssignment->f_total,
                'precio_total'  => $import,
                'factor'        => $TravelAssignment->f_total,
                'costo_unit'    => $costConFactor,
                'costo_total'   => $cost * $dias * $operarios,
                'kg_unit'       => 0,
                'm2_unit'       => 0,
            ]);
        }
    }
 
    // ── 4. agregar todo al carritp ──────────────────
    
    $this->viaticos_add_carrito($request->Quotation_Id);
    return redirect()->route('selectivo.show', $request->Quotation_Id);
}
    public function selectivo_travel_assignments_add(Request $request)
    {
        $rules = [
            'dias' => 'required',
            
            'operarios' => 'required',
            'cost' => 'required',
            'description' => 'required',
        ];

        $messages = [
            'dias.required' => 'Por favor capture la cantidad de dias',
            'description.required' => 'Por favor seleccione una descripción',
            'cost.required' => 'Por favor capture el costo por operacion',
            'operarios.required' => 'Por favor capture la cantidad de personas',
            
        ];

        $request->validate($rules,$messages);

        $TravelAssignments = TravelAssignment::where('description', $request->description)->first();
        if($TravelAssignments){
            $Cost = $TravelAssignments->cost * $TravelAssignments->f_total;
            $Import = $request->cost * $request->dias*$request->operarios*$TravelAssignments->f_total;
            $QuotationTravelAssignments = new QuotationTravelAssignment();
            $QuotationTravelAssignments->quotation_id = $request->Quotation_Id;
            $QuotationTravelAssignments->dias = $request->dias;
            $QuotationTravelAssignments->amount = $request->dias;
            $QuotationTravelAssignments->operarios = $request->operarios;
            $QuotationTravelAssignments->description = $TravelAssignments->description;
            $QuotationTravelAssignments->unit = $TravelAssignments->unit;
            $QuotationTravelAssignments->cost = $request->cost*$TravelAssignments->f_total;
            $QuotationTravelAssignments->import = $Import;
            $QuotationTravelAssignments->save();
        }        

        return redirect()->route('selectivo_quotation_travel_assignments', $request->Quotation_Id);
    }






    
    public function selectivo_installs($id)
    {
        $Quotation_Id = $id;
        $QuotationInstalls = QuotationInstall::where('quotation_id', $Quotation_Id)->get();
        if(count($QuotationInstalls) > 0){
            $TotalImportInstall = 0;
            foreach($QuotationInstalls as $row){
                $TotalImportInstall = $TotalImportInstall + $row->import;
            }
        }else{
            $TotalImportInstall = 0;
        }
        $QuotationUninstalls = QuotationUninstall::where('quotation_id', $Quotation_Id)->get();
        if(count($QuotationUninstalls) > 0){
            $TotalImportUninstall = 0;
            foreach($QuotationUninstalls as $row){
                $TotalImportUninstall = $TotalImportUninstall + $row->import;
            }
        }else{
            $TotalImportUninstall = 0;
        }
        $TotalImport = $TotalImportInstall + $TotalImportUninstall;

        return view('quotes.selectivo.installations.index', compact(
            'Quotation_Id',
            'QuotationInstalls',
            'QuotationUninstalls',
            'TotalImportInstall',
            'TotalImportUninstall',
            'TotalImport',
        ));
    }

    public function selectivo_fiut_add(Request $request)
    {
        if($request->install == 'SI'){
            if($request->uninstall == 'SI'){
                $rules = [
                    'TotalImportInstall' => 'required_without_all: TotalImportUninstall ',
                    'TotalImportUninstall' => 'required_without_all: TotalImportInstall ',
                ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'Sí';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }                
                $PrintUninstall = 'Sí';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }
            }else{
                $rules = [
                    'TotalImportInstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'Sí';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }
                // dd('caso que creo');
            }
        }elseif($request->install == 'INCLUIDA'){
            if($request->uninstall == 'INCLUIDA'){
                $rules = [
                    'TotalImportInstall' => 'required_without_all: TotalImportUninstall ',
                    'TotalImportUninstall' => 'required_without_all: TotalImportInstall ',
               ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'In';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }                
                $PrintUninstall = 'In';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }
            }else{
                $rules = [
                    'TotalImportInstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'In';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }
            }
        }else{
            $PrintInstall = 'No';
            $PrintUninstall = 'No';
            
            $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->delete();
            $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->delete();
        }
        if($request->uninstall == 'SI'){
            if($request->install == 'SI'){
                $rules = [
                    'TotalImportInstall' => 'required_without_all: TotalImportUninstall ',
                    'TotalImportUninstall' => 'required_without_all: TotalImportInstall ',
               ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'Sí';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }                
                $PrintUninstall = 'Sí';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }                
            }else{
                $rules = [
                    'TotalImportUninstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintUninstall = 'Sí';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }
            }
        }elseif($request->uninstall == 'INCLUIDA'){
            if($request->install == 'INCLUIDA'){
                $rules = [
                    'TotalImportInstall' => 'required|not_in:0',
                    'TotalImportUninstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'In';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }                
                $PrintUninstall = 'In';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }                
            }else{
                $rules = [
                    'TotalImportUninstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintUninstall = 'In';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }
            }
        }else{
        if($request->uninstall == 'No'){
            $PrintInstall = 'No';
            $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->delete();
           
        }
        if($request->install == 'No'){
            $PrintUninstall = 'No';
            $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->delete();
        }
            
            
           
            
        }
        return $this->selectivo_installs_add_carrito($request->Quotation_Id);

        
    }

    public function double_deep_show($id)
    {
        $Quotation_Id = $id;
        $Packagings = Packaging::where('quotation_id', $Quotation_Id)->get();
        if(count($Packagings)>0){
            $TotalTransports = 0;
            foreach($Packagings as $row){
                $TotalTransports = ($TotalTransports + $row->import);
            }
        }else{
            $TotalTransports = "";
        }

        return view('quotes.double_deep.freights.index', compact(
            'Quotation_Id',
            'Packagings',
            'TotalTransports',
        ));
    }

    public function double_deep_transports($id)
    {
        $Quotation_Id = $id;
        $Destinations = Destination::distinct()->get('destination');
        $Units = Destination::distinct()->get('unit');

        return view('quotes.double_deep.freights.transports', compact(
            'Quotation_Id',
            'Destinations',
            'Units',
        ));
    }

    public function double_deep_transports_add(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'destination' => 'required',
            'unit' => 'required',
        ];
        $messages = [
            'amount.required' => 'Por favor capture la cantidad',
            'destination.required' => 'Por favor seleccione un destino',
            'unit.required' => 'Por favor seleccione el tipo de unidad',
        ];
        $request->validate($rules,$messages);

        $Destinations = Destination::where('destination', $request->destination)->where('unit', $request->unit)->first();
        if($Destinations){
            $Cost = $Destinations->cost * $Destinations->f_total;
            $Import = $request->amount * $Cost;

            $Packagings = new Packaging();
            $Packagings->quotation_id = $request->Quotation_Id;
            $Packagings->amount = $request->amount;
            $Packagings->destination = $Destinations->destination;
            $Packagings->state = $Destinations->state;
            $Packagings->unit = $Destinations->unit;
            $Packagings->cost = $Cost;
            $Packagings->import = $Import;
            $Packagings->save();
        }

        return redirect()->route('double_deep_freights.show', $request->Quotation_Id);
    }

    public function double_deep_quotation_travel_assignments($id)
    {
        $Quotation_Id = $id;
        $QuotationTravelAssignments = QuotationTravelAssignment::where('quotation_id', $Quotation_Id)->get();
        if(count($QuotationTravelAssignments)>0){
            $TotalTravelAssignments = 0;
            foreach($QuotationTravelAssignments as $row){
                $TotalTravelAssignments = ($TotalTravelAssignments + $row->import);
            }
            $TotalTravelAssignments = $TotalTravelAssignments;
        }else{
            $TotalTravelAssignments = "";
        }

        return view('quotes.double_deep.travel_assignments.index', compact(
            'Quotation_Id',
            'QuotationTravelAssignments',
            'TotalTravelAssignments',
        ));
    }

    public function double_deep_travel_assignments($id)
    {
        $Quotation_Id = $id;
        $Descriptions = TravelAssignment::distinct()->get('description');

        return view('quotes.double_deep.freights.travel_assignments', compact(
            'Quotation_Id',
            'Descriptions',
        ));
    }

    public function double_deep_travel_assignments_add(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'description' => 'required',
        ];

        $messages = [
            'amount.required' => 'Por favor capture la cantidad',
            'description.required' => 'Por favor seleccione una descripción',
        ];

        $request->validate($rules,$messages);

        $TravelAssignments = TravelAssignment::where('description', $request->description)->first();
        if($TravelAssignments){
            $Cost = $TravelAssignments->cost * $TravelAssignments->f_total;
            $Import = $request->amount * $Cost;
            $QuotationTravelAssignments = new QuotationTravelAssignment();
            $QuotationTravelAssignments->quotation_id = $request->Quotation_Id;
            $QuotationTravelAssignments->amount = $request->amount;
            $QuotationTravelAssignments->description = $TravelAssignments->description;
            $QuotationTravelAssignments->unit = $TravelAssignments->unit;
            $QuotationTravelAssignments->cost = $Cost;
            $QuotationTravelAssignments->import = $Import;
            $QuotationTravelAssignments->save();
        }

        return redirect()->route('double_deep_quotation_travel_assignments', $request->Quotation_Id);
    }

    public function double_deep_installs($id)
    {
        $Quotation_Id = $id;
        $QuotationInstalls = QuotationInstall::where('quotation_id', $Quotation_Id)->get();
        if(count($QuotationInstalls) > 0){
            $TotalImportInstall = 0;
            foreach($QuotationInstalls as $row){
                $TotalImportInstall = $TotalImportInstall + $row->import;
            }
        }else{
            $TotalImportInstall = 0;
        }
        $QuotationUninstalls = QuotationUninstall::where('quotation_id', $Quotation_Id)->get();
        if(count($QuotationUninstalls) > 0){
            $TotalImportUninstall = 0;
            foreach($QuotationUninstalls as $row){
                $TotalImportUninstall = $TotalImportUninstall + $row->import;
            }
        }else{
            $TotalImportUninstall = 0;
        }
        $TotalImport = $TotalImportInstall + $TotalImportUninstall;

        return view('quotes.double_deep.installations.index', compact(
            'Quotation_Id',
            'QuotationInstalls',
            'QuotationUninstalls',
            'TotalImportInstall',
            'TotalImportUninstall',
            'TotalImport',
        ));
    }

    public function double_deep_fiut_add(Request $request)
    {
        if($request->install == 'SI'){
            if($request->uninstall == 'SI'){
                $rules = [
                    'TotalImportInstall' => 'required|not_in:0',
                    'TotalImportUninstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'Sí';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }                
                $PrintUninstall = 'Sí';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }
            }else{
                $rules = [
                    'TotalImportInstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'Sí';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }
            }
        }elseif($request->install == 'INCLUIDA'){
            if($request->uninstall == 'INCLUIDA'){
                $rules = [
                    'TotalImportInstall' => 'required|not_in:0',
                    'TotalImportUninstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'In';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }                
                $PrintUninstall = 'In';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }
            }else{
                $rules = [
                    'TotalImportInstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'In';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }
            }
        }else{
            $PrintInstall = 'No';
            $PrintUninstall = 'No';
            $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->delete();
            $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->delete();
        }
        if($request->uninstall == 'SI'){
            if($request->install == 'SI'){
                $rules = [
                    'TotalImportInstall' => 'required|not_in:0',
                    'TotalImportUninstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'Sí';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }                
                $PrintUninstall = 'Sí';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }                
            }else{
                $rules = [
                    'TotalImportUninstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintUninstall = 'Sí';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }
            }
        }elseif($request->uninstall == 'INCLUIDA'){
            if($request->install == 'INCLUIDA'){
                $rules = [
                    'TotalImportInstall' => 'required|not_in:0',
                    'TotalImportUninstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportInstall.required' => 'Agregue informacióm de la Instalación',
                    'TotalImportInstall.not_in' => 'Agregue por lo menos un concepto a la Instalación',
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintInstall = 'In';
                $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationInstalls as $row)
                {
                    $row->print = $PrintInstall;
                    $row->breakdown_install = NULL;
                    $row->save();
                }                
                $PrintUninstall = 'In';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }                
            }else{
                $rules = [
                    'TotalImportUninstall' => 'required|not_in:0',
                ];
                $messages = [
                    'TotalImportUninstall.required' => 'Agregue información de la desinstalación',
                    'TotalImportUninstall.not_in' => 'Agregue por lo menos un concepto a la Desinstalación',
                ];
                $request->validate($rules,$messages);
                $PrintUninstall = 'In';
                $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->get();
                foreach($QuotationUninstalls as $row)
                {
                    $row->print = $PrintUninstall;
                    $row->breakdown_uninstall = NULL;
                    $row->save();
                }
            }
        }else{
            $PrintInstall = 'No';
            $PrintUninstall = 'No';
            $QuotationInstalls = QuotationInstall::where('quotation_id', $request->Quotation_Id)->delete();
            $QuotationUninstalls = QuotationUninstall::where('quotation_id', $request->Quotation_Id)->delete();
        }
        
        return redirect()->route('double_deep.show', $request->Quotation_Id);
    }

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
    public function fletes_add_carrito($id)
    {
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','SFLETE')->get();
        if($cartl2->count()>0){
            foreach($cartl2 as $c){
                Cart_product::destroy($c->id);
            }
        }
        //agregar el nuevo al carrito, lo que este en 
        $productos = Packaging::where('quotation_id', $Quotation_Id)->get();
        //guardar en el carrito
        foreach($productos as $p){
            $Cart_product= new Cart_product();
            $Cart_product->name='FLETE';
            $Cart_product->type='SFLETE';
            $Cart_product->unit_price=$p->cost;
            $Cart_product->total_price=$p->import;
            $Cart_product->quotation_id=$Quotation_Id;
            $Cart_product->user_id=Auth::user()->id;
            $Cart_product->amount=$p->amount;
            $Cart_product->save();
                //ligar las instancias
            $p->cart_id=$Cart_product->id;
            $p->save();
        }
        
        
        return redirect()->route('selectivo.show',$Quotation_Id);
    }
    public function viaticos_add_carrito($id)
    {
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','SVIAT')->get();
        if($cartl2->count()>0){
            foreach($cartl2 as $c){
                Cart_product::destroy($c->id);
            }
            
        }
        //agregar el nuevo al carrito, lo que este en 
        $productos = QuotationTravelAssignment::where('quotation_id', $Quotation_Id)->get();
        //guardar en el carrito
        foreach($productos as $p){
            $Cart_product= new Cart_product();
            $Cart_product->name='VIATICO '.$p->description;
            $Cart_product->type='SVIAT';
            $Cart_product->unit_price=$p->cost;
            $Cart_product->total_price=$p->import;
            $Cart_product->quotation_id=$Quotation_Id;
            $Cart_product->user_id=Auth::user()->id;
            $Cart_product->amount=$p->amount;
            $Cart_product->save();
             //ligar las instancias
            $p->cart_id=$Cart_product->id;
            $p->save();
        }
        
        return redirect()->route('selectivo.show',$Quotation_Id);
    }
    public function selectivo_installs_add_carrito($id)
    {
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','SINS')->get();
        if($cartl2->count()>0){
            foreach($cartl2 as $c){
                Cart_product::destroy($c->id);
            }
        }
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','SUINS')->get();
        if($cartl2->count()>0){
            foreach($cartl2 as $c){
                Cart_product::destroy($c->id);
            }
        }
        //agregar el nuevo al carrito, lo que este en 
        $productos = QuotationInstall::where('quotation_id', $Quotation_Id)->get();
        //guardar en el carrito
        foreach($productos as $p){
            $Cart_product= new Cart_product();
            $Cart_product->name='INSTALACION';
            $Cart_product->type='SINS';
            $Cart_product->unit_price=$p->cost;
            $Cart_product->total_price=$p->import;
            $Cart_product->quotation_id=$Quotation_Id;
            $Cart_product->user_id=Auth::user()->id;
            $Cart_product->amount=$p->amount;
            $Cart_product->save();
             //ligar las instancias
            $p->cart_id=$Cart_product->id;
            $p->save();
        }
        $productos = QuotationUninstall::where('quotation_id', $Quotation_Id)->get();
        //guardar en el carrito
        foreach($productos as $p){
            $Cart_product= new Cart_product();
            $Cart_product->name='DESINSTALACION';
            $Cart_product->type='SUINS';
            $Cart_product->unit_price=$p->cost;
            $Cart_product->total_price=$p->import;
            $Cart_product->quotation_id=$Quotation_Id;
            $Cart_product->user_id=Auth::user()->id;
            $Cart_product->amount=$p->amount;
            $Cart_product->save();
             //ligar las instancias
            $p->cart_id=$Cart_product->id;
            $p->save();
        }
        
        
        return redirect()->route('selectivo.show',$Quotation_Id);
    }
}
