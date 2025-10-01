<?php

namespace App\Http\Controllers;
use Symfony\Component\Process\Process; 
use Symfony\Component\Process\Exception\ProcessFailedException; 

use Illuminate\Http\Request;

class RedaccionController extends Controller
{
    public function generate($id,$pdf)
    {
        $QuotationId=$id;
        $caminoalpoder=public_path();
        $process = new Process(['/var/www/app-env/bin/python3', 'redaccion.py',$QuotationId],$caminoalpoder);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $data = $process->getOutput();
        
           
       if($pdf==0){
            return response()->download(public_path('storage/Cotizacion'.$QuotationId.'.docx'));
        }else{
          $process2=new Process(['lowriter','--convert-to', 'pdf', 'Cotizacion'.$QuotationId.'.docx'],$caminoalpoder.'/storage/');
         
          $process2->run();
          if (!$process2->isSuccessful()) {
             throw new ProcessFailedException($process2);
          }
          $data = $process2->getOutput();
         return response()->download(public_path('storage/'.'Cotizacion'.$QuotationId.'.pdf'));
     
        }
    }
}
