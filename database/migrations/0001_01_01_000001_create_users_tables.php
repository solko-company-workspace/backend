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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->boolean('is_admin')->default(false)->comment('관리자 유무');
            $table->string('name')->comment('이름');
            $table->string('login_id')->unique()->comment('아이디');
            $table->string('password')->comment('비밀번호');
            $table->rememberToken();
            $table->string('phone', 20)->comment('전화번호');
            $table->string('email', 100)->nullable()->unique()->comment('이메일');
            $table->string('address', 500)->nullable()->comment('주소');
            $table->date('birth')->nullable()->comment('생일');
            $table->foreignId('member_type_id')->constrained('codes', 'code_id')->comment('회원 유형 | codes.group_key=MEMBER_TYPE');
            $table->boolean('is_approved')->default(false)->comment('관리자 승인 여부');
            $table->timestamps();
            $table->softDeletes();
        });

        // 회원 승인 이력
        Schema::create('user_approval_logs', function (Blueprint $table) {
            $table->id('user_approval_log_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete()->comment('승인 대상 유저');
            $table->foreignId('processed_by')->constrained('users', 'user_id')->comment('처리한 관리자');
            $table->foreignId('status_id')->constrained('codes', 'code_id')->comment('처리 상태 | codes.group_key=USER_APPROVAL_STATUS');
            $table->string('reason', 500)->nullable()->comment('처리 사유');
            $table->timestamp('created_at');

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_approval_logs');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
