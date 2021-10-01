<?php

namespace App\Http\Controllers;

use App\API\ConsumirApi;
use App\Models\Conta;
use App\Models\Operacao;
use GrahamCampbell\ResultType\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContaController extends Controller{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->conta = DB::select('select * from conta');
        return  $this->conta;
    }



    /**
     * Realiza depósito na conta requerida.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deposito(Request $request)
    {
        
        try {

            $depositoData = $request->all();

            $numero_conta = $depositoData['numero_conta'];
            $tipo_transacao = $depositoData['tipo_transacao'];
            $valor = $depositoData['valor'];
            $moeda = $depositoData['moeda'];
            DB::insert('insert into transacao (numero_conta, tipo_transacao, valor, moeda)
                        values (?, ?, ?, ?)', [$numero_conta, $tipo_transacao, $valor, $moeda]);
            
            return response()->json(['msg' => 'Depósito realizado com sucesso'], 201);

        } catch (\Throwable $e) {
            if(config('app.debug')){
                return response()->json(['msg' => $e->getMessage()]);
            }
        }
    }


    public function saque(Request $request){

        $dados_saque = $request->all();

        $numero_conta = $dados_saque['numero_conta'];
        $moeda = $dados_saque['moeda'];
        $valor = $dados_saque['valor_saque'];
 
        $sum_deposito = DB::table('transacao')
                            ->where('numero_conta', 1)
                            ->where('moeda', 'like', $moeda)
                            ->where('tipo_transacao', 'like', 'deposito')
                            ->sum('valor');

        // $sum_saque = DB::table('transacao')
        //                     ->where('numero_conta', 1)
        //                     ->where('moeda', 'like', 'USD')
        //                     ->where('tipo_transacao', 'like', 'saque')
        //                     ->sum('valor');     
                            
        // $saldo = $sum_deposito - $sum_saque;
        return $sum_deposito;
        

        // $resultado = ConsumirApi::getData("USD", "09-02-2021");

        // return $resultado;
    }

}
