<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained();
            $table->string('name')->index();
            $table->timestamps();
        });

        $entries = config('cities');
        foreach ($entries as $stateName => $counties) {
            $state = \App\Models\State::create(['name' => $stateName]);
            foreach ($counties as $countyName) {
                \App\Models\County::create([
                    'state_id' => $state->id,
                    'name' => $countyName
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('counties');
    }
}
