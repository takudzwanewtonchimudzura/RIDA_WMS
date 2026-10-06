<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AccessLevelTestSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment('testing') || DB::connection()->getDatabaseName() !== 'RIDA_WMS_TEST') {
            throw new RuntimeException('Access-level test accounts may only be seeded into RIDA_WMS_TEST with APP_ENV=testing.');
        }

        $accounts = [
            [
                'name' => 'TEST ONLY - Administrator',
                'email' => 'test.admin@rida.gov.zw',
                'role' => 'admin',
                'department' => 'Test Administration',
                'rights' => 'System-wide asset access and administration; create, view, update, and delete records; approve workflows; manage users, clients, invoices, suppliers, inventory, and stock controls.',
            ],
            [
                'name' => 'TEST ONLY - Operator',
                'email' => 'test.operator@rida.gov.zw',
                'role' => 'operator',
                'department' => 'Test Field Operations',
                'rights' => 'Create operational records and view/update own records; deletion is limited to own records where allowed. Can submit prospecting, tickets, stock requests, and usage reports. Cannot perform administrator approvals or access user/client/invoice/supplier administration.',
            ],
            [
                'name' => 'TEST ONLY - Viewer',
                'email' => 'test.viewer@rida.gov.zw',
                'role' => 'viewer',
                'department' => 'Test Read Only',
                'rights' => 'Read-only access to permitted asset records, inventory, and reports. Cannot create, edit, delete, approve, request/issue stock, or submit usage reports. Can submit a support ticket; ticket access remains requester-scoped.',
            ],
        ];

        $lines = [
            'RIDA RWIMS ACCESS TEST ACCOUNTS',
            'TEST ONLY. These accounts are seeded only in RIDA_WMS_TEST.',
            'Do not use these accounts or credentials in production.',
            'LOGIN URL: ' . rtrim((string) config('app.url'), '/') . '/login',
            'LOCAL ARTISAN SERVE URL: http://127.0.0.1:8000/login',
            'Passwords are randomly generated for this run. Rerun this seeder to rotate them.',
            '',
        ];

        foreach ($accounts as $account) {
            $password = bin2hex(random_bytes(18));
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($password),
                    'role' => $account['role'],
                    'department' => $account['department'],
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
            if (!Hash::check($password, $user->password)) {
                throw new RuntimeException('Generated test password did not match its stored hash.');
            }

            $lines[] = 'LEVEL: ' . strtoupper($account['role']);
            $lines[] = 'NAME: ' . $account['name'];
            $lines[] = 'EMAIL: ' . $account['email'];
            $lines[] = 'PASSWORD: ' . $password;
            $lines[] = 'RIGHTS: ' . $account['rights'];
            $lines[] = '';
        }

        Storage::disk('local')->put('access-test-credentials.txt', implode(PHP_EOL, $lines) . PHP_EOL);
        $this->command?->info('Three test accounts seeded in RIDA_WMS_TEST. Private credentials file: storage/app/private/access-test-credentials.txt');
    }
}
