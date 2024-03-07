<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Quotation;
class MenuJoistController extends Controller
{
    public function show($id)
    {
        $Quotation=Quotation::find($id);
        $System=$Quotation->type;
        $Quotation_Id = $id;
        return view('quotes.selectivo.joists.index', compact('Quotation_Id','System'));
    }
    public function drive_show($id)
    {
        $Quotation_Id = $id;
        return view('quotes.drivein.joists.index', compact('Quotation_Id'));
    }
}
