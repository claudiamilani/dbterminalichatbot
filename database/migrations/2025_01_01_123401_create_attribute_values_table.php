<?php

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
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dbt_attribute_id')->index();
            $table->unsignedBigInteger('ingestion_id')->nullable()->index();
            $table->unsignedBigInteger('ingestion_source_id')->nullable()->index();
            $table->unsignedBigInteger('terminal_id')->index();
            $table->longText('value')->nullable();
            $table->longText('raw_value')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_id')->nullable()->index();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('dbt_attribute_id')->references('id')->on('dbt_attributes')->onDelete('cascade');
            $table->foreign('ingestion_id')->references('id')->on('ingestions')->onDelete('set null');
            $table->foreign('ingestion_source_id')->references('id')->on('ingestion_sources')->onDelete('set null');
            $table->foreign('terminal_id')->references('id')->on('terminals')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
