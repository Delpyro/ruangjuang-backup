<?php

use App\Livewire\Admin\UsersManage;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Admin\ReviewsManage;

use App\Livewire\Customers\RaporPage;
use Illuminate\Support\Facades\Route;
use App\Livewire\Customers\BundlePage;
use App\Livewire\Customers\TryoutPage;
use App\Livewire\Admin\DashboardManage;
use App\Livewire\Customers\PaymentPage;
use App\Livewire\Customers\BundleDetail;
use App\Livewire\Customers\TryoutDetail;
use App\Livewire\Customers\MyTryoutsPage;
use App\Livewire\Admin\Bundles\BundlesEdit;
use App\Livewire\Admin\Tryouts\TryoutsEdit;
use App\Livewire\Customers\TryoutWorksheet;
use App\Livewire\Customers\TryoutResultPage;
use App\Livewire\Admin\Bundles\BundlesCreate;
use App\Livewire\Admin\Bundles\BundlesManage;
use App\Livewire\Admin\Tryouts\TryoutsCreate;
use App\Livewire\Admin\Tryouts\TryoutsManage;
use App\Livewire\Customers\TransactionHistory;
use App\Livewire\Admin\TransactionsManage; // <-- Ini adalah kelas lama, TIDAK AKAN DIGUNAKAN
use App\Livewire\Admin\Question\QuestionManage;
use App\Livewire\Admin\QuestionCategoriesManage;
use App\Livewire\Customers\TryoutDiscussionPage;
use App\Http\Controllers\Admin\TinyMceController;
use App\Http\Controllers\PaymentCallbackController;
use App\Livewire\Admin\PromoManage;
use App\Livewire\Admin\QuestionSubCategoriesManage;
use App\Livewire\Customers\TryoutDiscussionWorksheet;
use App\Livewire\Customers\Dashboard as CustomersDashboard;
use App\Livewire\Customers\TestimonialPage; 

// [!code ++] IMPORT KOMPONEN TRANSAKSI BARU (nama umum)
use App\Livewire\Admin\TransactionsIndex;
use App\Livewire\Admin\TransactionsDetail; 
// [!code --] use App\Livewire\Admin\TransactionsManage; // Hapus atau biarkan saja (tidak dipakai)

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', CustomersDashboard::class)->name('customers.dashboard');

// Rute untuk halaman testimoni publik
Route::get('/testimonials', TestimonialPage::class)->name('testimonials.index');

/*
|--------------------------------------------------------------------------
| Midtrans Webhook/Callback Routes (Public - No Auth)
|--------------------------------------------------------------------------
*/
Route::prefix('payment')->name('payment.')->group(function () {
    // Webhook notification dari Midtrans (POST)
    Route::post('/notification', [PaymentCallbackController::class, 'handle'])
        ->name('notification');

    // Redirect pages dari Midtrans setelah pembayaran (GET)
    Route::get('/finish', [PaymentCallbackController::class, 'finish'])
        ->name('finish');

    Route::get('/pending', [PaymentCallbackController::class, 'pending'])
        ->name('pending');

    Route::get('/error', [PaymentCallbackController::class, 'error'])
        ->name('error');

    // Manual status check untuk debugging
    Route::get('/status/{orderId}', [PaymentCallbackController::class, 'checkStatus'])
        ->name('status');
});

/*
|--------------------------------------------------------------------------
| Development/Testing Routes (Opsional)
|--------------------------------------------------------------------------
*/
if (app()->environment('local', 'development')) {
    Route::view('ihaa', 'homepage');
    Route::view('to', 'pengerjaan-tryout');

    Route::get('/debug/midtrans-test', function () {
        $midtransService = app(\App\Services\MidtransService::class);
        return response()->json($midtransService->testConnection());
    });

    Route::get('/debug/payment-test/{tryout_slug}', function ($tryout_slug) {
        $tryout = \App\Models\Tryout::where('slug', $tryout_slug)->first();

        if (!$tryout) {
            return response()->json(['error' => 'Tryout not found'], 404);
        }

        return response()->json([
            'tryout' => [
                'id' => $tryout->id,
                'title' => $tryout->title,
                'slug' => $tryout->slug,
                'price' => $tryout->price,
                'final_price' => $tryout->final_price,
                'discount' => $tryout->discount,
            ],
            'midtrans_config' => [
                'server_key' => substr(config('services.midtrans.server_key'), 0, 10) . '...',
                'client_key' => config('services.midtrans.client_key'),
                'is_production' => config('services.midtrans.is_production'),
                'merchant_id' => 'G681396961'
            ]
        ]);
    });
}

