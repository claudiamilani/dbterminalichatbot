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
        Schema::create('dbt_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->longText('type_options')->nullable()->comment('Format values, Multiple, searchable list...');
            $table->longText('description')->nullable();
            $table->longText('default_value')->nullable()->comment('Default value or selected options');
            $table->boolean('published')->default(false);
            $table->unsignedBigInteger('attr_category_id')->index();
            $table->unsignedBigInteger('display_order')->default(0);
            $table->unsignedBigInteger('ingestion_source_id')->nullable()->index();
            $table->unsignedBigInteger('ingestion_id')->nullable()->index();
            $table->unsignedBigInteger('created_by_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_id')->nullable()->index();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('attr_category_id')->on('attr_categories')->references('id');
            $table->foreign('created_by_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('updated_by_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('ingestion_source_id')->on('ingestion_sources')->references('id')->onDelete('set null');
            $table->foreign('ingestion_id')->on('ingestions')->references('id')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dbt_attributes');
    }
};
