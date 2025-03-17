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
        Schema::create('gsma_terminals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('legacy_terminal_id')->nullable()->index();
            $table->unsignedBigInteger('legacy_id')->index();
            $table->unsignedBigInteger('terminal_id')->index();
            $table->timestamps();

            $table->foreign('terminal_id')->on('terminals')->references('id')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gsma_terminals');
    }
};
