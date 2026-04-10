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
        Schema::create('company_members', function (Blueprint $table) {
            $table->id('company_member_id');
            $table->foreignId('user_id')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete()
                ->comment('유저 id');
            $table->foreignId('company_id')->constrained('companies', 'company_id')->comment('업체 id');
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments', 'department_id')
                ->comment('부서 id');
            $table->foreignId('position_id')
                ->nullable()
                ->constrained('codes', 'code_id')
                ->comment('직급 | codes.group_key=POSITION');
            $table->foreignId('role_id')->nullable()->constrained('roles', 'role_id')->nullOnDelete()->comment('개별 권한 role | null이면 member_type 기본 role 사용');
            $table->boolean('is_primary')->default(false)->comment('대표회사 여부 (1:대표, 0:일반)');
            $table->text('memo')->nullable()->comment('메모');
            $table->dateTime('join_date')->nullable()->comment('입사일자');
            $table->dateTime('leave_date')->nullable()->comment('퇴사일자');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'company_id'], 'uq_company_members_user_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_members');
    }
};