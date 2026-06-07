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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['inbound', 'outbound']);
            $table->string('upload_date', 8);
            $table->unsignedInteger('sequence')->unique();
            $table->string('s3_path');
            $table->string('original_filename');
            $table->string('mime_type')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'type', 'upload_date', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
