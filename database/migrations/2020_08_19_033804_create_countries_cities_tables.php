<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCountriesCitiesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('country_id');

            $table->unique(['name', 'country_id']);
            $table->foreign('country_id')
                ->references('id')
                ->on('countries');
        });

        $kzId = DB::table('countries')->insertGetId([ 'name' => 'Kazakhstan' ]);
        $ruId = DB::table('countries')->insertGetId([ 'name' => 'Russia' ]);
        $deId = DB::table('countries')->insertGetId([ 'name' => 'Germany' ]);

        DB::table('cities')->insert([
            [ 'name' => 'Almaty', 'country_id' => $kzId ],
            [ 'name' => 'Astana', 'country_id' => $kzId ],
            [ 'name' => 'Moscow', 'country_id' => $ruId ],
            [ 'name' => 'Berlin', 'country_id' => $deId ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('countries');
    }
}
