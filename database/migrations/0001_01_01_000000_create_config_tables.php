<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 공통 코드
        Schema::create('codes', function (Blueprint $table) {
            $table->id('code_id');
            $table->unsignedTinyInteger('sort')->default(0)->comment('정렬순위');
            $table->string('group_key', 50)->comment('코드 그룹');
            $table->string('code_key', 50)->comment('코드 키');
            $table->string('name', 100)->comment('표시 이름');
            $table->text('description')->nullable()->comment('코드 설명');
            $table->boolean('is_active')->default(true)->comment('코드 사용 여부');
            $table->timestamps();
            $table->unique(['group_key', 'code_key'], 'uq_codes_group_code');
        });

        // 부서
        Schema::create('departments', function (Blueprint $table) {
            $table->id('department_id');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('departments', 'department_id')
                ->nullOnDelete()
                ->comment('상위 부서 ID');
            $table->string('name', 100)->comment('부서명');
            $table->string('code', 50)->unique()->comment('부서코드 | ex) DEVELOP, SALES');
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('정렬순서');
            $table->boolean('is_active')->default(true)->comment('사용여부');
            $table->timestamps();
            $table->softDeletes();
        });

        // 국세청 업종코드-산업코드분류 연계표
        Schema::create('industry_code_mappings', function (Blueprint $table) {
            $table->bigIncrements('industry_code_id');

            // 원본 순서
            $table->unsignedInteger('row_no')->nullable()->comment('CSV 일련번호');

            // 국세청 업종코드 영역
            $table->string('tax_office_code', 20)->nullable()->comment('2021년 귀속 국세청 업종코드');
            $table->string('tax_main_code', 10)->nullable()->comment('국세청 대분류 코드');
            $table->string('tax_main_name', 255)->nullable()->comment('국세청 대분류명');
            $table->string('tax_mid_code', 10)->nullable()->comment('국세청 중분류 코드');
            $table->string('tax_mid_name', 255)->nullable()->comment('국세청 중분류명');
            $table->string('tax_sub_code', 10)->nullable()->comment('국세청 소분류 코드');
            $table->string('tax_sub_name', 255)->nullable()->comment('국세청 소분류명');
            $table->string('tax_detail_code', 10)->nullable()->comment('국세청 세분류 코드');
            $table->string('tax_detail_name', 255)->nullable()->comment('국세청 세분류명');
            $table->string('tax_subdetail_name', 255)->nullable()->comment('국세청 세세분류명');

            // 표준산업분류 영역
            $table->string('ksic_code', 20)->nullable()->comment('표준산업분류 코드');
            $table->string('ksic_main_code', 10)->nullable()->comment('표준산업분류 대분류 코드');
            $table->string('ksic_main_name', 255)->nullable()->comment('표준산업분류 대분류명');
            $table->string('ksic_mid_code', 10)->nullable()->comment('표준산업분류 중분류 코드');
            $table->string('ksic_mid_name', 255)->nullable()->comment('표준산업분류 중분류명');
            $table->string('ksic_sub_code', 10)->nullable()->comment('표준산업분류 소분류 코드');
            $table->string('ksic_sub_name', 255)->nullable()->comment('표준산업분류 소분류명');
            $table->string('ksic_detail_code', 10)->nullable()->comment('표준산업분류 세분류 코드');
            $table->string('ksic_detail_name', 255)->nullable()->comment('표준산업분류 세분류명');
            $table->string('ksic_subdetail_name', 255)->nullable()->comment('표준산업분류 세세분류명');

            // 기타
            $table->text('description')->nullable()->comment('세부설명');

            $table->timestamps();

            $table->index('row_no');
            $table->index('tax_office_code');
            $table->index('ksic_code');
            $table->unique(['tax_office_code', 'ksic_code'], 'uq_industry_code_mappings_tax_ksic');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('codes');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('industry_code_mappings');
    }
};
