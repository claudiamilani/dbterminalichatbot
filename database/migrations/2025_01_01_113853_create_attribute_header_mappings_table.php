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
        Schema::create('attribute_header_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('header_name');
            $table->unsignedBigInteger('dbt_attribute_id')->nullable()->index();
            $table->unsignedBigInteger('ingestion_source_id')->nullable()->index();
            $table->unsignedBigInteger('created_by_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('ingestion_source_id')->on('ingestion_sources')->references('id')->onDelete('set null');
            $table->foreign('dbt_attribute_id')->on('dbt_attributes')->references('id')->onDelete('set null');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_header_mappings');
    }
};
