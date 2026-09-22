<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->handle(Illuminate\Http\Request::capture());
$res = \DB::table('answers')->first();
echo "TYPE: " . gettype($res) . "\n";
if (is_object($res)) { echo "CLASS: " . get_class($res) . "\n"; }
