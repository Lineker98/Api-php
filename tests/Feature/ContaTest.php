<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContaTest extends TestCase
{

    use RefreshDatabase;

    public function criacaoConta(){


        $conta_data = [
            'numero_conta' => 1,
        ];

        $this->json('POST', 'api/contas', $conta_data)
            ->assertStatus(201)
            ->assertJson([
                "mensagem" => "Conta criada com sucesso!",
            ]);
        
    }

    public function realizacaoDeposito($dados_deposito){

        // operacao de deposito
        $this->json('POST', 'api/contas/deposito', $dados_deposito)
            ->assertStatus(201)
            ->assertJson([
                "mensagem" => "Depósito realizado com sucesso!",
            ]);

    }

    
    /** @test */
    public function contaPodeSerCriada(){
        $this->criacaoConta();
    }

    /** @test */
    public function depositoNaoPodeSerRealizadoSemContaCriada(){
        
        $deposito_data = [
            'numero_conta' => 1,
            'valor' => 350,
            'moeda' => "USD",
        ];

        $this->json('POST', 'api/contas/deposito', $deposito_data)
            ->assertStatus(400);
            
    }

    /** @test */
    public function operacoesDeDepositosSaoInseridasNoRegistroDeTransacoes(){

        $this->criacaoConta();

        $dados_deposito_1 = [
            'numero_conta' => 1,
            'valor' => 350,
            'moeda' => "USD",
        ];
        $dados_deposito_2 = [
            'numero_conta' => 1,
            'valor' => 350,
            'moeda' => "EUR",
        ];

        // primeiro deposito
        $this->realizacaoDeposito($dados_deposito_1);
        // segundo deposito
        $this->realizacaoDeposito($dados_deposito_2);
            
        $this->assertCount(2, DB::select('select * from transacao'));

    }

    /** @test */
    public function RegistroDeDepositoNoSaldo(){

        $this->criacaoConta();

        $dados_deposito = [
            'numero_conta' => 1,
            'valor' => 350,
            'moeda' => "USD",
        ];

        // operacao de deposito
        $this->realizacaoDeposito($dados_deposito);
        
        $this->assertCount(1, DB::select('select * from saldo'));
    }

    /** @test */
    public function somaDosDepositosNaMesmaMoedaNoSaldo(){

        $this->criacaoConta();

        $dados_deposito_1 = [
            'numero_conta' => 1,
            'valor' => 350,
            'moeda' => "USD",
        ];
        $dados_deposito_2 = [
            'numero_conta' => 1,
            'valor' => 500,
            'moeda' => "USD",
        ];

        // primeiro deposito
        $this->realizacaoDeposito($dados_deposito_1);

        // segundo deposito
        $this->realizacaoDeposito($dados_deposito_2);

        $actual = DB::table('saldo')
                    ->where('numero_conta', 1)
                    ->where('moeda', 'like', 'USD')
                    ->sum('saldo');

        $this->assertEquals($dados_deposito_1['valor'] + $dados_deposito_2['valor'], $actual);

    }

    /** @test */
    public function obtencaoDoSaldoSemExpecificacaoDaMoeda(){

        $this->criacaoConta();

        $dados_deposito_1 = [
            'numero_conta' => 1,
            'valor' => 350,
            'moeda' => "USD",
        ];
        // primeiro deposito
        $this->realizacaoDeposito($dados_deposito_1);

        $dados_deposito_2 = [
            'numero_conta' => 1,
            'valor' => 10,
            'moeda' => "EUR",
        ];
        // segundo deposito
        $this->realizacaoDeposito($dados_deposito_2);

        $this->json('GET', 'api/contas/saldo/1')
            ->assertStatus(200)
            ->assertJson([
                0 => [
                    'saldo' => 350,
                    'moeda' => 'USD'
                ],
                1 => [
                    'saldo' => 10,
                    'moeda' => 'EUR'
                ]
            ]);

    }

}
