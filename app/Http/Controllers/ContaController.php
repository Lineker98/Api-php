<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\Operacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContaController extends Controller{

    private $conta;
    private $operacao;

    public function __construct(Conta $conta, Operacao $operacao)
    {
        $this->conta = $conta;
        $this->operacao = $operacao;
    }
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $this->conta = DB::select('select * from conta where numero_conta = ?', [1]);
        $data = ['data' => $this->conta];

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
