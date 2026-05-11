<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RefundRequest;

$lastRequests = RefundRequest::orderBy('id', 'desc')->take(5)->get();

foreach ($lastRequests as $req) {
    echo "ID: {$req->id}, Amount: {$req->refund_amount}, Status: {$req->status}, Reservation ID: {$req->reservation_id}\n";
}
