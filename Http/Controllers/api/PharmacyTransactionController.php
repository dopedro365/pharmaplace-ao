<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PharmacyTransactionResource;
use App\Models\PharmacyTransaction;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PharmacyTransactionController extends Controller
{
    /**
     * Display a listing of pharmacy transactions
     */
    public function index(Request $request): JsonResponse
    {
        $query = PharmacyTransaction::with(['pharmacy', 'order']);

        // Filter by pharmacy if user is pharmacy owner
        if (Auth::user()->isPharmacy()) {
            $pharmacy = Auth::user()->pharmacy;
            if ($pharmacy) {
                $query->where('pharmacy_id', $pharmacy->id);
            }
        }

        // Filter by pharmacy_id if provided and user has permission
        if ($request->has('pharmacy_id')) {
            $pharmacyId = $request->get('pharmacy_id');
            
            // Check if user can view this pharmacy's transactions
            $pharmacy = Pharmacy::findOrFail($pharmacyId);
            Gate::authorize('viewTransactions', $pharmacy);
            
            $query->where('pharmacy_id', $pharmacyId);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('data_transferencia', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->whereDate('data_transferencia', '<=', $request->get('date_to'));
        }

        // Filter by amount range
        if ($request->has('amount_min')) {
            $query->where('valor', '>=', $request->get('amount_min'));
        }

        if ($request->has('amount_max')) {
            $query->where('valor', '<=', $request->get('amount_max'));
        }

        // Search by reference or IBAN
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('referencia', 'like', "%{$search}%")
                  ->orWhere('cliente_iban', 'like', "%{$search}%")
                  ->orWhere('empresa_iban', 'like', "%{$search}%");
            });
        }

        // Order by date (newest first by default)
        $orderBy = $request->get('order_by', 'data_transferencia');
        $orderDirection = $request->get('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Paginate results
        $perPage = min($request->get('per_page', 15), 100); // Max 100 per page
        $transactions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => PharmacyTransactionResource::collection($transactions),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'from' => $transactions->firstItem(),
                'to' => $transactions->lastItem()
            ]
        ]);
    }

    /**
     * Display the specified transaction
     */
    public function show(PharmacyTransaction $transaction): JsonResponse
    {
        // Check authorization
        Gate::authorize('view', $transaction);

        $transaction->load(['pharmacy', 'order']);

        return response()->json([
            'success' => true,
            'data' => (new PharmacyTransactionResource($transaction))->withDetails()
        ]);
    }

    /**
     * Update the specified transaction status
     */
    public function update(Request $request, PharmacyTransaction $transaction): JsonResponse
    {
        Gate::authorize('update', $transaction);

        $request->validate([
            'status' => 'required|in:pending,verified,used,rejected',
            'observacoes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $transaction->status;
        
        $transaction->update([
            'status' => $request->status,
            'observacoes' => $request->observacoes ?? $transaction->observacoes
        ]);

        // Log status change
        \Log::info('Transaction status updated', [
            'transaction_id' => $transaction->id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'updated_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status da transação atualizado com sucesso',
            'data' => new PharmacyTransactionResource($transaction->fresh(['pharmacy', 'order']))
        ]);
    }

    /**
     * Get transaction statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = PharmacyTransaction::query();

        // Filter by pharmacy if user is pharmacy owner
        if (Auth::user()->isPharmacy()) {
            $pharmacy = Auth::user()->pharmacy;
            if ($pharmacy) {
                $query->where('pharmacy_id', $pharmacy->id);
            }
        }

        // Filter by pharmacy_id if provided and user has permission
        if ($request->has('pharmacy_id')) {
            $pharmacyId = $request->get('pharmacy_id');
            $pharmacy = Pharmacy::findOrFail($pharmacyId);
            Gate::authorize('viewTransactions', $pharmacy);
            $query->where('pharmacy_id', $pharmacyId);
        }

        // Date range filter
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $query->whereBetween('data_transferencia', [$dateFrom, $dateTo]);

        $transactions = $query->get();

        $statistics = [
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
                'days' => now()->parse($dateFrom)->diffInDays(now()->parse($dateTo)) + 1
            ],
            'totals' => [
                'count' => $transactions->count(),
                'amount' => $transactions->sum('valor'),
                'amount_formatted' => number_format($transactions->sum('valor'), 2, ',', '.') . ' Kz'
            ],
            'by_status' => [
                'pending' => [
                    'count' => $transactions->where('status', 'pending')->count(),
                    'amount' => $transactions->where('status', 'pending')->sum('valor')
                ],
                'verified' => [
                    'count' => $transactions->where('status', 'verified')->count(),
                    'amount' => $transactions->where('status', 'verified')->sum('valor')
                ],
                'used' => [
                    'count' => $transactions->where('status', 'used')->count(),
                    'amount' => $transactions->where('status', 'used')->sum('valor')
                ],
                'rejected' => [
                    'count' => $transactions->where('status', 'rejected')->count(),
                    'amount' => $transactions->where('status', 'rejected')->sum('valor')
                ]
            ],
            'by_bank' => $transactions->groupBy('cliente_banco')->map(function($group, $bank) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('valor')
                ];
            }),
            'daily_summary' => $transactions->groupBy(function($transaction) {
                return $transaction->data_transferencia->format('Y-m-d');
            })->map(function($group, $date) {
                return [
                    'date' => $date,
                    'count' => $group->count(),
                    'amount' => $group->sum('valor')
                ];
            })->values(),
            'recent_activity' => [
                'last_24h' => $transactions->where('created_at', '>=', now()->subDay())->count(),
                'last_7d' => $transactions->where('created_at', '>=', now()->subWeek())->count(),
                'last_30d' => $transactions->where('created_at', '>=', now()->subMonth())->count()
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Bulk update transaction statuses
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_ids' => 'required|array|min:1',
            'transaction_ids.*' => 'required|exists:pharmacy_transactions,id',
            'status' => 'required|in:pending,verified,used,rejected',
            'observacoes' => 'nullable|string|max:1000'
        ]);

        $transactions = PharmacyTransaction::whereIn('id', $request->transaction_ids)->get();

        // Check authorization for each transaction
        foreach ($transactions as $transaction) {
            Gate::authorize('update', $transaction);
        }

        $updatedCount = PharmacyTransaction::whereIn('id', $request->transaction_ids)
            ->update([
                'status' => $request->status,
                'observacoes' => $request->observacoes,
                'updated_at' => now()
            ]);

        \Log::info('Bulk transaction status update', [
            'transaction_ids' => $request->transaction_ids,
            'new_status' => $request->status,
            'updated_count' => $updatedCount,
            'updated_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Status de {$updatedCount} transação(ões) atualizado com sucesso",
            'updated_count' => $updatedCount
        ]);
    }
}
