<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// --- CUSTOMER IMPORTS ---
use App\Livewire\Customers\RaporPage;
use App\Livewire\Customers\BundlePage;
use App\Livewire\Customers\TryoutPage;
use App\Livewire\Customers\PaymentPage;
use App\Livewire\Customers\BundleDetail;
use App\Livewire\Customers\TryoutDetail;
use App\Livewire\Customers\MyTryoutsPage;
use App\Livewire\Customers\TryoutWorksheet;
use App\Livewire\Customers\TryoutResultPage;
use App\Livewire\Customers\TransactionHistory;
use App\Livewire\Customers\TryoutDiscussionPage;
use App\Livewire\Customers\TryoutDiscussionWorksheet;
use App\Livewire\Customers\Dashboard as CustomersDashboard;
use App\Livewire\Customers\TestimonialPage; 

// --- ADMIN IMPORTS ---
use App\Livewire\Admin\UsersManage;
use App\Livewire\Admin\ReviewsManage;
use App\Livewire\Admin\UserAkses;
use App\Livewire\Admin\DashboardManage;
use App\Livewire\Admin\Bundles\BundlesEdit;
use App\Livewire\Admin\Tryouts\TryoutsEdit;
use App\Livewire\Admin\Bundles\BundlesCreate;
use App\Livewire\Admin\Bundles\BundlesManage;
use App\Livewire\Admin\Tryouts\TryoutsCreate;
use App\Livewire\Admin\Tryouts\TryoutsManage;
use App\Livewire\Admin\Question\QuestionManage;
use App\Livewire\Admin\QuestionCategoriesManage;
use App\Livewire\Admin\PromoManage;
use App\Livewire\Admin\QuestionSubCategoriesManage;
use App\Livewire\Admin\TransactionsIndex;
use App\Livewire\Admin\TransactionsDetail; 
use App\Livewire\Admin\AssignTryout;

// --- OWNER IMPORTS ---
use App\Livewire\Owner\DashboardManage as OwnerDashboardManage; 
use App\Livewire\Owner\Bundles\BundlesManage as OwnerBundlesManage;
use App\Livewire\Owner\Bundles\BundlesCreate as OwnerBundlesCreate;
use App\Livewire\Owner\Bundles\BundlesEdit as OwnerBundlesEdit;
use App\Livewire\Owner\Tryouts\TryoutsManage as OwnerTryoutsManage;
use App\Livewire\Owner\Tryouts\TryoutsCreate as OwnerTryoutsCreate;
use App\Livewire\Owner\Tryouts\TryoutsEdit as OwnerTryoutsEdit;
use App\Livewire\Owner\ReviewsManage as OwnerReviewsManage;
use App\Livewire\Owner\QuestionCategoriesManage as OwnerQuestionCategoriesManage;
use App\Livewire\Owner\QuestionSubCategoriesManage as OwnerQuestionSubCategoriesManage;
use App\Livewire\Owner\PromoManage as OwnerPromoManage; 
use App\Livewire\Owner\TransactionsIndex as OwnerTransactionsIndex; 
use App\Livewire\Owner\TransactionsDetail as OwnerTransactionsDetail;
use App\Livewire\Owner\UsersManage as OwnerUsersManage;
use App\Livewire\Owner\Question\QuestionManage as OwnerQuestionManage;

// --- CONTROLLER IMPORTS ---
use App\Http\Controllers\Admin\TinyMceController;
use App\Http\Controllers\PaymentCallbackController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', CustomersDashboard::class)->name('customers.dashboard');
Route::get('/testimonials', TestimonialPage::class)->name('testimonials.index');

