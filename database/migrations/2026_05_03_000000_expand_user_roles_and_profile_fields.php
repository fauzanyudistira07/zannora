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
            $table->string('employee_id', 50)->nullable()->unique()->after('role');
            $table->string('department', 120)->nullable()->after('employee_id');
            $table->string('job_title', 120)->nullable()->after('department');
            $table->timestamp('last_login_at')->nullable()->after('job_title');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','customer','manager','user') NOT NULL DEFAULT 'customer'");
            DB::table('users')->where('role', 'user')->update(['role' => 'customer']);
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','customer','manager') NOT NULL DEFAULT 'customer'");
        } else {
            DB::table('users')->where('role', 'user')->update(['role' => 'customer']);
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','staff','customer','manager','user') NOT NULL DEFAULT 'user'");
        }

        DB::table('users')->where('role', 'customer')->update(['role' => 'user']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','user') NOT NULL DEFAULT 'user'");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_id',
                'department',
                'job_title',
                'last_login_at',
            ]);
        });
    }
};
