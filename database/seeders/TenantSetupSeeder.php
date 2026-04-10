<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantSetupSeeder extends Seeder
{
    public function run(): void
    {
        // 필요한 code_id 조회
        $activeUserStatus = DB::table('codes')->where('group_key', 'USER_STATUS')->where('code_key', 'ACTIVE')->value('code_id');
        $activeCompanyStatus = DB::table('codes')->where('group_key', 'COMPANY_STATUS')->where('code_key', 'ACTIVE')->value('code_id');
        $internalCompanyType = DB::table('codes')->where('group_key', 'COMPANY_TYPE')->where('code_key', 'INTERNAL')->value('code_id');
        $adminMemberType = DB::table('codes')->where('group_key', 'MEMBER_TYPE')->where('code_key', 'ADMIN')->value('code_id');

        // 관리자 role 생성
        $roleId = DB::table('roles')->insertGetId([
            'role_name' => '관리자',
            'member_type_id' => $adminMemberType,
            'sidebar_permissions' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 관리자 계정 생성
        $userId = DB::table('users')->insertGetId([
            'name' => '관리자',
            'is_admin' => true,
            'login_id' => 'admin',
            'password' => Hash::make('123456'),
            'status_id' => $activeUserStatus,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 운영사 등록 (company_id = 1)
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => '운영사',
            'ceo_name' => '관리자',
            'company_type_id' => $internalCompanyType,
            'status_id' => $activeCompanyStatus,
            'is_active' => true,
            'created_by' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 관리자를 운영사 직원으로 등록
        DB::table('company_members')->insert([
            'user_id' => $userId,
            'company_id' => $companyId,
            'member_type_id' => $adminMemberType,
            'role_id' => $roleId,
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
