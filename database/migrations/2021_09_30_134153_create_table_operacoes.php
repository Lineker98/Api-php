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
        Schema::create('transacao', function (Blueprint $table) {
            $table->foreignId('numero_conta');
            $table->float('valor', 10, 2);
            $table->string('moeda', 3);
            $table->string('tipo_transacao');
            $table->timestamp('data_transacao')->nullable();

            // A chave estrangeira 'numero_conta'
            $table->foreign('numero_conta')

                // referencia o campo 'numero_conta'
                ->references('numero_conta')

                // da tabela 'contas'
                ->on('conta')

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
        Schema::dropIfExists('transacao');
    }
}
