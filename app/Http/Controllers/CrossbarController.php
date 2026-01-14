<?php

namespace App\Http\Controllers;

use App\Models\Crossbar;
use App\Models\SelectiveCrossbar;
use Illuminate\Http\Request;
use App\Models\Cart_product;
use App\Models\PriceList;
use Illuminate\Support\Facades\Auth;
use App\Models\Quotation;

class CrossbarController extends Controller
{
    public function show($id)
    {
        $Quotation_Id = $id;
        $Crossbars = Crossbar::where('id', '<>', '4')->get();

        return view('quotes.selectivo.crossbars.index', compact('Crossbars', 'Quotation_Id'));
    }

    public function calc(Request $request)
    {
        $rules = [
            'amount' => 'required',
        ];
        $messages = [
            'amount.required' => 'La cantidad de piezas es requerida',
        ];
        $request->validate($rules,$messages);
        $Quotation_Id = $request->Quotation_Id;
        $Amount = $request->amount;
        $Piece = Crossbar::find($request->piece);
        $PriceList = PriceList::where('piece', 'CROSS BAR')->where('caliber', 14)->first();
        $PriceConector=Pricelist::where('piece','CONECTOR CROSSBAR')->first();
        $ConectorPrice=0.84*$PriceConector->f_total *$PriceConector->cost;
        $ConectorPricesin=0.84*$PriceConector->cost;
        if($request->conector == 4){
            $Conector = Crossbar::where('type', 'CONECTOR DE CROSS BAR')->first();
            $SubTotal = $Amount * ($Piece->weight * $PriceList->cost * $PriceList->f_total +$ConectorPrice);
            $Precio_sn_factor = $Amount * ($Piece->weight * $PriceList->cost  +$ConectorPricesin);
            
            $ConConnector = 'Yes';
        }else{
            $Conector = '';
            $SubTotal = $Amount *  $Piece->weight * $PriceList->cost * $PriceList->f_total;
            $Precio_sn_factor = $Amount * ($Piece->weight * $PriceList->cost );
            
            $ConConnector = 'No';
        }

        $ConConnector;
        // dd($PriceList,$Piece,$SubTotal,$ConConnector);
        $SCB = SelectiveCrossbar::where('quotation_id', $Quotation_Id)->first();
        if($SCB){
            $SCB->amount = $Amount;
            $SCB->type = $Piece->type;
            $SCB->depth = $Piece->depth;
            $SCB->developing = $Piece->developing;
            $SCB->long = $Piece->length;
            $SCB->caliber = $Piece->caliber;
            $SCB->kg_m2 = $Piece->kg_m2;
            $SCB->weight = $Piece->weight;
            $SCB->m2 = $Piece->m2;
            if($ConConnector == 'No'){
                $SCB->connector = 0;
            }else{
                $SCB->connector = $Conector->price;
            }
            $SCB->sku = $Piece->sku;
            $SCB->unit_price = $SubTotal/$Amount;
            $SCB->total_price = $SubTotal;
            $SCB->save();
        }else{
            $SCB = new SelectiveCrossbar();
            $SCB->quotation_id = $Quotation_Id;
            $SCB->amount = $Amount;
            $SCB->type = $Piece->type;
            $SCB->depth = $Piece->depth;
            $SCB->developing = $Piece->developing;
            $SCB->long = $Piece->length;
            $SCB->caliber = $Piece->caliber;
            $SCB->kg_m2 = $Piece->kg_m2;
            $SCB->weight = $Piece->weight;
            $SCB->m2 = $Piece->m2;
            if($ConConnector = 'No'){
                $SCB->connector = 0;
            }else{
                $SCB->connector = $Conector->price;
            }            
            $SCB->sku = $Piece->sku;
            $SCB->unit_price = $SubTotal/$Amount;
            $SCB->total_price = $SubTotal;
            $SCB->save();
        }
        echo "Costo acero: $".$PriceList->cost." //Factor: ".$PriceList->f_total." //Peso: ".$Piece->weight;
        //guardar COMPONENTES para reportes
        $Type='SCB';
        $Componentes=Costo::where('quotation_id',$Quotation_Id)->where('type',$Type)->delete();
        
        DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=> '14',
                    'sku'=>$Piece->sku ,'cant'=>$Amount,'description'=>'CROSSBAR ',
                    'precio_unit'=>$Piece->weight * $PriceList->cost * $PriceList->f_total,
                    'precio_total'=>$Amount * $Piece->weight * $PriceList->cost * $PriceList->f_total, 'factor'=>$PriceList->f_total,
                    'costo_unit'=>$Piece->weight * $PriceList->cost,'costo_total'=>$Amount * $Piece->weight * $PriceList->cost ,
                    'kg_unit'=>$Piece->weight, 'm2_unit'=>$Piece->m2
                    ]
                );
        if($ConConnector != 'No'){
            echo "<br> precio conector: $".$PriceConector->cost."//Factor: ".$PriceConector->f_total." //preso conector: 0.84" ;
        DB::table('costos')->insert(
                    ['quotation_id' => $Quotation_Id, 'type' => $Type,'calibre'=> '10',
                    'sku'=>$Conector->sku ,'cant'=>$Amount,'description'=>'CONECTOR DE CORSSBAR',
                    'precio_unit'=>$ConectorPrice,
                    'precio_total'=>$Amount * $ConectorPrice, 'factor'=>$PriceConector->f_total,
                    'costo_unit'=>$ConectorPricesin,'costo_total'=>$Amount * $ConectorPricesin ,
                    'kg_unit'=>$Conector->weight, 'm2_unit'=>$Conector->m2
                    ]
                );
        }
        
        return view('quotes.selectivo.crossbars.calc', compact(
            'Precio_sn_factor',
            'Amount',
            'Piece',
            'SubTotal',
            'Conector',
            'Quotation_Id',
            'ConectorPrice'
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

    public function store(Request $request)
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
        $cartl2 = Cart_product::where('quotation_id', $Quotation_Id)->where('type','SCB')->first();
        if($cartl2){
            Cart_product::destroy($cartl2->id);
        }
        //agregar el nuevo al carrito, lo que este en 
        $SJL2 = SelectiveCrossbar::where('quotation_id', $Quotation_Id)->first();
        //guardar en el carrito
        $Cart_product= new Cart_product();
        $Cart_product->name='CROSSBAR';
        $Cart_product->type='SCB';
        $Cart_product->unit_price=$SJL2->total_price;
        $Cart_product->total_price=$SJL2->total_price/$SJL2->amount;
        $Cart_product->quotation_id=$Quotation_Id;
        $Cart_product->user_id=Auth::user()->id;
        $Cart_product->amount=$SJL2->amount;
        
        $Cart_product->costo_sn_factor=$Costo;
        $Cart_product->save();
        //ligar las instancias
        $SJL2->cart_id=$Cart_product->id;
        $SJL2->save();
        return redirect()->route('selectivo.show',$Quotation_Id);
    }
}
