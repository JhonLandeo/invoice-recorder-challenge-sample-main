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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('currency_id')->nullable()->after('total_amount');
            
            $table->unsignedBigInteger('type_document_id')->nullable()->after('currency_id');
            
            $table->string('series', 4)->after('type_document_id');
            $table->string('correlative', 8)->after('series');

            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('type_document_id')->references('id')->on('type_documents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropForeign(['type_document_id']);
            
            $table->dropColumn(['currency_id', 'type_document_id']);
            $table->dropColumn(['series', 'correlative']);
        });
    }
};
