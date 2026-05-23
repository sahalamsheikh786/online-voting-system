<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_known_district_name')->nullable()->after('district_id');
        });

        if (DB::getDriverName() === 'sqlite') {
            DB::table('users')
                ->join('districts', 'districts.id', '=', 'users.district_id')
                ->select('users.id as user_id', 'districts.name as district_name')
                ->get()
                ->each(function ($row) {
                    DB::table('users')
                        ->where('id', $row->user_id)
                        ->update(['last_known_district_name' => $row->district_name]);
                });

            return;
        }

        DB::statement('
            UPDATE users
            INNER JOIN districts ON districts.id = users.district_id
            SET users.last_known_district_name = districts.name
            WHERE users.district_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_known_district_name');
        });
    }
};
