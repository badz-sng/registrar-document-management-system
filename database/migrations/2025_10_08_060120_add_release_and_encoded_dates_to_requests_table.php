<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dateTime('encoded_at')->nullable()->after('status');
            $table->date('estimated_release_date')->nullable()->after('encoded_at');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['encoded_at', 'estimated_release_date']);
        });
    }
};
