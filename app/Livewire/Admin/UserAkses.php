<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\UserTryout; // Pastikan model ini ada
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class UserAkses extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $user;
    public $userId;
    public $confirmingReset = false;
    public $tryoutToReset = null;

    public function mount($id)
    {
        $this->userId = $id;
        $this->user = User::findOrFail($id);
    }

    public function confirmReset($userTryoutId)
    {
        $this->tryoutToReset = $userTryoutId;
        $this->confirmingReset = true;
    }

    public function cancelReset()
    {
        $this->confirmingReset = false;
        $this->tryoutToReset = null;
    }

    public function resetAkses()
    {
        if (!$this->tryoutToReset) return;

        DB::beginTransaction();
        try {
            $userTryout = UserTryout::findOrFail($this->tryoutToReset);

            // 1. Hapus semua jawaban user pada attempt ini
            DB::table('users_answers')->where('user_tryout_id', $userTryout->id)->delete();

            // 2. Hapus skor per kategori pada attempt ini
            DB::table('tryout_category_scores')->where('user_tryout_id', $userTryout->id)->delete();

            // 3. Hapus ranking user untuk tryout ini (opsional, agar bersih sampai dia selesai lagi)
            DB::table('rankings')
                ->where('id_user', $userTryout->id_user)
                ->where('tryout_id', $userTryout->tryout_id)
                ->delete();

            // 4. Reset status pengerjaan kembali seperti belum dikerjakan
            $userTryout->update([
                'started_at' => null,
                'ended_at' => null,
                'is_completed' => 0,
            ]);

            DB::commit();

            $this->confirmingReset = false;
            $this->tryoutToReset = null;
            
            session()->flash('success', 'Akses tryout (Attempt ke-' . $userTryout->attempt . ') berhasil di-reset. User bisa mengerjakannya kembali.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal mereset akses: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Mengambil data akses tryout user beserta detail tryout-nya
        // Pastikan model UserTryout memiliki relasi belongsTo ke Tryout dengan nama function 'tryout'
        $aksesTryouts = UserTryout::with('tryout')
            ->where('id_user', $this->userId)
            ->orderBy('tryout_id')
            ->orderBy('attempt')
            ->paginate(10);

        return view('livewire.admin.user-akses', [
            'aksesTryouts' => $aksesTryouts
        ])->layout('layouts.admin');
    }
}