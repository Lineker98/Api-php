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
            $valor = $depositoData['valor'];
            $moeda = $depositoData['moeda'];
            
            $dados_saldo = DB::select('select * from saldo where numero_conta = ? and moeda = ?',
                                        [$numero_conta, $moeda]);
                
            if (count($dados_saldo) === 0) {
                
                DB::insert('insert into deposito (numero_conta, valor, moeda) values (?, ?, ?)',
                            [$numero_conta, $valor, $moeda]);
                DB::insert('insert into saldo (numero_conta, saldo, moeda) values (?, ?, ?)',
                            [$numero_conta, $valor, $moeda]);
                
                return response()->json(['mensagem' => 'Depósito realizado com sucesso!'], 201);

            } else {
                
                $saldo =  $dados_saldo[0]->saldo;

                $affected = DB::update('update saldo set saldo = ? where moeda = ? and numero_conta = ?',
                [$saldo + $valor, $moeda, $numero_conta]);

                return response()->json(['mensagem' => 'Depósito realizado com sucesso!'], 201);
            }
            
            
            // return response()->json(['msg' => 'Depósito realizado com sucesso'], 201);

        } catch (\Throwable $e) {
            if(config('app.debug')){
                return response()->json(['msg' => $e->getMessage()], 400);
            }
            return response()->json(['mensagem' => 'Erro ao realizar operação!'], 400);
        }
    }


    public function saque(Request $request){

        date_default_timezone_set('America/Sao_Paulo');
        $dados_saque = $request->all();

        $numero_conta = $dados_saque['numero_conta'];
        $moeda = $dados_saque['moeda'];
        $valor = $dados_saque['valor'];
 
        // $sum_deposito = DB::table('transacao')
        //                     ->where('numero_conta', $numero_conta)
        //                     ->where('moeda', 'like', $moeda)
        //                     ->where('', 'like', 'deposito')
        //                     ->sum('valor');

        // $sum_saque = DB::table('transacao')
        //                      ->where('numero_conta', $numero_conta)
        //                      ->where('moeda', 'like', $moeda)
        //                      ->where('', 'like', 'saque')
        //                      ->sum('valor');     
                            
        // $saldo = $sum_deposito - $sum_saque;
        
        $full_date = date('d/m/Y h:i:s');
        $date = date('d/m/Y');

        $resultado = ConsumirApi::getData($moeda, $date);
        //return $resultado;
    }

}
