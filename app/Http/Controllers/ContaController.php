<?php

namespace App\Http\Controllers;

use App\API\ConsumirApi;
use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContaController extends Controller{

    
    private $full_date;
    private $date;

    public function __construct()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $this->full_date = date('Y-m-d h:i:s');
        $this->date = date('d/m/Y');
    }

    public function criacao_conta(Request $request){

        try{
            DB::insert('insert into conta (numero_conta, created_at) values (?, ?)',
            [$request->numero_conta, $this->full_date]);
            return response()->json(['mensagem' => 'Conta criada com sucesso!'], 201);

        }catch (\Throwable $e) {
            if(config('app.debug')){
                return response()->json(['mensagem' => $e->getMessage()], 400);
            }
            return response()->json(['mensagem' => 'Erro ao criar a conta!'], 400);
        }

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
                
                DB::insert('insert into transacao (numero_conta, valor, moeda, tipo_transacao, data_transacao) values (?, ?, ?, ?, ?)',
                            [$numero_conta, $valor, $moeda, 'deposito', $this->full_date]);
                DB::insert('insert into saldo (numero_conta, saldo, moeda) values (?, ?, ?)',
                            [$numero_conta, $valor, $moeda]);
                
                return response()->json(['mensagem' => 'Depósito realizado com sucesso!'], 201);

            } else {
                
                $saldo =  $dados_saldo[0]->saldo;

                DB::insert('insert into transacao (numero_conta, valor, moeda, tipo_transacao, data_transacao) values (?, ?, ?, ?, ?)',
                            [$numero_conta, $valor, $moeda, 'deposito', $this->full_date]);

                DB::update('update saldo set saldo = ? where moeda = ? and numero_conta = ?',
                [$saldo + $valor, $moeda, $numero_conta]);

                return response()->json(['mensagem' => 'Depósito realizado com sucesso!'], 201);
            }
            
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

            DB::insert('insert into transacao (numero_conta, valor, moeda, tipo_transacao, data_transacao) values (?, ?, ?, ?, ?)',
                        [$numero_conta, $valor, $moeda, 'saque', $this->full_date]);

            return response()->json(['mensagem' => 'Saque realizado com sucesso!'], 201);

        } else {
            return response()->json(['mensagem' => 'Saldo Insuficiente'], 201);
        }

        //$cotacao_moeda = ConsumirApi::getData($moeda, $this->date);
        //$cotacao_moeda = json_decode($cotacao_moeda);

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
            return response()->json(['msg' => 'Erro na obtenção do saldo!'], 400);
        }
    }

    public function extrato($numero_conta, $data_inicial = null, $data_final = null){

        try{
            // sem intervalo de tempo definido
            if ($data_inicial === null && $data_final === null) {
                $extrato = DB::select('select * from transacao where numero_conta = ?', [$numero_conta]);
                return response()->json(['Extrato da conta' => $extrato], 200);

            // especificação de início e fim do período
            } elseif($data_inicial !== null && $data_final !== null) {
                $extrato = DB::select('select * from transacao where numero_conta = ? and data_transacao between ? and ?', [$numero_conta, $data_inicial, $data_final]);
                return response()->json(['Extrato da conta' => $extrato], 200);

            // data inicial especificada até o dia atual
            } else{

                $dia_atual = date('Y-m-d');
                $extrato = DB::select('select * from transacao where numero_conta = ? and data_transacao between ? and ?', [$numero_conta, $data_inicial, $dia_atual]);
                return response()->json(['Extrato da conta' => $extrato], 200);
            }
        }
        catch (\Throwable $e) {

            if(config('app.debug')){
                return response()->json(['msg' => $e->getMessage()], 400);
            }
            return response()->json(['msg' => 'Erro na obtenção do extrato!'], 400);
        }
    }

}
