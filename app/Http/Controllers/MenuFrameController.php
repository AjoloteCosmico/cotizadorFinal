<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuFrameController extends Controller
{
    public function show($id)
    {
        $Quotation_Id = $id;
        return view('quotes.selectivo.frames.index', compact('Quotation_Id'));
    }
    public function drive_show($id)
    {
        $Quotation_Id = $id;
        return view('quotes.drivein.frames.index', compact('Quotation_Id'));
    }
}
