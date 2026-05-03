<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->after('transaction_code');
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id');
            $table->text('midtrans_snap_token')->nullable()->after('midtrans_transaction_id');
            $table->text('midtrans_redirect_url')->nullable()->after('midtrans_snap_token');
            $table->string('midtrans_payment_type', 50)->nullable()->after('midtrans_redirect_url');
            $table->string('midtrans_status_code', 20)->nullable()->after('midtrans_payment_type');
            $table->json('midtrans_payload')->nullable()->after('midtrans_status_code');

            $table->index('midtrans_order_id');
            $table->index('midtrans_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['midtrans_order_id']);
            $table->dropIndex(['midtrans_transaction_id']);

            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_transaction_id',
                'midtrans_snap_token',
                'midtrans_redirect_url',
                'midtrans_payment_type',
                'midtrans_status_code',
                'midtrans_payload',
            ]);
        });
    }
};

