<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->handle(Illuminate\Http\Request::capture());

$payload = [['questionId' => 1, 'answerId' => null, 'isDoubtful' => false]];
$component = app(\App\Livewire\Customers\TryoutWorksheet::class);

$component->userTryoutId = 1;
$component->userId = 1;
$component->questionsJson = json_encode([['id' => 1]]);
try {
    $res = $component->autoSave($payload);
    echo "OK: " . ($res ? 'true' : 'false') . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
