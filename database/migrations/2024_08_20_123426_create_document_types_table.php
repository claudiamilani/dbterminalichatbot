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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('channel_id')->index();
            $table->unsignedBigInteger('created_by_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_id')->nullable()->index();
            $table->unsignedBigInteger('legacy_id')->nullable()->index()->index();
            $table->timestamps();

            $table->foreign('channel_id')->on('channels')->references('id')->onDelete('cascade');
            $table->foreign('created_by_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('updated_by_id')->on('users')->references('id')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
