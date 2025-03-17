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
        Schema::create('legacy_imports', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->boolean('update_existing')->default(true);
            $table->unsignedInteger('status')->default(0);
            $table->longText('message')->nullable();
            $table->unsignedBigInteger('requested_by_id')->nullable()->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_imports');
    }
};
