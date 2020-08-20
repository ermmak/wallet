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
            $table->foreignId('to');
            $table->timestamp('created_at')->useCurrent();
            $table->decimal('amount', 14);
            $table->boolean('recipient_currency');
            $table->softDeletes();

            $table->index(['from', 'to', 'created_at']);
            $table->foreign('from')
                ->references('id')
                ->on('wallets');
            $table->foreign('to')
                ->references('id')
                ->on('wallets');
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
