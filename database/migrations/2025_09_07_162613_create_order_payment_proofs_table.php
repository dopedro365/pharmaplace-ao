<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates a dedicated table to store individual payment proofs
     * with detailed tracking and validation information
     */
    public function up(): void
    {
        Schema::create('order_payment_proofs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('pharmacy_transaction_id')->nullable()->constrained('pharmacy_transactions')->onDelete('set null');
            
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('file_hash')->nullable(); // for duplicate detection
            
            $table->enum('status', ['uploaded', 'processing', 'verified', 'rejected', 'used'])->default('uploaded');
            $table->decimal('extracted_amount', 15, 2)->nullable();
            $table->date('extracted_date')->nullable();
            $table->string('extracted_reference')->nullable();
            $table->json('extracted_data')->nullable(); // Store all extracted data as JSON
            
            $table->boolean('is_valid')->default(false);
            $table->json('validation_errors')->nullable(); // Store validation errors as JSON
            $table->text('processing_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->json('processing_history')->nullable(); // Track all processing steps
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index(['status', 'processed_at']);
            $table->index(['file_hash']); // For duplicate detection
            $table->index(['extracted_reference']); // For reference lookups
            $table->index(['pharmacy_transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payment_proofs');
    }
};
