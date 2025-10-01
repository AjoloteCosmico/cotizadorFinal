<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorepaymentsRequest;
use App\Http\Requests\UpdatepaymentsRequest;
use SplFileInfo;
use Illuminate\Support\Facades\Storage;
use DB;
use PDF;
use Symfony\Component\Process\Process; 
use Symfony\Component\Process\Exception\ProcessFailedException; 
use Illuminate\Support\Facades\Auth;

use App\Models\Quotation;

class ReportsController extends Controller
{
    public function index(){
        
        $User = auth()->user();
        if(auth()->user()->can('VER TODAS LAS COTIZACIONES')){
            $Quotations = Quotation::all();
        }else{
            $Quotations = Quotation::where('user_id',$User->id);
        }
        return view('reports.index',compact('Quotations'));
    }

    public function generate($id,$report,$pdf,$tipo=0)
    {
        $caminoalpoder=public_path();
        $process = new Process(['/var/www/app-env/bin/python3',$caminoalpoder.'/'.$report.'.py',$id,$tipo]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $data = $process->getOutput();
        if($pdf==0){
            return response()->download(public_path('storage/report/'.$report.$id.'.xlsx'));
        }else{
          $process2=new Process(['localc','--headless','--convert-to', 'pdf', $report.$id.'.xlsx'],$caminoalpoder.'/storage/report/');
         
          $process2->run();
          if (!$process2->isSuccessful()) {
             throw new ProcessFailedException($process2);
          }
          $data = $process2->getOutput();
         return response()->download(public_path('storage/report/'.$report.$id.'.pdf'));
     
        }
    }
}
