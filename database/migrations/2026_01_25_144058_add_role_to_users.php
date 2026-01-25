<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('email_verified_at');
            });
        }

        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('student')->after('is_admin');
            });
        }

        DB::table('users')
            ->where('is_admin', true)
            ->update(['role' => 'admin']);

        if (Schema::hasTable('classrooms')) {
            $lecturerIds = DB::table('classrooms')
                ->whereNotNull('lecturer_id')
                ->distinct()
                ->pluck('lecturer_id');

            if ($lecturerIds->isNotEmpty()) {
                DB::table('users')
                    ->whereIn('id', $lecturerIds)
                    ->where('role', '!=', 'admin')
                    ->update(['role' => 'lecturer']);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
