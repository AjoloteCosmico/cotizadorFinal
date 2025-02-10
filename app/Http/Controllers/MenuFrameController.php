<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;

class MenuFrameController extends Controller
{
    public function show($id)
    {
        $Quotation_Id = $id;
        $type=Quotation::find($id)->type;
        return view('quotes.selectivo.frames.index', compact('Quotation_Id','type'));
    }
    public function drive_show($id)
    {
        $Quotation_Id = $id;
        return view('quotes.drivein.frames.index', compact('Quotation_Id'));
    }
}
