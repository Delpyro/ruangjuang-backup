<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UserTryout;
use App\Models\Tryout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StartTryoutModal extends Component
{
    public $showModal = false;
    public $tryout;
    public $userTryout;
    public $remainingTime = 0;

    protected $listeners = ['openStartTryoutModal' => 'openModal'];

    public function mount($tryout = null)
    {
        $this->tryout = $tryout;
    }

    public function openModal($tryoutId)
    {
        $this->tryout = Tryout::with('userTryout')->find($tryoutId);
        
        if (!$this->tryout) {
            $this->dispatch('showToast', ['type' => 'error', 'message' => 'Tryout tidak ditemukan']);
            return;
        }

        $this->userTryout = $this->tryout->userTryout;
        $this->checkLocalStorage();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['tryout', 'userTryout', 'remainingTime']);
    }

    public function checkLocalStorage()
    {
        if ($this->userTryout && $this->userTryout->started_at && !$this->userTryout->ended_at) {
            $localData = $this->getLocalStorageData();
            
            if ($localData && isset($localData['ended_at'])) {
                $endedAt = \Carbon\Carbon::parse($localData['ended_at']);
                $this->remainingTime = now()->diffInSeconds($endedAt, false);
                
                if ($this->remainingTime <= 0) {
                    $this->autoCompleteTryout();
                }
            }
        }
    }

    private function getLocalStorageData()
    {
        try {
            $data = json_decode(request()->cookie('tryout_' . $this->tryout->id), true);
            return $data ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function startTryout()
    {
        try {
            DB::transaction(function () {
                $userTryout = UserTryout::where('id_user', auth()->id())
                    ->where('tryout_id', $this->tryout->id)
                    ->first();

                if (!$userTryout) {
                    throw new \Exception('Data tryout tidak ditemukan');
                }

                // Set start and end time
                $startedAt = now();
                $endedAt = $startedAt->copy()->addMinutes($this->tryout->duration);

                $userTryout->update([
                    'started_at' => $startedAt,
                    'ended_at' => $endedAt
                ]);

                // Save to local storage
                $this->setLocalStorageData($startedAt, $endedAt);

                $this->dispatch('tryoutStarted', [
                    'tryout_id' => $this->tryout->id,
                    'started_at' => $startedAt->toISOString(),
                    'ended_at' => $endedAt->toISOString()
                ]);

                $this->dispatch('showToast', [
                    'type' => 'success', 
                    'message' => 'Tryout berhasil dimulai!'
                ]);
            });

            $this->closeModal();
            
            // Redirect to tryout page
            return redirect()->route('tryout.working', ['tryout' => $this->tryout->slug]);

        } catch (\Exception $e) {
            Log::error('Error starting tryout: ' . $e->getMessage());
            $this->dispatch('showToast', [
                'type' => 'error', 
                'message' => 'Gagal memulai tryout: ' . $e->getMessage()
            ]);
        }
    }

    private function setLocalStorageData($startedAt, $endedAt)
    {
        $data = [
            'started_at' => $startedAt->toISOString(),
            'ended_at' => $endedAt->toISOString(),
            'tryout_id' => $this->tryout->id,
            'user_id' => auth()->id(),
            'last_updated' => now()->toISOString()
        ];

        // Set cookie for 7 days
        cookie()->queue('tryout_' . $this->tryout->id, json_encode($data), 60 * 24 * 7);
    }

    private function autoCompleteTryout()
    {
        try {
            $userTryout = UserTryout::where('id_user', auth()->id())
                ->where('tryout_id', $this->tryout->id)
                ->first();

            if ($userTryout) {
                $userTryout->update(['ended_at' => now()]);
                $this->clearLocalStorageData();
            }
        } catch (\Exception $e) {
            Log::error('Error auto completing tryout: ' . $e->getMessage());
        }
    }

    private function clearLocalStorageData()
    {
        cookie()->queue(cookie()->forget('tryout_' . $this->tryout->id));
    }

    public function render()
    {
        return view('livewire.customers.start-tryout-modal');
    }
}