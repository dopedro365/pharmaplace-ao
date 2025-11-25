<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index(); // 'terms_of_use', 'privacy_policy'
            $table->string('title');
            $table->longText('content');
            $table->string('version', 10)->default('1.0');
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};
