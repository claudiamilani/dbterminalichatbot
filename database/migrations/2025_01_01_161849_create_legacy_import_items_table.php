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
        Schema::create('legacy_import_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('legacy_import_id')->index();
            $table->string('legacy_id')->nullable()->index();
            $table->unsignedInteger('status')->default(0);
            $table->string('result')->nullable();
            $table->longText('message')->nullable();
            $table->timestamps();

            $table->foreign('legacy_import_id')->references('id')->on('legacy_imports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_import_items');
    }
};
