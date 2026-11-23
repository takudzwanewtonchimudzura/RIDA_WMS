<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DemoUserSeeder extends Seeder
{
    /**
     * Seeds a realistic set of demo users so dependent seeders (boreholes, dams,
     * schemes, etc.) always have creators/approvers to attach to.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('DemoUserSeeder may not be run in production.');
        }

        $password = env('DEMO_USER_PASSWORD') ?: bin2hex(random_bytes(9));

        $accounts = [
            ['name' => 'Tendai Moyo', 'email' => 'tendai.moyo@rida.gov.zw', 'role' => 'supervisor', 'department' => 'Provincial Engineering'],
            ['name' => 'Farai Chikwanha', 'email' => 'farai.chikwanha@rida.gov.zw', 'role' => 'operator', 'department' => 'Field Operations'],
            ['name' => 'Rutendo Sibanda', 'email' => 'rutendo.sibanda@rida.gov.zw', 'role' => 'operator', 'department' => 'Drilling'],
            ['name' => 'Kudakwashe Ndlovu', 'email' => 'kudakwashe.ndlovu@rida.gov.zw', 'role' => 'operator', 'department' => 'Field Operations'],
            ['name' => 'Chiedza Mutasa', 'email' => 'chiedza.mutasa@rida.gov.zw', 'role' => 'viewer', 'department' => 'Monitoring & Evaluation'],
            ['name' => 'Tapiwa Gumbo', 'email' => 'tapiwa.gumbo@rida.gov.zw', 'role' => 'finance', 'department' => 'Finance'],
            ['name' => 'Rudo Dube', 'email' => 'rudo.dube@rida.gov.zw', 'role' => 'stores', 'department' => 'Stores'],
            ['name' => 'Nyasha Banda', 'email' => 'nyasha.banda@rida.gov.zw', 'role' => 'supervisor', 'department' => 'Provincial Engineering'],
            ['name' => 'Ruvimbo Makoni', 'email' => 'ruvimbo.makoni@rida.gov.zw', 'role' => 'finance', 'department' => 'Finance'],
        ];

        $lines = [
            'RIDA RWIMS DEMO ACCOUNTS',
            'For local/demo use only. Do not reuse these credentials in production.',
            'Passwords are randomly generated per run unless DEMO_USER_PASSWORD is set in .env.',
            '',
        ];

        foreach ($accounts as $account) {
            $user = User::firstOrCreate(
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

            $lines[] = 'NAME: ' . $user->name;
            $lines[] = 'EMAIL: ' . $user->email;
            $lines[] = 'ROLE: ' . ucfirst($user->role);
            $lines[] = 'PASSWORD: ' . ($user->wasRecentlyCreated ? $password : '(unchanged; use existing password)');
            $lines[] = '';
        }

        Storage::disk('local')->append('demo-user-credentials.txt', implode(PHP_EOL, $lines) . PHP_EOL);
        $this->command?->info('Demo users prepared; existing passwords preserved. Credentials are in private storage.');
    }
}
