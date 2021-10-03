<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContaTest extends TestCase
{

    use RefreshDatabase;
    
    /** @test */
    public function conta_pode_ser_criada(){

        $this->withoutExceptionHandling();

        $conta_data = [
            'numero_conta' => 1,
        ];

        $this->json('POST', 'api/contas', $conta_data, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJson([
                "mensagem" => "Conta criada com sucesso!",
            ]);

    }

    /** @test */
    public function deposito_nao_pode_ser_realizado_sem_conta_criada(){
        
        $deposito_data = [
            'numero_conta' => 5,
            'valor' => 350,
            'moeda' => "USD",
        ];

        $this->json('POST', 'api/contas/deposito', $deposito_data, ['Accept' => 'application/json'])
            ->assertStatus(400);
            
        
        //$this->assertCount(1, DB::select('select * from transacao'));

    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
