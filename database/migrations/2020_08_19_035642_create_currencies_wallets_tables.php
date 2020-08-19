<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesWalletsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->decimal('rate', 10, 8);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('currencies')->insert([
            [ 'name' => 'USD Dollar', 'code' => 'usd', 'rate' => 1 ],
            [ 'name' => 'Euro', 'code' => 'eur', 'rate' => 1.19 ],
            [ 'name' => 'Russian Ruble', 'code' => 'rub', 'rate' => 0.014 ],
            [ 'name' => 'Tenge', 'code' => 'kzt', 'rate' => 0.0024 ],
        ]);

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('currency_id');
            $table->decimal('amount', 12);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->foreign('currency_id')
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
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('currencies');
    }
}
