<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Manage Questions - {{ $tryout->title }}</h1>
                <p class="text-gray-600">Kelola pertanyaan untuk tryout ini</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                {{-- Kolom Utama (Form Soal) --}}
                <div class="lg:w-3/4">
                    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold">Soal Nomor {{ $currentQuestionNumber }}</h2>
                            <div class="flex space-x-2">
                                <button wire:click="navigateToNewQuestion" 
                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                                    Soal Baru
                                </button>
                                <button wire:click="openModal(false)" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                    List Questions
                                </button>
                            </div>
                        </div>

                        {{-- Menampilkan error validasi bawaan Livewire --}}
                        @if($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 0116 0zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-red-800 font-medium">Terdapat kesalahan dalam pengisian form:</span>
                                </div>
                                <ul class="mt-2 ml-7 text-red-700 text-sm list-disc">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Menampilkan error kustom (dari validateAnswers) --}}
                        @if($hasAnswerErrors)
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 0116 0zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-red-800 font-medium">Terdapat masalah dengan jawaban:</span>
                                </div>
                                <ul class="mt-2 ml-7 text-red-700 text-sm list-disc">
                                    @foreach($answerErrorMessages as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Info Wajib Isi --}}
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-blue-800 text-sm">Field yang ditandai dengan <span class="text-red-500">*</span> wajib diisi.</span>
                            </div>
                        </div>

                        {{-- Form Utama --}}
                        <form wire:submit.prevent="save">
                            <div class="space-y-6">
                                
                                {{-- Kategori & Sub Kategori --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="id_question_categories" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                        <select wire:model="id_question_categories" id="id_question_categories" 
                                                wire:change="loadAvailableSubCategories"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @if($this->getFieldError('id_question_categories')) border-red-300 @endif">
                                            <option value="">Pilih Kategori</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($this->getFieldError('id_question_categories'))
                                            <div class="mt-1 text-red-600 text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $this->getFieldError('id_question_categories') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <label for="id_question_sub_category" class="block text-sm font-medium text-gray-700">Sub Kategori <span class="text-red-500">*</span></label>
                                        <select wire:model="id_question_sub_category" 
                                                id="id_question_sub_category" 
                                                wire:key="subcategory-{{ $id_question_categories }}" {{-- PERBAIKAN UTAMA: Tambahkan wire:key --}}
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @if($this->getFieldError('id_question_sub_category')) border-red-300 @endif"
                                                {{ $availableSubCategories->isEmpty() ? 'disabled' : '' }}>
                                            <option value="">Pilih Sub Kategori</option>
                                            @foreach($availableSubCategories as $subCategory)
                                                <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($availableSubCategories->isEmpty() && $id_question_categories)
                                            <p class="text-xs text-gray-500 mt-1">Tidak ada sub kategori untuk kategori ini</p>
                                        @endif
                                        @if($this->getFieldError('id_question_sub_category'))
                                            <div class="mt-1 text-red-600 text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $this->getFieldError('id_question_sub_category') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Pertanyaan (TinyMCE) --}}
                                <div wire:ignore>
                                    <label for="question_editor" class="block text-sm font-medium text-gray-700">Pertanyaan <span class="text-red-500">*</span></label>
                                    <textarea id="question_editor" rows="6" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @if($this->getFieldError('question')) border-red-300 @endif"
                                              placeholder="Masukkan pertanyaan di sini...">{!! $question !!}</textarea>
                                    @if($this->getFieldError('question'))
                                        <div class="mt-1 text-red-600 text-sm flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $this->getFieldError('question') }}
                                        </div>
                                    @else
                                        <div class="mt-1 text-xs text-gray-500">
                                            Minimal 5 karakter. Karakter saat ini: {{ strlen(strip_tags($question)) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Pilihan Jawaban --}}
                                <div>
                                    <div class="flex justify-between items-center mb-3">
                                        <label class="block text-sm font-medium text-gray-700">Jawaban <span class="text-red-500">*</span></label>
                                        <button type="button" wire:click="addAnswer" 
                                                class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm hover:bg-green-200 transition-colors"
                                                {{ count($answers) >= 8 ? 'disabled' : '' }}>
                                            + Tambah Jawaban
                                        </button>
                                    </div>
                                    
                                    @foreach($answers as $index => $answer)
                                        <div class="border rounded-md p-4 mb-3 @if($this->getAnswerFieldError($index, 'answer') || $this->getAnswerFieldError($index, 'points') ) border-red-200 bg-red-50 @else bg-gray-50 @endif" wire:key="answer-{{ $index }}">
                                            <div class="flex items-start space-x-3">
                                                {{-- Huruf A, B, C --}}
                                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold mt-1">
                                                    {{ $this->getAnswerLetter($index) }}
                                                </div>
                                                
                                                <div class="flex-grow">
                                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                                        {{-- Teks Jawaban (TinyMCE) --}}
                                                        <div class="md:col-span-10">
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                                                Teks Jawaban {{ $this->getAnswerLetter($index) }} <span class="text-red-500">*</span>
                                                            </label>
                                                            
                                                            <div wire:ignore wire:key="tinymce-answer-{{ $index }}"> 
                                                                <textarea id="answer_editor_{{ $index }}" 
                                                                          rows="2"
                                                                          placeholder="Teks jawaban {{ $this->getAnswerLetter($index) }}"
                                                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @if($this->getAnswerFieldError($index, 'answer')) border-red-300 @endif">{!! $answer['answer'] !!}</textarea>
                                                            </div>
                                                            
                                                            @if($this->getAnswerFieldError($index, 'answer'))
                                                                <div class="mt-1 text-red-600 text-sm flex items-center">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    {{ $this->getAnswerFieldError($index, 'answer') }}
                                                                </div>
                                                            @else
                                                                <div class="mt-1 text-xs text-gray-500">
                                                                    Maksimal 500 karakter. Karakter saat ini: {{ strlen(strip_tags($answer['answer'])) }}
                                                                </div>
                                                            @endif
                                                        </div>

                                                        {{-- Poin Jawaban --}}
                                                        <div class="md:col-span-2">
                                                            <label class="block text-xs font-medium text-gray-600 mb-1">Poin</label>
                                                            <input type="number" 
                                                                    wire:model="answers.{{ $index }}.points"
                                                                    min="0"
                                                                    max="5"
                                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @if($this->getAnswerFieldError($index, 'points')) border-red-300 @endif"
                                                                    placeholder="0">
                                                            @if($this->getAnswerFieldError($index, 'points'))
                                                                <div class="mt-1 text-red-600 text-sm flex items-center">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    {{ $this->getAnswerFieldError($index, 'points') }}
                                                                </div>
                                                            @else
                                                                <div class="mt-1 text-xs text-gray-500">
                                                                    Min: 0, Max: 5
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- Opsi Jawaban Benar & Hapus --}}
                                                    <div class="mt-2 flex justify-between items-center">
                                                        <div class="flex items-center">
                                                            <input type="checkbox" 
                                                                    wire:model="answers.{{ $index }}.is_correct"
                                                                    wire:click="updateAnswerCorrect({{ $index }})"
                                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                            <span class="ml-2 text-sm text-gray-600">Jawaban Benar</span>
                                                        </div>
                                                        
                                                        @if(count($answers) > 1)
                                                            <button type="button" 
                                                                    wire:click="removeAnswer({{ $index }})"
                                                                    class="text-red-600 hover:text-red-800 text-sm flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                                Hapus
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Info Persyaratan Jawaban --}}
                                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 text-yellow-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="text-yellow-800 text-sm">
                                                <p class="font-medium">Persyaratan jawaban:</p>
                                                <ul class="mt-1 ml-4 list-disc">
                                                    <li>Setiap jawaban harus memiliki teks</li>
                                                    <li>Setidaknya satu jawaban harus ditandai sebagai benar</li>
                                                    <li>Tidak boleh ada jawaban yang duplikat</li>
                                                    <li>Jawaban yang ditandai benar harus memiliki teks</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Penjelasan (TinyMCE) --}}
                                <div wire:ignore> 
                                    <label for="explanation_editor" class="block text-sm font-medium text-gray-700">Penjelasan <span class="text-red-500">*</span></label>
                                    <textarea id="explanation_editor" rows="4" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @if($this->getFieldError('explanation')) border-red-300 @endif"
                                              placeholder="Penjelasan untuk jawaban yang benar... ">{!! $explanation !!}</textarea>
                                    @if($this->getFieldError('explanation'))
                                        <div class="mt-1 text-red-600 text-sm flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $this->getFieldError('explanation') }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Status Aktif --}}
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="is_active" 
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-600">Aktif</span>
                                </div>

                                {{-- Tombol Aksi Form --}}
                                <div class="flex justify-end space-x-3 pt-4 border-t">
                                    <button type="button" wire:click="resetForm" 
                                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 transition-colors">
                                        Reset Form
                                    </button>
                                    <button type="submit" 
                                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 transition-colors flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Simpan & Soal Berikutnya 
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Sidebar Kanan (Navigasi) --}}
                <div class="lg:w-1/4">
                    <div class="bg-white shadow-sm rounded-lg p-6 sticky top-6">
                        <h3 class="text-lg font-semibold mb-4">Navigasi Soal</h3>
                        
                        {{-- Navigasi Nomor Soal --}}
                        <div class="grid grid-cols-5 gap-2 mb-4">
                            
                            @for($i = 1; $i <= $totalQuestions; $i++)
                                @if($questionItem = $questionsList->get($i-1))
                                    @php
                                        $baseClasses = 'w-10 h-10 rounded border transition-colors flex items-center justify-center';
                                        $stateClasses = '';
                                
                                        if ($i == $currentQuestionNumber) {
                                            // 1. Tombol untuk soal yang sedang aktif (diedit)
                                            $stateClasses = 'bg-blue-600 text-white border-blue-600';
                                        } elseif (!$questionItem->is_active) {
                                            // 2. Tombol untuk soal yang TIDAK aktif (nonaktif)
                                            $stateClasses = 'bg-red-100 border-red-300 text-red-700 hover:bg-red-200'; 
                                        } else {
                                            // 3. Tombol untuk soal lain yang aktif (default)
                                            $stateClasses = 'bg-gray-100 border-gray-300 hover:bg-blue-500 hover:text-white';
                                        }
                                    @endphp
                                
                                    <button wire:click="navigateToQuestion({{ $questionItem->id }})"
                                            class="{{ $baseClasses }} {{ $stateClasses }}">
                                        {{ $i }}
                                    </button>
                                @endif
                            @endfor
                            
                            {{-- Tombol Soal Baru (+) --}}
                            @if($totalQuestions > 0)
                                <button wire:click="navigateToNewQuestion"
                                        class="w-10 h-10 rounded border border-dashed border-gray-400 bg-white text-gray-400 hover:border-blue-500 hover:text-blue-500 flex items-center justify-center transition-colors">
                                    <span class="text-xl">+</span>
                                </button>
                            @endif
                        </div>

                        {{-- Info Status --}}
                        <div class="text-sm text-gray-600 space-y-1">
                            <div>Total Soal: <span class="font-semibold">{{ $tryout->questions->count() }}</span></div>
                            <div>Soal Aktif: <span class="font-semibold text-green-600">{{ $tryout->activeQuestions->count() }}</span></div>
                            <div>Sedang Edit: <span class="font-semibold text-blue-600">Soal {{ $currentQuestionNumber }}</span></div>
                        </div>
                        
                        {{-- Tombol Aksi Sidebar --}}
                        <div class="mt-4 space-y-2">
                            <button wire:click="openModal(false)" 
                                    class="w-full text-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-sm transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 4a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1V5a1 1 0 00-1-1H5zm0-2a3 3 0 00-3 3v10a3 3 0 003 3h10a3 3 0 003-3V5a3 3 0 00-3-3H5z" clip-rule="evenodd"/>
                                </svg>
                                Lihat Semua Soal
                            </button>
                            
                            @if($isEdit)
                                <button wire:click="navigateToNewQuestion" 
                                        class="w-full text-center px-3 py-2 bg-green-100 text-green-700 rounded hover:bg-green-200 text-sm transition-colors flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Buat Soal Baru
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk List Soal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-4 border w-full max-w-6xl shadow-lg rounded-md bg-white">
                {{-- Modal Header --}}
                <div class="flex justify-between items-center pb-3">
                    <h3 class="text-lg font-bold">Daftar Semua Questions - {{ $tryout->title }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Filter --}}
                <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <input type="text" wire:model.live="search" placeholder="Cari pertanyaan..." 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <select wire:model.live="categoryFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach($filterCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="subCategoryFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                {{ $filterSubCategories->isEmpty() ? 'disabled' : '' }}>
                            <option value="">Semua Sub Kategori</option>
                            @foreach($filterSubCategories as $subCategory)
                                <option value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Modal Tabel Konten --}}
                <div class="max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Question</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($questions as $question)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $loop->iteration + (($questions->currentPage() - 1) * $questions->perPage()) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal">
                                        <div class="text-sm max-w-md">{!! Str::limit(strip_tags($question->question), 100) !!}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $question->category?->name ?? '-' }}
                                        @if($question->subCategory)
                                            <br><span class="text-xs text-gray-500">{{ $question->subCategory->name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $question->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                        <button wire:click="navigateToQuestion({{ $question->id }})" 
                                                class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <button onclick="confirmDelete({{ $question->id }})" 
                                                class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50 transition-colors flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Hapus
                                        </button>
                                        <button wire:click="toggleStatus({{ $question->id }})" 
                                                class="text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-50 transition-colors flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ $question->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    @if($questions->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-4 text-lg font-medium">Tidak ada soal yang ditemukan.</p>
                            <p class="text-sm">Coba ubah filter pencarian atau buat soal baru.</p>
                        </div>
                    @endif
                </div>

                {{-- Modal Pagination --}}
                <div class="px-6 py-4 border-t">
                    {{ $questions->links() }}
                </div>
            </div>
        </div>
    @endif

    {{-- Notifikasi (Toast) --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
             class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Script untuk SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fungsi global untuk konfirmasi delete
        function confirmDelete(questionId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Soal yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Panggil Livewire method langsung
                    @this.call('deleteQuestion', questionId);
                }
            });
        }

        // Inisialisasi setelah Livewire siap
        document.addEventListener('livewire:init', function() {
            // Event listener untuk event dari Livewire (backup)
            Livewire.on('show-delete-confirmation', (event) => {
                if(event.questionId) {
                    confirmDelete(event.questionId);
                } else if (event[0] && event[0].questionId) { 
                    confirmDelete(event[0].questionId);
                }
            });
        });

        // Auto close modal setelah berhasil submit di modal
        document.addEventListener('livewire:success', function() {
            if (@this.get('showModal') && document.querySelector('[x-data]')) {
                setTimeout(() => {
                    @this.call('closeModal');
                }, 2000);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Question management loaded successfully');
        });
    </script>
</div>

{{-- Script Push untuk TinyMCE (Tetap sama) --}}
@push('scripts')
<script>
    // Definisikan URL upload gambar secara global di window
    @if(Route::has('admin.tinymce.upload.image'))
        window.uploadImageUrl = "{{ route('admin.tinymce.upload.image') }}"; 
    @else
        window.uploadImageUrl = null;
        console.warn('Route "admin.tinymce.upload.image" not defined. Image upload in TinyMCE will not work.');
    @endif

    // FUNGSI CALLBACK: Berisi semua logic komunikasi Livewire (@this)
    const livewireTinyMCE = (editor, livewireProperty) => {
        // Init logic (untuk mengisi konten saat load form)
        editor.on('init', function(e) {
            if (typeof @this.get === 'function') {
                editor.setContent(@this.get(livewireProperty) || '');
            }
        });

        // Ketika konten editor berubah atau blur, kirim data kembali ke Livewire
        editor.on('change', function(e) {
            if (typeof @this.set === 'function') {
                @this.set(livewireProperty, editor.getContent());
            }
        });
        editor.on('blur', function(e) {
            if (typeof @this.set === 'function') {
                @this.set(livewireProperty, editor.getContent());
            }
        });
    };

    // FUNGSI KHUSUS: Untuk Jawaban Dinamis (answers.X.answer)
    const initAnswerEditor = (index) => {
        const selector = `textarea#answer_editor_${index}`;
        const property = `answers.${index}.answer`;
        
        // Panggil fungsi init global dengan callback Livewire
        initTinyMCE(selector, (editor) => {
            // Logic Livewire untuk Jawaban:
            editor.on('init', function(e) {
                if (typeof @this.get === 'function') {
                    const currentContent = @this.get(property) || '';
                    editor.setContent(currentContent);
                }
            });

            editor.on('change', function(e) {
                if (typeof @this.set === 'function') {
                    @this.set(property, editor.getContent());
                }
            });
            editor.on('blur', function(e) {
                if (typeof @this.set === 'function') {
                    @this.set(property, editor.getContent());
                }
            });
        });
    }

    // FUNGSI PUSAT UNTUK RE-INIT SEMUA EDITOR JAWABAN
    function reinitializeAllAnswerEditors() {
        if (typeof @this.get !== 'function') return; 

        // Hancurkan semua editor jawaban yang mungkin ada sebelumnya
        @this.get('answers').forEach((answer, index) => {
            const editor = tinymce.get(`answer_editor_${index}`);
            if (editor) {
                editor.destroy();
            }
        });
        
        // Inisialisasi ulang editor Jawaban dengan data baru
        setTimeout(() => {
            if (typeof @this.get !== 'function') return; 
            @this.get('answers').forEach((answer, index) => {
                initAnswerEditor(index);
            });
        }, 50); // Delay kecil agar DOM benar-benar siap
    }


    // Panggil inisialisasi setelah Livewire memuat DOM
    document.addEventListener('livewire:load', function () {
        // Inisialisasi editor statis
        initTinyMCE('textarea#question_editor', (editor) => livewireTinyMCE(editor, 'question'));
        initTinyMCE('textarea#explanation_editor', (editor) => livewireTinyMCE(editor, 'explanation'));
        
        // Inisialisasi editor dinamis (Jawaban)
        reinitializeAllAnswerEditors();
    });

    // PENTING: Mendengarkan event dari PHP saat data soal diubah/dimuat ulang
    document.addEventListener('init-answers', function () {
        reinitializeAllAnswerEditors();
    });

    // Event kustom dari removeAnswer()
    document.addEventListener('answers-updated', function () {
        reinitializeAllAnswerEditors();
    });

    document.addEventListener('question-loaded', function () {
        // Update Editor Pertanyaan
        const questionEditor = tinymce.get('question_editor');
        if (questionEditor && typeof @this !== 'undefined' && typeof @this.get === 'function') {
            questionEditor.setContent(@this.get('question') || '');
        } else if(questionEditor) {
            questionEditor.setContent('');
        }

        // Update Editor Penjelasan
        const explanationEditor = tinymce.get('explanation_editor');
        if (explanationEditor && typeof @this !== 'undefined' && typeof @this.get === 'function') {
            explanationEditor.setContent(@this.get('explanation') || '');
        } else if(explanationEditor) {
            explanationEditor.setContent('');
        }
        
        reinitializeAllAnswerEditors();
    });


    // PENTING: Menangani Penambahan/Navigasi Livewire
    document.addEventListener('livewire:navigated', function () {
        // Hancurkan editor statis
        if (tinymce.get('question_editor')) tinymce.get('question_editor').destroy();
        if (tinymce.get('explanation_editor')) tinymce.get('explanation_editor').destroy();

        setTimeout(() => {
            // Inisialisasi ulang statis
            initTinyMCE('textarea#question_editor', (editor) => livewireTinyMCE(editor, 'question'));
            initTinyMCE('textarea#explanation_editor', (editor) => livewireTinyMCE(editor, 'explanation'));
            
            // Inisialisasi ulang dinamis
            reinitializeAllAnswerEditors();
        }, 100);
    });

    // HOOK Khusus: Untuk penambahan item dinamis (ketika user klik + Tambah Jawaban)
    document.addEventListener('livewire:update', function() {
        if (typeof @this.get !== 'function') return; 

        // Cek editor jawaban yang belum diinisialisasi
        @this.get('answers').forEach((answer, index) => {
            if (!tinymce.get(`answer_editor_${index}`)) {
                 initAnswerEditor(index);
            }
        });
    });
</script>
@endpush