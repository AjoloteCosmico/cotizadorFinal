<?php

namespace App\Http\Controllers;
use App\Models\Quotation;
use Illuminate\Http\Request;

class DriveInController extends Controller
{
    public function show($id)
    {
        $Quotation_Id = $id;
        $Quotations = Quotation::find($id);
        $Quotations->type = "SELECTIVO";
        $Quotations->save();

        return view('quotes.drivein.index', compact('Quotation_Id'));
    }
    public function index()
    {
        //
    }
}
