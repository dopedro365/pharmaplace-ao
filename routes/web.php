<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\PharmacyLocationController;
use App\Http\Controllers\PharmacyProductController;
use App\Http\Controllers\PharmacyStoreController;
use App\Http\Controllers\ProductController;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

// ========================================
// ROTAS PÚBLICAS
// ========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
Route::get('/pharmacy/{pharmacy}/store', [PharmacyStoreController::class, 'index'])->name('pharmacy.store');
Route::get('/products/{product}', [PharmacyProductController::class, 'show'])->name('product.show');
Route::get('/pharmacy/location', [PharmacyLocationController::class, 'index'])->name('pharmacy.location');
Route::get('/sellers', [PharmacyController::class, 'index'])->name('pharmacies.index');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

Route::get('/termos-de-uso', [App\Http\Controllers\Api\LegalDocumentController::class, 'showTerms'])
    ->name('legal.terms');

Route::get('/politica-de-privacidade', [App\Http\Controllers\Api\LegalDocumentController::class, 'showPrivacy'])
    ->name('legal.privacy');

Route::get('/api/legal-documents/{type}', [App\Http\Controllers\Api\LegalDocumentController::class, 'show'])
    ->name('api.legal.document')
    ->where('type', 'terms_of_use|privacy_policy');

// ========================================
// ROTAS DE AUTENTICAÇÃO CUSTOMIZADAS
// ========================================

// REDIRECIONAMENTO DO FILAMENT PARA LOGIN PERSONALIZADO
Route::get('/painel/login', function () {
    return redirect()->route('login');
})->name('filament.painel.auth.login');

// Rotas de autenticação customizadas
Route::middleware('guest')->group(function () {
    // LOGIN
    Route::get('/login', [App\Http\Controllers\Auth\CustomLoginController::class, 'showLoginForm'])
        ->name('login');
    
    Route::post('/login', [App\Http\Controllers\Auth\CustomLoginController::class, 'login'])
        ->name('login.process');

    // REGISTRO
    Route::get('/register', [App\Http\Controllers\Auth\CustomRegisterController::class, 'showRegistrationForm'])
        ->name('register');
    
    Route::post('/register', [App\Http\Controllers\Auth\CustomRegisterController::class, 'register'])
        ->name('register.process');

    // RESET DE SENHA
    Route::get('/password/reset', [App\Http\Controllers\Auth\PasswordResetController::class, 'showRequestForm'])
        ->name('password.request');
    
    Route::post('/password/email', [App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])
        ->name('password.send-link');
    
    Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])
        ->name('password.reset.form');
    
    Route::post('/password/reset', [App\Http\Controllers\Auth\PasswordResetController::class, 'resetPassword'])
        ->name('password.reset');
});

// LOGOUT
Route::post('/logout', [App\Http\Controllers\Auth\CustomLoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ROTA PARA CONTA PENDENTE
Route::get('/account/pending', [App\Http\Controllers\Auth\CustomRegisterController::class, 'showPendingAccount'])
    ->name('account.pending')
    ->middleware('auth');

// ROTA PARA PÁGINA DE BOAS-VINDAS APÓS REGISTRO
Route::get('/welcome', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    return view('auth.welcome-success');
})->name('welcome.success')->middleware('auth');

// ========================================
// ROTAS PROTEGIDAS (USUÁRIOS LOGADOS)
// ========================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        // Redirecionar usuários não aprovados para página de pendência
        if ($user->status !== 'approved') {
            return redirect()->route('account.pending');
        }
        
        return view('dashboard');
    })->name('dashboard');
    
    // DOWNLOADS DE PEDIDOS
    Route::get('/painel/order/{order}/download-pdf', function (Order $order) {
        $user = Auth::user();
        
        if ($user->role === 'customer' && $order->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para baixar este pedido.');
        }
        
        if ($user->role === 'pharmacy' && $order->pharmacy_id !== ($user->pharmacy->id ?? 0)) {
            abort(403, 'Você não tem permissão para baixar este pedido.');
        }

        try {
            $pdf = Pdf::loadView('pdf.order-detail', [
                'order' => $order->load(['user', 'pharmacy', 'items.product', 'bankAccount'])
            ]);

            return $pdf->download("pedido-{$order->order_number}.pdf");
        } catch (\Exception $e) {
            abort(500, 'Erro ao gerar PDF do pedido: ' . $e->getMessage());
        }
    })->name('order.download.pdf');

    Route::get('/painel/order/{order}/download-proof', function (Order $order) {
        $user = Auth::user();
        
        if ($user->role === 'customer' && $order->user_id !== $user->id) {
            abort(403, 'Você não tem permissão para baixar este comprovativo.');
        }
        
        if ($user->role === 'pharmacy' && $order->pharmacy_id !== ($user->pharmacy->id ?? 0)) {
            abort(403, 'Você não tem permissão para baixar este comprovativo.');
        }

        if (!$order->payment_proof) {
            abort(404, 'Comprovativo não encontrado.');
        }

        try {
            $filePath = storage_path('app/public/' . $order->payment_proof);
            
            if (!file_exists($filePath)) {
                abort(404, 'Arquivo do comprovativo não encontrado.');
            }

            $fileName = 'comprovativo-' . $order->order_number . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
            
            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            abort(500, 'Erro ao baixar comprovativo: ' . $e->getMessage());
        }
    })->name('order.download.proof');
});

// ========================================
// ROTAS DE CARRINHO
// ========================================
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/{pharmacy}', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

Route::middleware('auth')->group(function () {
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
});

// ========================================
// ROTAS DE CHECKOUT (SEM MIDDLEWARE)
// ========================================
Route::get('/checkout/{pharmacy}', [CheckoutController::class, 'show'])->name('checkout.form');

//ROTA DE SUCESSO DO PEDIDO
Route::get('/order/success', function () {
    return view('order-success');
})->name('order.success');