/*
|--------------------------------------------------------------------------
| Midtrans Webhook/Callback Routes (Public - No Auth)
|--------------------------------------------------------------------------
*/
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/notification', [PaymentCallbackController::class, 'handle'])->name('notification');
    Route::get('/finish', [PaymentCallbackController::class, 'finish'])->name('finish');
    Route::get('/pending', [PaymentCallbackController::class, 'pending'])->name('pending');
    Route::get('/error', [PaymentCallbackController::class, 'error'])->name('error');
    Route::get('/status/{orderId}', [PaymentCallbackController::class, 'checkStatus'])->name('status');
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
        if (!$tryout) return response()->json(['error' => 'Tryout not found'], 404);
        
        return response()->json([
            'tryout' => [
                'id' => $tryout->id, 'title' => $tryout->title, 'slug' => $tryout->slug,
                'price' => $tryout->price, 'final_price' => $tryout->final_price, 'discount' => $tryout->discount,
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

Route::get('/test-midtrans', function () {
    $midtransService = new \App\Services\MidtransService();
    return response()->json($midtransService->testConnection());
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', CustomersDashboard::class)->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    Route::get('/my-transactions', TransactionHistory::class)->name('transaction.history');
    Route::get('/my-rapor', RaporPage::class)->name('rapor.index');
        
    // Tryout Routes
    Route::prefix('tryout')->name('tryout.')->group(function () {
        Route::get('/', TryoutPage::class)->name('index');
        Route::get('/my-tryouts', MyTryoutsPage::class)->name('my-tryouts');
        Route::get('/{tryout:slug}', TryoutDetail::class)->name('detail');
        Route::get('/{tryout_slug}/payment', PaymentPage::class)->name('payment');
        Route::get('/{tryout:slug}/start/{attempt}', TryoutWorksheet::class)->name('start');
        Route::get('/{tryout:slug}/continue/{attempt}', TryoutWorksheet::class)->name('continue');
        Route::get('/{tryout:slug}/results', TryoutResultPage::class)->name('my-results'); 
        Route::get('/{tryout:slug}/discussion', TryoutDiscussionWorksheet::class)->name('discussion');
    });

    // Bundle Routes
    Route::prefix('bundle')->name('bundle.')->group(function () {
        Route::get('/', BundlePage::class)->name('index'); 
        Route::get('/{bundle:slug}', BundleDetail::class)->name('detail'); 
        Route::get('/{bundle_slug}/payment', PaymentPage::class)->name('payment'); 
    });

    // Payment Info Routes
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/transaction/{orderId}', function ($orderId) {
            $midtransService = app(\App\Services\MidtransService::class);
            return response()->json($midtransService->getStatus($orderId));
        })->name('transaction.detail');
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN Routes (Hanya untuk role 'admin')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('dashboard', DashboardManage::class)->name('dashboard');
    Route::get('users', UsersManage::class)->name('users');
    
    Route::get('user/akses', UserAkses::class)->name('user.akses'); 
    Route::get('user/akses/{id}', UserAkses::class)->name('user.akses.detail');
    
    Route::get('assign-tryout', AssignTryout::class)->name('assign-tryout');
    
    Route::get('question-categories', QuestionCategoriesManage::class)->name('question-categories');
    Route::get('question-sub-categories', QuestionSubCategoriesManage::class)->name('question-sub-categories');

    Route::post('tinymce/upload/image', [TinyMceController::class, 'uploadImage'])->name('tinymce.upload.image');

    Route::prefix('tryouts')->name('tryouts.')->group(function () {
        Route::get('/', TryoutsManage::class)->name('index');
        Route::get('/create', TryoutsCreate::class)->name('create');
        Route::get('/edit/{id}', TryoutsEdit::class)->name('edit');
    });
    
    Route::get('/tryouts/{tryoutId}/questions', QuestionManage::class)->name('tryouts.questions');

    Route::prefix('bundles')->name('bundles.')->group(function () {
        Route::get('/', BundlesManage::class)->name('index');
        Route::get('/create', BundlesCreate::class)->name('create');
        Route::get('/edit/{bundle:slug}', BundlesEdit::class)->name('edit');
    });

    Route::get('reviews', ReviewsManage::class)->name('reviews.index');
    Route::get('promo', PromoManage::class)->name('promo.index');
    
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', TransactionsIndex::class)->name('index'); 
        Route::get('/detail/{type}/{id}', TransactionsDetail::class)->name('detail'); 
        Route::get('/{id}', function ($id) {
            $transaction = \App\Models\Transaction::with(['user', 'tryout'])->findOrFail($id);
            return view('admin.transactions.detail', compact('transaction'));
        })->name('show'); 

        Route::post('/{orderId}/sync', function ($orderId) {
            $midtransService = app(\App\Services\MidtransService::class);
            $result = $midtransService->getStatus($orderId);
            if ($result['success']) {
                return response()->json(['success' => true, 'message' => 'Status berhasil disinkronisasi', 'data' => $result]);
            }
            return response()->json(['success' => false, 'message' => 'Gagal menyinkronisasi status', 'error' => $result['error']], 400);
        })->name('sync');
    });
});

/*
|--------------------------------------------------------------------------
| OWNER Routes (Hanya untuk role 'owner')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'owner'])->prefix('owner')->name('owner.')->group(function () {
    
    Route::get('dashboard', OwnerDashboardManage::class)->name('dashboard');

    Route::post('tinymce/upload/image', [TinyMceController::class, 'uploadImage'])->name('tinymce.upload.image');

    Route::prefix('bundles')->name('bundles.')->group(function () {
        Route::get('/', OwnerBundlesManage::class)->name('index');
        Route::get('/create', OwnerBundlesCreate::class)->name('create');
        Route::get('/edit/{bundle:slug}', OwnerBundlesEdit::class)->name('edit');
    });

    Route::prefix('tryouts')->name('tryouts.')->group(function () {
        Route::get('/', OwnerTryoutsManage::class)->name('index');
        Route::get('/create', OwnerTryoutsCreate::class)->name('create');
        Route::get('/edit/{id}', OwnerTryoutsEdit::class)->name('edit');
    });
    
    Route::get('/tryouts/{tryoutId}/questions', OwnerQuestionManage::class)->name('tryouts.questions');
    
    Route::get('question-categories', OwnerQuestionCategoriesManage::class)->name('question-categories');
    Route::get('question-sub-categories', OwnerQuestionSubCategoriesManage::class)->name('question-sub-categories');
    Route::get('reviews', OwnerReviewsManage::class)->name('reviews.index');
    Route::get('promo', OwnerPromoManage::class)->name('promo.index');
    Route::get('users', OwnerUsersManage::class)->name('users');

    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', OwnerTransactionsIndex::class)->name('index'); 
        Route::get('/detail/{type}/{id}', OwnerTransactionsDetail::class)->name('detail');
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