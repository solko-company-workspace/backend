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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id('leave_request_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('leave_type_code_id')->nullable()->constrained('codes', 'code_id')->comment('휴가유형 | codes.group_key=LEAVE_TYPE');
            $table->text('reason')->nullable()->comment('신청사유');
            $table->dateTime('start_datetime')->comment('시작일시');
            $table->dateTime('end_datetime')->comment('종료일시');
            $table->boolean('is_all_day')->default(true)->comment('종일여부');
            $table->decimal('days_count', 4, 1)->default(1.0)->comment('사용일수');
            $table->foreignId('status_code_id')->nullable()->constrained('codes', 'code_id')->comment('신청상태 | codes.group_key=LEAVE_STATUS');
            $table->foreignId('current_approver_id')->nullable()->constrained('users', 'user_id')->comment('현재 결재자');
            $table->dateTime('final_approved_at')->nullable()->comment('최종승인일시');
            $table->dateTime('rejected_at')->nullable()->comment('반려일시');
            $table->dateTime('cancelled_at')->nullable()->comment('취소일시');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'start_datetime']);
        });

        Schema::create('leave_approvals', function (Blueprint $table) {
            $table->id('leave_approval_id');
            $table->foreignId('leave_request_id')->constrained('leave_requests', 'leave_request_id');
            $table->foreignId('approver_id')->constrained('users', 'user_id')->comment('결재자');
            $table->unsignedTinyInteger('step_order')->default(1)->comment('결재순서');
            $table->foreignId('action_status_code_id')->nullable()->constrained('codes', 'code_id')->comment('결재상태 | codes.group_key=LEAVE_APPROVAL_STATUS');
            $table->text('comment')->nullable()->comment('결재의견');
            $table->dateTime('acted_at')->nullable()->comment('처리일시');
            $table->timestamps();

            $table->index(['leave_request_id', 'step_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_approvals');
        Schema::dropIfExists('leave_requests');
    }
};
