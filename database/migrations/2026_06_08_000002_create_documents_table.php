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
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('name_slug');
            $table->string('reference_number')->unique();
            $table->enum('type', ['inbound', 'outbound']);
            $table->enum('status', ['draft', 'verified'])->default('draft');
            $table->string('upload_date', 8);
            $table->unsignedInteger('sequence')->unique();
            $table->string('s3_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'type', 'category_id', 'upload_date', 'sequence']);
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
