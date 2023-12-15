<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;

class SelectivoController extends Controller
{
    public function show($id,$double=0)
    {
        if($double==0){
            $System="SELECTIVO";
        }else{

            $System="DOBLE PROFUNDIDAD";
        }
        $Quotation_Id = $id;
        $Quotations = Quotation::find($id);
        
        $Quotations->type = $System;
        $Quotations->save();

        return view('quotes.selectivo.index', compact('Quotation_Id','System'));
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
}
