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
        Schema::create('correspondences', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('serial_number')->nullable()->unique();
            $table->enum('category', ['request', 'decision', 'circular', 'summons']);
            $table->string('sender');
            $table->string('receiver');
            $table->string('subject');
            $table->text('content');
            $table->enum('priority', ['normal', 'urgent'])->default('normal');
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correspondences');
    }
};
