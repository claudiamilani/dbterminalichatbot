<?php

use App\DBT\Transpose;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transpose_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dbt_attribute_id')->nullable()->index();
            $table->string('label');
            $table->string('type');
            $table->unsignedBigInteger('display_order')->default(0);
            $table->timestamps();
            $table->foreign('dbt_attribute_id')->references('id')->on('dbt_attributes')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transpose_configs');
    }
};
