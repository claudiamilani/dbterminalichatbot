<?php

use App\DBT\Models\IngestionSource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingestion_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('priority')->default(0)->comment('Gives priority when updating existing data. Lower means higher priority');
            $table->longText('default_options')->nullable();
            $table->boolean('enabled')->default(0)->comment('Allows users or scheduled tasks to start an ingestion of this type');
            $table->unsignedBigInteger('created_by_id')->nullable()->index();
            $table->unsignedBigInteger('updated_by_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('created_by_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('updated_by_id')->on('users')->references('id')->onDelete('set null');

        });

        IngestionSource::unguard();
        IngestionSource::create(['id' => IngestionSource::SRC_ADMIN, 'name' => 'MDM Admin Ingestion', 'priority' => 1, 'default_options' => IngestionSource::DEFAULT_OPTIONS]);
        IngestionSource::create(['id' => IngestionSource::SRC_MOBILETHINK, 'name' => 'MobileThink', 'priority' => 2, 'default_options' => IngestionSource::DEFAULT_OPTIONS]);
        IngestionSource::create(['id' => IngestionSource::SRC_GSMA, 'name' => 'GSMA', 'priority' => 3, 'default_options' => IngestionSource::DEFAULT_OPTIONS]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingestion_sources');
    }
};
