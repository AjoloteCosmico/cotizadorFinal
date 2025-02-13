<?php

namespace App\Http\Controllers;

use App\Models\Grill;
use App\Models\Joist;
use App\Models\PriceListAuxiliar;
use Illuminate\Http\Request;
use App\Models\Quotation;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart_product;

class GrillController extends Controller
{
    public function selectivo_grills_index($id)
    {
        $Joists = Joist::all();
        $Quotation_Id = $id;

        return view('quotes.selectivo.grills.index', compact(
            'Joists',
            'Quotation_Id',
        ));
    }

    public function selectivo_grills_store(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'front' => 'required',
            'color' => 'required',
            'background' => 'required',
            'cost' => 'required',
            'dimensions' => 'required',
            'loading_capacity' => 'required',
            'joist_type' => 'required',
        ];
        $messages = [
            'amount.required' => 'Capture la información solicitada',
            'front.required' => 'Capture la información solicitada',
            'color.required' => 'Capture la información solicitada',
            'background.required' => 'Capture la información solicitada',
            'cost.required' => 'Capture la información solicitada',
            'dimensions.required' => 'Capture la información solicitada',
            'loading_capacity.required' => 'Capture la información solicitada',
            'joist_type.required' => 'Capture la información solicitada',
        ];
        $request->validate($rules,$messages);

        $PriceListAuxiliars = PriceListAuxiliar::where('description', 'PARRILLAS')->first();

        $UnitPrice = $request->cost * $PriceListAuxiliars->f_total;
        $TotalPrice = $request->amount * $UnitPrice;

        $Grills = Grill::where('quotation_id', $request->Quotation_Id)->first();
        if($Grills)
        {
            $Grills = Grill::where('quotation_id', $request->Quotation_Id)->first();
            $Grills->front = $request->front;
            $Grills->color = $request->color;
            $Grills->background = $request->background;
            $Grills->cost = $request->cost;
            $Grills->dimensions = $request->dimensions;
            $Grills->loading_capacity = $request->loading_capacity;
            $Grills->joist_type = $request->joist_type;
            $Grills->amount = $request->amount;
            $Grills->unit_price = $UnitPrice;
            $Grills->total_price = $TotalPrice;
            $Grills->save();
        }else
        {
            $Grills = new Grill();
            $Grills->quotation_id = $request->Quotation_Id;
            $Grills->front = $request->front;
            $Grills->color = $request->color;
            $Grills->background = $request->background;
            $Grills->cost = $request->cost;
            $Grills->dimensions = $request->dimensions;
            $Grills->loading_capacity = $request->loading_capacity;
            $Grills->joist_type = $request->joist_type;
            $Grills->amount = $request->amount;
            $Grills->unit_price = $UnitPrice;
            $Grills->total_price = $TotalPrice;
            $Grills->save();
        }
        echo "  //Factor: ".$PriceListAuxiliars->f_total.' '.$PriceListAuxiliars->description.$PriceListAuxiliars->type; 
        
       

        $Grills = Grill::where('quotation_id', $request->Quotation_Id)->first();
        return view('quotes.selectivo.grills.store', compact(
            'Grills',
        ));
    }

    public function double_deep_grills_index($id)
    {
        $Joists = Joist::all();
        $Quotation_Id = $id;

        return view('quotes.double_deep.grills.index', compact(
            'Joists',
            'Quotation_Id',
        ));
    }

    public function double_deep_grills_store(Request $request)
    {
        $rules = [
            'amount' => 'required',
            'front' => 'required',
            'color' => 'required',
            'background' => 'required',
            'cost' => 'required',
            'dimensions' => 'required',
            'loading_capacity' => 'required',
            'joist_type' => 'required',
        ];
        $messages = [
            'amount.required' => 'Capture la información solicitada',
            'front.required' => 'Capture la información solicitada',
            'color.required' => 'Capture la información solicitada',
            'background.required' => 'Capture la información solicitada',
            'cost.required' => 'Capture la información solicitada',
            'dimensions.required' => 'Capture la información solicitada',
            'loading_capacity.required' => 'Capture la información solicitada',
            'joist_type.required' => 'Capture la información solicitada',
        ];
        $request->validate($rules,$messages);

        $PriceListAuxiliars = PriceListAuxiliar::where('description', 'PARRILLAS')->first();

        $UnitPrice = $request->cost * $PriceListAuxiliars->f_total;
        $TotalPrice = $request->amount * $UnitPrice;

        $Grills = Grill::where('quotation_id', $request->Quotation_Id)->first();
        if($Grills)
        {
            $Grills = Grill::where('quotation_id', $request->Quotation_Id)->first();
            $Grills->front = $request->front;
            $Grills->color = $request->color;
            $Grills->background = $request->background;
            $Grills->cost = $request->cost;
            $Grills->dimensions = $request->dimensions;
            $Grills->loading_capacity = $request->loading_capacity;
            $Grills->joist_type = $request->joist_type;
            $Grills->amount = $request->amount;
            $Grills->unit_price = $UnitPrice;
            $Grills->total_price = $TotalPrice;
            $Grills->save();
        }else
        {
            $Grills = new Grill();
            $Grills->quotation_id = $request->Quotation_Id;
            $Grills->front = $request->front;
            $Grills->color = $request->color;
            $Grills->background = $request->background;
            $Grills->cost = $request->cost;
            $Grills->dimensions = $request->dimensions;
            $Grills->loading_capacity = $request->loading_capacity;
            $Grills->joist_type = $request->joist_type;
            $Grills->amount = $request->amount;
            $Grills->unit_price = $UnitPrice;
            $Grills->total_price = $TotalPrice;
            $Grills->save();
        }

        $Grills = Grill::where('quotation_id', $request->Quotation_Id)->first();
        return view('quotes.double_deep.grills.store', compact(
            'Grills',
        ));
    }
    public function add_carrito($id)
    {
        $Quotation_Id = $id;
        $Quotation=Quotation::find($id);
        //buscar si en el carrito hay otro SHLF de esta cotizacion y borrarlo
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','GRILL')->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = Grill::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='PARRILLA';
        $Cart_product->type='GRILL';
        $Cart_product->unit_price=$SJL2->unit_price;
        $Cart_product->total_price=$SJL2->total_price;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        $Cart_product->save();
        //ligar las instancias
        $SJL2->cart_id=$Cart_product->id();
        $SJL2->save();
        return redirect()->route('selectivo_panels',$Quotation_Id);
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
}