// Test Midtrans Connection
Route::get('/test-midtrans', function () {
    $midtransService = new \App\Services\MidtransService();
    $result = $midtransService->testConnection();

    return response()->json($result);
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', CustomersDashboard::class)->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    Route::get('/my-transactions', TransactionHistory::class)
        ->name('transaction.history');
    Route::get('/my-rapor', RaporPage::class)
        ->name('rapor.index');
        
    // Tryout Routes
    Route::prefix('tryout')->name('tryout.')->group(function () {
        Route::get('/', TryoutPage::class)->name('index');
        Route::get('/my-tryouts', MyTryoutsPage::class)->name('my-tryouts');
        Route::get('/{tryout:slug}', TryoutDetail::class)->name('detail');
        Route::get('/{tryout_slug}/payment', PaymentPage::class)->name('payment');
        Route::get('/{tryout:slug}/start/{attempt}', TryoutWorksheet::class)
        ->name('start');
        Route::get('/{tryout:slug}/continue/{attempt}', TryoutWorksheet::class)
            ->name('continue');
        Route::get('/{tryout:slug}/results', TryoutResultPage::class) 
            ->name('my-results'); 
        Route::get('/{tryout:slug}/discussion', TryoutDiscussionWorksheet::class)
            ->name('discussion');
    });

    // Bundle Routes (Customer View & Purchase)
    Route::prefix('bundle')->name('bundle.')->group(function () {
        Route::get('/', BundlePage::class)->name('index'); 
        // [PERBAIKAN] Menggunakan Route Model Binding untuk model Bundle
        Route::get('/{bundle:slug}', BundleDetail::class)->name('detail'); 
        Route::get('/{bundle_slug}/payment', PaymentPage::class)->name('payment'); 
    });

    // Payment Info Routes
    Route::prefix('payment')->name('payment.')->group(function () {
        
        // [FIX] Menghapus route 'payment.history' yang redundan
        // Route::get('/history', ...)->name('history'); 

        Route::get('/transaction/{orderId}', function ($orderId) {
            $midtransService = app(\App\Services\MidtransService::class);
            $status = $midtransService->getStatus($orderId);
            return response()->json($status);
        })->name('transaction.detail');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
// [FIX] Menggabungkan semua rute admin ke dalam SATU grup
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard & User
    Route::get('dashboard', DashboardManage::class)->name('dashboard');
    Route::get('users', UsersManage::class)->name('users');
    
    // Questions
    Route::get('question-categories', QuestionCategoriesManage::class)->name('question-categories');
    Route::get('question-sub-categories', QuestionSubCategoriesManage::class)->name('question-sub-categories');

    // TinyMCE
    Route::post('tinymce/upload/image', [TinyMceController::class, 'uploadImage'])
        ->name('tinymce.upload.image');

    // Tryouts
    Route::prefix('tryouts')->name('tryouts.')->group(function () {
        Route::get('/', TryoutsManage::class)->name('index');
        Route::get('/create', TryoutsCreate::class)->name('create');
        Route::get('/edit/{id}', TryoutsEdit::class)->name('edit');
    });
    Route::get('/tryouts/{tryoutId}/questions', QuestionManage::class)
        ->name('tryouts.questions');

    // Bundles
    Route::prefix('bundles')->name('bundles.')->group(function () {
        Route::get('/', BundlesManage::class)->name('index');
        Route::get('/create', BundlesCreate::class)->name('create');
        Route::get('/edit/{bundle:slug}', BundlesEdit::class)->name('edit');
    });

    // Reviews
    Route::get('reviews', ReviewsManage::class)->name('reviews.index');
    Route::get('promo', PromoManage::class)->name('promo.index');
    
    // Transaction Management BARU
    Route::prefix('transactions')->name('transactions.')->group(function () {
        
        // [BARU] Halaman Index/Ringkasan (Card View)
        Route::get('/', TransactionsIndex::class)->name('index'); // Menggunakan TransactionsIndex

        // [BARU] Halaman Detail/Tabel Transaksi spesifik untuk Item
        // Parameter {type} bisa 'tryout' atau 'bundle', {id} adalah ID item
        Route::get('/detail/{type}/{id}', TransactionsDetail::class)->name('detail'); // Menggunakan TransactionsDetail

        // Route lama yang mungkin tidak lagi terpakai di komponen baru, tapi tetap dipertahankan
        Route::get('/{id}', function ($id) {
            $transaction = \App\Models\Transaction::with(['user', 'tryout'])->findOrFail($id);
            return view('admin.transactions.detail', compact('transaction'));
        })->name('show'); // Ubah nama rute detail lama agar tidak bentrok dengan 'detail' baru

        Route::post('/{orderId}/sync', function ($orderId) {
            $midtransService = app(\App\Services\MidtransService::class);
            $result = $midtransService->getStatus($orderId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil disinkronisasi',
                    'data' => $result
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyinkronisasi status',
                'error' => $result['error']
            ], 400);
        })->name('sync');
    });

});


/*
|--------------------------------------------------------------------------
| Logout Route
|--------------------------------------------------------------------------
*/
Route::post('logout', function () {
    Auth::guard('web')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';