<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // role 기본 정보
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id');
            $table->string('role_name', 100)->comment('권한 이름 | ex) 사내직원_영업팀장, 외부업체_기본');
            $table->timestamps();
        });

        // role별 메뉴 권한
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id('role_permission_id');
            $table->foreignId('role_id')->constrained('roles', 'role_id')->cascadeOnDelete()->comment('role id');
            $table->string('menu_path', 200)->comment('메뉴 경로 | ex) /intra-company/registration');
            $table->boolean('can_read')->default(false)->comment('조회');
            $table->boolean('can_create')->default(false)->comment('등록');
            $table->boolean('can_edit')->default(false)->comment('수정');
            $table->boolean('can_delete')->default(false)->comment('삭제');
            $table->timestamps();

            $table->unique(['role_id', 'menu_path'], 'uq_role_permissions_role_menu');
        });

        // 외부회원 - 회원유형별 기본 role (가입/승인 시 자동 적용)
        Schema::create('member_type_roles', function (Blueprint $table) {
            $table->foreignId('member_type_id')
                ->constrained('codes', 'code_id')
                ->cascadeOnDelete()
                ->comment('회원 유형 | codes.group_key=MEMBER_TYPE');
            $table->foreignId('role_id')->constrained('roles', 'role_id')->cascadeOnDelete()->comment('role id');
            $table->timestamps();

            $table->primary('member_type_id');
        });

        // 사내직원 - 부서+직급별 기본 role
        Schema::create('department_position_roles', function (Blueprint $table) {
            $table->foreignId('department_id')->constrained('departments', 'department_id')->cascadeOnDelete()->comment('부서 id');
            $table->foreignId('position_id')
                ->constrained('codes', 'code_id')
                ->cascadeOnDelete()
                ->comment('직급 | codes.group_key=POSITION');
            $table->foreignId('role_id')->constrained('roles', 'role_id')->cascadeOnDelete()->comment('role id');
            $table->timestamps();

            $table->primary(['department_id', 'position_id']);
        });

        // 개인 커스텀 권한 (기본 role에서 특정 메뉴만 override)
        Schema::create('user_custom_permissions', function (Blueprint $table) {
            $table->id('user_custom_permission_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete()->comment('유저 id');
            $table->foreignId('company_member_id')->constrained('company_members', 'company_member_id')->cascadeOnDelete()->comment('대표회사 기준 company_member id');
            $table->string('menu_path', 200)->comment('메뉴 경로 | ex) /intra-company/registration');
            $table->boolean('can_read')->default(false)->comment('조회');
            $table->boolean('can_create')->default(false)->comment('등록');
            $table->boolean('can_edit')->default(false)->comment('수정');
            $table->boolean('can_delete')->default(false)->comment('삭제');
            $table->timestamps();

            $table->unique(['company_member_id', 'menu_path'], 'uq_user_custom_permissions_member_menu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_custom_permissions');
        Schema::dropIfExists('department_position_roles');
        Schema::dropIfExists('member_type_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
    }
};
