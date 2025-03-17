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
        Schema::create('ingestions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ingestion_source_id');
            $table->longText('file_path');
            $table->unsignedInteger('status')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_id')->nullable()->index();
            $table->longText('notify_mails')->nullable();
            $table->longText('options')->nullable();
            $table->longText('message')->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('ingestion_source_id')->on('ingestion_sources')->references('id')->onDelete('set null');
            $table->foreign('created_by_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('updated_by_id')->on('users')->references('id')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingestions');
    }
};
