<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('activity_log_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->comment('행위자');
            $table->string('action', 20)->comment('행위 | CREATED, UPDATED, DELETED');
            $table->string('target_type', 50)->comment('대상 모델 | companies, users, company_members ...');
            $table->unsignedBigInteger('target_id')->comment('대상 레코드 ID');
            $table->string('description')->nullable()->comment('설명 | ex) 업체명 변경');
            $table->json('payload')->nullable()->comment('변경 전후 데이터');
            $table->timestamp('created_at');

            $table->index(['target_type', 'target_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
