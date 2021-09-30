<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOperacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operacoes', function (Blueprint $table) {
            $table->foreignId('numero_conta');
            $table->string('tipo_operacao');
            $table->float('valor');
            $table->string('moeda');
            $table->timestamps();

            // A chave estrangeira 'numero_conta'
            $table->foreign('numero_conta')

                // referencia o campo 'numero_conta'
                ->references('numero_conta')

                // da tabela 'contas'
                ->on('contas')

                // Deleção em cascata
                ->onDelete('CASCADE')

                // Update em cascata
                ->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operacoes');
    }
}
