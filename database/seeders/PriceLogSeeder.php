<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PriceLog;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PriceLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereIn('role', ['admin', 'gestor'])->first();
        $adminId = $admin ? $admin->id : 1;

        $items = [
            [
                'item_type' => 'training',
                'new_price' => 50000.0,
                'reason' => 'Precio inicial del sistema para Iris Training',
            ],
            [
                'item_type' => 'passport_management',
                'new_price' => 2500.0,
                'reason' => 'Precio inicial del sistema para Gestión de Pasaporte',
            ],
            [
                'item_type' => 'vip_transfer',
                'new_price' => 1000.0,
                'reason' => 'Precio inicial del sistema para Transfer VIP',
            ],
            [
                'item_type' => 'refund_insurance',
                'new_price' => 10.0,
                'reason' => 'Porcentaje inicial del sistema para Seguro de Reembolso (10%)',
            ],
            [
                'item_type' => 'crew_expense_per_au',
                'new_price' => 12.0,
                'reason' => 'Gasto inicial por tripulante por AU',
            ],
            [
                'item_type' => 'hours_per_au',
                'new_price' => 48.0,
                'reason' => 'Horas de vuelo equivalentes por AU',
            ],
        ];

        foreach ($items as $item) {
            if (!PriceLog::where('item_type', $item['item_type'])->exists()) {
                PriceLog::create([
                    'item_type' => $item['item_type'],
                    'item_id' => 0,
                    'old_price' => 0,
                    'new_price' => $item['new_price'],
                    'reason' => $item['reason'],
                    'admin_id' => $adminId,
                ]);
            }
        }
    }
}
