<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('password_version')->default(0);
            $table->boolean('must_change_password')->default(false);
            $table->timestamp('temporary_password_expires_at')->nullable();
        });
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('values');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['password_version', 'must_change_password', 'temporary_password_expires_at']);
        });
    }
};
