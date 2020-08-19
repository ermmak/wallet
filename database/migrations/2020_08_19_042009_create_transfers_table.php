<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from');
            $table->foreignId('from_currency');
            $table->decimal('from_currency_rate', 10, 8);
            $table->decimal('from_amount', 12);
            $table->foreignId('to');
            $table->foreignId('to_currency');
            $table->decimal('to_currency_rate', 10, 8);
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();

            $table->foreign('from')
                ->references('id')
                ->on('wallets');
            $table->foreign('from_currency')
                ->references('id')
                ->on('currencies');
            $table->foreign('to')
                ->references('id')
                ->on('wallets');
            $table->foreign('to_currency')
                ->references('id')
                ->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
}
