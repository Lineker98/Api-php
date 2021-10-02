<?php

namespace App\Http\Controllers;

use App\API\ConsumirApi;
use App\Models\Conta;
use App\Models\Operacao;
use GrahamCampbell\ResultType\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;

class ContaController extends Controller{

    
    private $full_date;
    private $date;

    public function __construct()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $this->full_date = date('d/m/Y h:i:s');
        $this->date = date('d/m/Y');
    }


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

        $dados_saque = $request->all();

        $numero_conta = $dados_saque['numero_conta'];
        $moeda = $dados_saque['moeda'];
        $valor = $dados_saque['valor'];
 
        $saldo = DB::table('saldo')
                            ->where('numero_conta', $numero_conta)
                            ->where('moeda', 'like', $moeda)
                            ->sum('saldo');

        
        if ($saldo >= $valor) {

            DB::update('update saldo set saldo = ? where moeda = ? and numero_conta = ?',
                        [$saldo - $valor, $moeda, $numero_conta]);
            return response()->json(['mensagem' => 'Saque realizado com sucesso!'], 201);

        } else {
            
        }

        $cotacao_moeda = ConsumirApi::getData($moeda, $this->date);
        $cotacao_moeda = json_decode($cotacao_moeda);

        //$cotacao_moeda->value[0]->dataHoraCotacao
        //return $cotacao_moeda->value[0];
    }

    public function saldo($numero_conta, $moeda = null){

        try{
            if ($moeda === null) { 
                
                $saldo = DB::select('select saldo, moeda from saldo where numero_conta = ?', [$numero_conta]);
                return response()->json($saldo, 200);

            } else {

                //para cada moeda e saldo na conta
                $moeda_saldo = DB::select('select moeda, saldo from saldo where numero_conta = ?', [$numero_conta]);

                $saldo_total = 0;

                foreach ($moeda_saldo as $key => $value) {

                    // sigla da moeda
                    $sigla_moeda =  $value->moeda;
                    // obtenção da cotação da moeda para compra
                    $cotacao_moeda = ConsumirApi::getData($sigla_moeda, $this->date);
                    $cotacao_moeda = json_decode($cotacao_moeda);

                    if ($sigla_moeda === $moeda) {
                        $saldo_total = $saldo_total + $value->saldo;
                    } else {

                        // obtenção da cotação da moeda para realizar a venda para o saque na moeda desejada
                        $moeda_saque = ConsumirApi::getData($moeda, $this->date);
                        $moeda_saque = json_decode($moeda_saque);

                        $saldo_total += ($value->saldo * $cotacao_moeda->value[0]->cotacaoCompra)/$moeda_saque->value[0]->cotacaoVenda;

                    }
                    
                }
                return response()->json(['Saldo total em '.$moeda.' = ' => $saldo_total], 200);
            }
        }catch (\Throwable $e) {
            
            if(config('app.debug')){
                return response()->json(['msg' => $e->getMessage()], 400);
            }
            return response()->json(['mensagem' => 'Erro na obtenção do saldo!'], 400);
        }
    }

}
