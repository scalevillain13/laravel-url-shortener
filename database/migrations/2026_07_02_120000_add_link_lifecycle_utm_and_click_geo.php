<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('code');
            $table->timestamp('expires_at')->nullable()->after('is_active');
            $table->string('utm_source')->nullable()->after('expires_at');
            $table->string('utm_medium')->nullable()->after('utm_source');
            $table->string('utm_campaign')->nullable()->after('utm_medium');
        });

        Schema::table('clicks', function (Blueprint $table) {
            $table->string('country', 2)->nullable()->after('user_agent');
            $table->string('city')->nullable()->after('country');
            $table->boolean('is_bot')->default(false)->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'expires_at', 'utm_source', 'utm_medium', 'utm_campaign']);
        });

        Schema::table('clicks', function (Blueprint $table) {
            $table->dropColumn(['country', 'city', 'is_bot']);
        });
    }
};
