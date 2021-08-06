<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('portable_weight')->index();
            $table->text('image')->nullable();
            $table->timestamps();
        });

        $vehicleWeights = config('weights');
        foreach ($vehicleWeights as $vehicleWeight) {
            \App\Models\VehicleType::create([
                'name' => $vehicleWeight['title'],
                'portable_weight' => $vehicleWeight['weight'],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_types');
    }
}
