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
        Schema::create('tacs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('terminal_id');
            $table->string('value');
            $table->unsignedBigInteger('ingestion_id')->nullable()->index();
            $table->unsignedBigInteger('ingestion_source_id')->nullable()->index();
            $table->unsignedBigInteger('created_by_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_id')->nullable()->index();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('created_by_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('updated_by_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('ingestion_source_id')->on('ingestion_sources')->references('id')->onDelete('set null');
            $table->foreign('ingestion_id')->on('ingestions')->references('id')->onDelete('set null');
            $table->foreign('terminal_id')->on('terminals')->references('id')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tacs');
    }
};
