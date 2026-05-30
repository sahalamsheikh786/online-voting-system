<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'last_known_district_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('last_known_district_name')->nullable()->after('district_id');
            });
        }

        DB::table('users')
            ->join('districts', 'districts.id', '=', 'users.district_id')
            ->select('users.id as user_id', 'districts.name as district_name')
            ->get()
            ->each(function ($row) {
                DB::table('users')
                    ->where('id', $row->user_id)
                    ->update(['last_known_district_name' => $row->district_name]);
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'last_known_district_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_known_district_name');
            });
        }
    }
};
