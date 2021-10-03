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

        $deposito_data_1 = [
            'numero_conta' => 1,
            'valor' => 350,
            'moeda' => "USD",
        ];
        // primeiro deposito
        $this->json('POST', 'api/contas/deposito', $deposito_data_1)
            ->assertStatus(201)
            ->assertJson([
                "mensagem" => "Depósito realizado com sucesso!",
            ]);

        $deposito_data_2 = [
            'numero_conta' => 1,
            'valor' => 350,
            'moeda' => "EUR",
        ];
        // segundo deposito
        $this->json('POST', 'api/contas/deposito', $deposito_data_2)
            ->assertStatus(201)
            ->assertJson([
                "mensagem" => "Depósito realizado com sucesso!",
            ]);
            
        $this->assertCount(2, DB::select('select * from transacao'));

    }

    /** @test */
    public function RegistroDeDepositoNoSaldo(){

        $this->criacaoConta();

        $deposito_data = [
            'numero_conta' => 1,
            'valor' => 350,
            'moeda' => "USD",
        ];

        // operacao de deposito
        $this->json('POST', 'api/contas/deposito', $deposito_data)
            ->assertStatus(201)
            ->assertJson([
                "mensagem" => "Depósito realizado com sucesso!",
            ]);
        
        $this->assertCount(1, DB::select('select * from saldo'));
    }

    /** @test */
    public function somaDosDepositosNaMesmaMoedaNoSaldo(){

        $this->criacaoConta();

        $deposito_data_1 = [
            'numero_conta' => 1,
            'valor' => 350,
            'moeda' => "USD",
        ];

        // primeiro deposito
        $this->json('POST', 'api/contas/deposito', $deposito_data_1)
            ->assertStatus(201)
            ->assertJson([
                "mensagem" => "Depósito realizado com sucesso!",
            ]);
        
        $deposito_data_2 = [
            'numero_conta' => 1,
            'valor' => 500,
            'moeda' => "USD",
        ];
    
        // segundo deposito
        $this->json('POST', 'api/contas/deposito', $deposito_data_2)
            ->assertStatus(201)
            ->assertJson([
                "mensagem" => "Depósito realizado com sucesso!",
            ]);

        $actual = DB::table('saldo')
                    ->where('numero_conta', 1)
                    ->where('moeda', 'like', 'USD')
                    ->sum('saldo');
        $this->assertEquals($deposito_data_1['valor'] + $deposito_data_2['valor'], $actual);

    }

    
}
