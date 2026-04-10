<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CodeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            // 회원 상태
            ['group_key' => 'USER_STATUS', 'code_key' => 'ACTIVE',    'name' => '활성',   'sort' => 1],
            ['group_key' => 'USER_STATUS', 'code_key' => 'INACTIVE',  'name' => '비활성', 'sort' => 2],
            ['group_key' => 'USER_STATUS', 'code_key' => 'SUSPENDED', 'name' => '차단',   'sort' => 3],
            ['group_key' => 'USER_STATUS', 'code_key' => 'WITHDRAWN', 'name' => '탈퇴',   'sort' => 4],

            // 회원 승인 상태
            ['group_key' => 'USER_APPROVAL_STATUS', 'code_key' => 'APPROVED', 'name' => '승인', 'sort' => 1],
            ['group_key' => 'USER_APPROVAL_STATUS', 'code_key' => 'REJECTED', 'name' => '반려', 'sort' => 2],

            // 업체 상태
            ['group_key' => 'COMPANY_STATUS', 'code_key' => 'ACTIVE',   'name' => '운영', 'sort' => 1],
            ['group_key' => 'COMPANY_STATUS', 'code_key' => 'INACTIVE', 'name' => '휴업',   'sort' => 2],
            ['group_key' => 'COMPANY_STATUS', 'code_key' => 'CLOSED',   'name' => '폐업',   'sort' => 3],

            // 업체 유형
            ['group_key' => 'COMPANY_TYPE', 'code_key' => 'INTERNAL',   'name' => '자사',     'sort' => 1],
            ['group_key' => 'COMPANY_TYPE', 'code_key' => 'PARTNER',    'name' => '협력사',   'sort' => 2],
            ['group_key' => 'COMPANY_TYPE', 'code_key' => 'CONSULTANT', 'name' => '컨설팅사', 'sort' => 3],

            // 회원 유형
            ['group_key' => 'MEMBER_TYPE', 'code_key' => 'EMPLOYEE',   'name' => '사내직원',  'sort' => 1],
            ['group_key' => 'MEMBER_TYPE', 'code_key' => 'COMPANY',    'name' => '기업담당자', 'sort' => 2],
            ['group_key' => 'MEMBER_TYPE', 'code_key' => 'CONSULTANT', 'name' => '컨설턴트',  'sort' => 3],

            // 직급
            ['group_key' => 'POSITION', 'code_key' => 'STAFF',     'name' => '사원', 'sort' => 1],
            ['group_key' => 'POSITION', 'code_key' => 'SENIOR',    'name' => '주임', 'sort' => 2],
            ['group_key' => 'POSITION', 'code_key' => 'ASSISTANT', 'name' => '대리', 'sort' => 3],
            ['group_key' => 'POSITION', 'code_key' => 'MANAGER',   'name' => '과장', 'sort' => 4],
            ['group_key' => 'POSITION', 'code_key' => 'DEPUTY',    'name' => '차장', 'sort' => 5],
            ['group_key' => 'POSITION', 'code_key' => 'GENERAL',   'name' => '부장', 'sort' => 6],
            ['group_key' => 'POSITION', 'code_key' => 'DIRECTOR',  'name' => '이사', 'sort' => 7],
            ['group_key' => 'POSITION', 'code_key' => 'CEO',       'name' => '대표', 'sort' => 8],

            // 일정 유형
            ['group_key' => 'SCHEDULE_TYPE', 'code_key' => 'PERSONAL',    'name' => '개인',       'sort' => 1],
            ['group_key' => 'SCHEDULE_TYPE', 'code_key' => 'SHARED',      'name' => '공유',       'sort' => 2],
            ['group_key' => 'SCHEDULE_TYPE', 'code_key' => 'VISIT',       'name' => '기업방문',   'sort' => 3],
            ['group_key' => 'SCHEDULE_TYPE', 'code_key' => 'APPLICATION', 'name' => '사업접수',   'sort' => 4],
            ['group_key' => 'SCHEDULE_TYPE', 'code_key' => 'ETC',         'name' => '기타',       'sort' => 5],

            // 휴가 유형
            ['group_key' => 'LEAVE_TYPE', 'code_key' => 'ANNUAL',    'name' => '연차',   'sort' => 1],
            ['group_key' => 'LEAVE_TYPE', 'code_key' => 'HALF_AM',   'name' => '오전반차', 'sort' => 2],
            ['group_key' => 'LEAVE_TYPE', 'code_key' => 'HALF_PM',   'name' => '오후반차', 'sort' => 3],
            ['group_key' => 'LEAVE_TYPE', 'code_key' => 'SICK',      'name' => '병가',   'sort' => 4],
            ['group_key' => 'LEAVE_TYPE', 'code_key' => 'SPECIAL',   'name' => '특별휴가', 'sort' => 5],

            // 휴가 신청 상태
            ['group_key' => 'LEAVE_STATUS', 'code_key' => 'PENDING',   'name' => '대기',   'sort' => 1],
            ['group_key' => 'LEAVE_STATUS', 'code_key' => 'APPROVED',  'name' => '승인',   'sort' => 2],
            ['group_key' => 'LEAVE_STATUS', 'code_key' => 'REJECTED',  'name' => '반려',   'sort' => 3],
            ['group_key' => 'LEAVE_STATUS', 'code_key' => 'CANCELLED', 'name' => '취소',   'sort' => 4],

            // 결재 상태
            ['group_key' => 'LEAVE_APPROVAL_STATUS', 'code_key' => 'PENDING',  'name' => '대기', 'sort' => 1],
            ['group_key' => 'LEAVE_APPROVAL_STATUS', 'code_key' => 'APPROVED', 'name' => '승인', 'sort' => 2],
            ['group_key' => 'LEAVE_APPROVAL_STATUS', 'code_key' => 'REJECTED', 'name' => '반려', 'sort' => 3],

            // 부서별 필터 (기존 시스템 기준 — 실제 운영에 맞게 수정 필요)
            ['group_key' => 'DEVELOP_FILTER',   'code_key' => 'SMART_FACTORY', 'name' => '스마트공장', 'sort' => 1],
            ['group_key' => 'DEVELOP_FILTER',   'code_key' => 'SMART_WORKSHOP', 'name' => '스마트공방', 'sort' => 2],
            ['group_key' => 'DEVELOP_FILTER',   'code_key' => 'ROBOT_AI',      'name' => '로봇/AI',   'sort' => 3],
            ['group_key' => 'DEVELOP_FILTER',   'code_key' => 'ETC',           'name' => '기타',      'sort' => 4],

            ['group_key' => 'SALES_FILTER',     'code_key' => 'SMART_FACTORY', 'name' => '스마트공장', 'sort' => 1],
            ['group_key' => 'SALES_FILTER',     'code_key' => 'SMART_WORKSHOP', 'name' => '스마트공방', 'sort' => 2],
            ['group_key' => 'SALES_FILTER',     'code_key' => 'ROBOT_AI',      'name' => '로봇/AI',   'sort' => 3],
            ['group_key' => 'SALES_FILTER',     'code_key' => 'ETC',           'name' => '기타',      'sort' => 4],

            ['group_key' => 'MARKETING_FILTER', 'code_key' => 'SOFTWARE',      'name' => '소프트웨어',    'sort' => 1],
            ['group_key' => 'MARKETING_FILTER', 'code_key' => 'GOV_SUPPORT',   'name' => '정부지원사업', 'sort' => 2],
            ['group_key' => 'MARKETING_FILTER', 'code_key' => 'HOSPITAL',      'name' => '병원 마케팅',  'sort' => 3],
            ['group_key' => 'MARKETING_FILTER', 'code_key' => 'DESIGN',        'name' => '디자인',      'sort' => 4],

            ['group_key' => 'TECH_FILTER',      'code_key' => 'COMPANY',       'name' => '업체지원', 'sort' => 1],
            ['group_key' => 'TECH_FILTER',      'code_key' => 'EVALUATION',    'name' => '평가지원', 'sort' => 2],
            ['group_key' => 'TECH_FILTER',      'code_key' => 'SALES',         'name' => '영업지원', 'sort' => 3],
            ['group_key' => 'TECH_FILTER',      'code_key' => 'PLAN',          'name' => '기획지원', 'sort' => 4],
            ['group_key' => 'TECH_FILTER',      'code_key' => 'TECH',          'name' => '기술지원', 'sort' => 5],

            ['group_key' => 'PLAN_FILTER',      'code_key' => 'SMART_FACTORY', 'name' => '스마트공장', 'sort' => 1],
            ['group_key' => 'PLAN_FILTER',      'code_key' => 'SMART_WORKSHOP', 'name' => '스마트공방', 'sort' => 2],
            ['group_key' => 'PLAN_FILTER',      'code_key' => 'ROBOT_AI',      'name' => '로봇/AI',   'sort' => 3],
            ['group_key' => 'PLAN_FILTER',      'code_key' => 'ETC',           'name' => '기타',      'sort' => 4],
        ];

        foreach ($codes as $code) {
            DB::table('codes')->insertOrIgnore([
                ...$code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
