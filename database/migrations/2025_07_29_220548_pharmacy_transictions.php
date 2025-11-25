<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Updates pharmacy_transactions table to better support
     * multiple proofs and enhanced tracking
     */
    public function up(): void
    {
        Schema::table('pharmacy_transactions', function (Blueprint $table) {
            $table->string('processing_method')->default('automatic')->after('aplicativo');
            $table->decimal('confidence_score', 3, 2)->nullable()->after('processing_method');
            $table->json('extraction_metadata')->nullable()->after('confidence_score');
            $table->boolean('requires_manual_review')->default(false)->after('extraction_metadata');
            $table->timestamp('last_reviewed_at')->nullable()->after('requires_manual_review');
            $table->foreignId('last_reviewed_by')->nullable()->constrained('users')->onDelete('set null')->after('last_reviewed_at');
            
            $table->string('transaction_hash')->nullable()->after('last_reviewed_by');
            $table->boolean('is_duplicate')->default(false)->after('transaction_hash');
            $table->foreignId('original_transaction_id')->nullable()->constrained('pharmacy_transactions')->onDelete('set null')->after('is_duplicate');
            
            $table->json('status_history')->nullable()->after('original_transaction_id');
            $table->text('rejection_reason')->nullable()->after('status_history');
            
            $table->index(['processing_method', 'status']);
            $table->index(['requires_manual_review', 'status']);
            $table->index(['transaction_hash']);
            $table->index(['is_duplicate', 'original_transaction_id']);
            $table->index(['last_reviewed_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacy_transactions', function (Blueprint $table) {
            $table->dropIndex(['processing_method', 'status']);
            $table->dropIndex(['requires_manual_review', 'status']);
            $table->dropIndex(['transaction_hash']);
            $table->dropIndex(['is_duplicate', 'original_transaction_id']);
            $table->dropIndex(['last_reviewed_at', 'status']);
            
            $table->dropForeign(['last_reviewed_by']);
            $table->dropForeign(['original_transaction_id']);
            
            $table->dropColumn([
                'processing_method',
                'confidence_score',
                'extraction_metadata',
                'requires_manual_review',
                'last_reviewed_at',
                'last_reviewed_by',
                'transaction_hash',
                'is_duplicate',
                'original_transaction_id',
                'status_history',
                'rejection_reason'
            ]);
        });
    }
};
