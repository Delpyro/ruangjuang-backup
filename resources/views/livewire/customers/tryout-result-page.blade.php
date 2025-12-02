<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12 mt-20">
    <div class="container mx-auto px-4">

        @if ($hasReviewed)
            <div id="results-wrapper" wire:key="results-unlocked">
                
                <div class="text-center mb-10" data-aos="fade-up">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3 leading-tight">
                        Hasil Ujian: {{ $tryout->title }}
                    </h1>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Terima kasih atas ulasan Anda! Berikut adalah riwayat pengerjaan Anda.
                    </p>
                </div>

                <div class="max-w-3xl mx-auto space-y-10">

                    @forelse ($resultsData as $result)
                        <div wire:key="result-attempt-{{ $result['attempt'] }}" 
                             class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 md:p-6" 
                             data-aos="fade-up" data-aos-delay="100">

                            <div class="mb-5 pb-5 border-b border-gray-200">
                                <h2 class="text-2xl font-bold text-gray-900">
                                    Percobaan Ke-{{ $result['attempt'] }}
                                </h2>
                                <p class="text-sm text-gray-500">
                                    {{-- [FIX] Pakai variabel string matang dari Controller --}}
                                    Selesai pada: {{ $result['tanggal_fix'] }} WIB
                                </p>
                            </div>

                            <div class="flex flex-col md:flex-row gap-5 md:gap-6 items-center">
                        
                                <div class="flex-shrink-0 flex flex-col items-center justify-center w-36 h-36 bg-blue-50 border-4 border-blue-200 rounded-full text-blue-600">
                                    <span class="text-sm font-medium">Skor Total</span>
                                    <span class="text-5xl font-extrabold">{{ number_format($result['finalScore'], 0, ',', '.') }}</span>
                                </div>
                                
                                <div class="flex-1 grid grid-cols-3 gap-3 w-full">
                                    <div class="bg-green-50 border border-green-100 rounded-lg p-3 text-center">
                                        <span class="text-sm text-green-700 block">Benar</span>
                                        <span class="text-2xl font-bold text-green-600 block mt-1">{{ $result['totalCorrect'] }}</span>
                                    </div>
                                    <div class="bg-red-50 border border-red-100 rounded-lg p-3 text-center">
                                        <span class="text-sm text-red-700 block">Salah</span>
                                        <span class="text-2xl font-bold text-red-600 block mt-1">{{ $result['totalWrong'] }}</span>
                                    </div>
                                    <div class="bg-gray-100 border border-gray-200 rounded-lg p-3 text-center">
                                        <span class="text-sm text-gray-600 block">Kosong</span>
                                        <span class="text-2xl font-bold text-gray-900 block mt-1">{{ $result['totalUnanswered'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8">
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">
                                    Rincian Skor (Percobaan {{ $result['attempt'] }})
                                </h4>
                                <div class="space-y-4">
                                    @forelse ($result['categoryScores'] as $category)
                                        <div>
                                            <div class="flex justify-between items-baseline mb-1">
                                                <span class="font-semibold text-gray-700">{{ $category['name'] }}</span>
                                                <span class="text-lg font-bold text-blue-600">{{ number_format($category['skor_kategori'], 0, ',', '.') }}</span>
                                            </div>
                                            
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $category['percentage'] ?? 0 }}%"></div>
                                            </div>

                                            <div class="flex justify-between text-xs text-gray-500 mt-1.5">
                                                <span>Benar: <strong class="text-green-600">{{ $category['benar'] }}</strong></span>
                                                <span>Salah: <strong class="text-red-600">{{ $category['salah'] }}</strong></span>
                                                <span>Kosong: <strong class="text-gray-700">{{ $category['kosong'] }}</strong></span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-sm italic">Tidak ada rincian kategori.</p>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                    @empty
                        <p class="text-center text-gray-500">Gagal memuat data hasil.</p>
                    @endforelse

                    {{-- KOTAK SKOR MAKSIMUM & PASSING GRADE --}}
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-5 md:p-6" data-aos="fade-up" data-aos-delay="200">
                        <h4 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-medal text-yellow-500 mr-2"></i> Rincian Skor SKD 
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Skor Maksimal</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Passing Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $skdScores = [
                                            ['name' => 'TWK', 'max' => 150, 'pg' => 65],
                                            ['name' => 'TIU', 'max' => 175, 'pg' => 80],
                                            ['name' => 'TKP', 'max' => 225, 'pg' => 166],
                                        ];
                                        $totalMax = array_sum(array_column($skdScores, 'max'));
                                        $totalPg = array_sum(array_column($skdScores, 'pg'));
                                    @endphp

                                    @foreach ($skdScores as $item)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['name'] }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-center text-sm text-blue-600 font-semibold">{{ $item['max'] }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-center text-sm text-green-600 font-semibold">{{ $item['pg'] }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-blue-50/50 font-bold border-t border-blue-200">
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">Total</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-center text-sm text-blue-700">{{ $totalMax }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-center text-sm text-green-700">{{ $totalPg }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- END KOTAK SKOR --}}

                    <div class="mt-10 pt-8 border-t border-gray-200 flex flex-col sm:flex-row gap-3 justify-center" data-aos="fade-up" data-aos-delay="200">
                        <button 
                            wire:click="goToDiscussion"
                            class="w-full sm:w-auto flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition-all duration-300 text-base">
                            <i class="fas fa-search mr-2"></i>
                            Lihat Pembahasan
                        </button>

                        <a href="{{ route('tryout.my-tryouts') }}"
                           class="w-full sm:w-auto flex items-center justify-center bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-semibold py-3 px-6 rounded-lg shadow-sm transition-all duration-300 text-base"
                           wire:navigate>
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Daftar Tryout
                        </a>
                    </div>

                </div>
            </div>

        @else
            <div wire:key="placeholder-locked" 
                 class="max-w-lg mx-auto bg-white rounded-xl shadow-lg border border-gray-100 p-8 text-center"
                 data-aos="fade-up">
                <i class="fas fa-lock text-4xl text-gray-400 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-900 mb-3">Satu Langkah Lagi...</h2>
                <p class="text-gray-600">
                    Hasil ujian dan pembahasan Anda terkunci. Harap berikan ulasan singkat Anda untuk membukanya.
                </p>
                <p class="text-sm text-gray-500 mt-4">(Modal review akan muncul secara otomatis)</p>
            </div>
        @endif

    </div>

    {{-- MODAL REVIEW (Tetap sama seperti sebelumnya) --}}
    <div
    x-data="{ show: @entangle('showReviewModal') }"
    x-show="show"
    x-on:keydown.escape.window.prevent=""
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4 backdrop-blur-sm"
    style="display: none;"
    x-init="if (show) document.body.style.overflow = 'hidden';"
    x-effect="if (show) document.body.style.overflow = 'hidden'; else document.body.style.overflow = '';"
    >
        <div
            x-show="show"
            x-on:click.outside.prevent x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-gray-200"
        >
            <div class="bg-gray-50 px-6 py-5 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-star text-yellow-400 mr-3"></i>
                    Berikan Ulasan Anda
                </h2>
                <p class="text-sm text-gray-600 mt-1">Bagaimana pengalaman Anda mengerjakan tryout ini?</p>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="font-semibold text-gray-700 block mb-2">Rating Anda</label>
                    <div class="flex space-x-2">
                        @foreach (range(1, 5) as $star)
                            <button wire:click.prevent="setRating({{ $star }})" class="focus:outline-none">
                                <i class="fas fa-star text-3xl transition-colors duration-150 
                                    {{ $reviewRating >= $star ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300' }}">
                                </i>
                            </button>
                        @endforeach
                    </div>
                    @error('reviewRating') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="reviewText" class="font-semibold text-gray-700 block mb-2">Ulasan Anda</label>
                    <textarea 
                        id="reviewText"
                        wire:model="reviewText"
                        rows="4"
                        placeholder="Tuliskan ulasan Anda di sini (minimal 10 karakter)..."
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                    @error('reviewText') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                <button
                    type="button"
                    wire:click="submitReview"
                    wire:loading.attr="disabled"
                    class="w-full px-5 py-3 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 focus:outline-none disabled:opacity-75 inline-flex items-center justify-center">
                    <span wire:loading.remove wire:target="submitReview">
                        Kirim Ulasan & Lihat Hasil
                    </span>
                    <span wire:loading wire:target="submitReview" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mengirim...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>