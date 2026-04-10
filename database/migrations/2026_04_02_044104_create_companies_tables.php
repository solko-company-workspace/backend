<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id('company_id');
            $table->string('company_name', 200)->comment('업체명');
            $table->string('business_number', 20)->nullable()->comment('사업자번호');
            $table->string('ceo_name', 50)->comment('대표명');
            $table->string('contact', 30)->comment('전화번호');
            $table->string('email', 100)->nullable()->comment('이메일');
            $table->string('address', 500)->nullable()->comment('업체 주소');
            $table->date('established_date')->nullable()->comment('개업일자');
            $table->unsignedSmallInteger('employee_count')->nullable()->comment('상시근로자수');
            $table->string('ksic_code', 10)
                ->nullable()->comment('표준산업분류코드 | ex) 33910');
            $table->string('tax_office_code', 10)
                ->nullable()->comment('국세청 업종 코드 | ex) 369503');
            $table->text('main_items')->nullable()->comment('주생산품');
            $table->text('memo')->nullable()->comment('비고');
            $table->foreignId('company_type_id')
                ->constrained('codes', 'code_id')
                ->comment('업체유형 | codes.group_key=COMPANY_TYPE');
            $table->foreignId('status_id')
                ->constrained('codes', 'code_id')
                ->comment('업체상태 | codes.group_key=COMPANY_STATUS');
            $table->boolean('is_active')->default(true)->comment('업체 활성화 유무');
            $table->foreignId('created_by')->constrained('users', 'user_id')->comment('등록자');
            $table->timestamps();
            $table->softDeletes();
        });

        // 업체 연도별 재무현황
        Schema::create('company_finances', function (Blueprint $table) {
            $table->id('company_financial_id');
            $table->foreignId('company_id')->constrained('companies', 'company_id')->cascadeOnDelete()->comment('업체 id');
            $table->year('year')->comment('사업연도');
            $table->unsignedBigInteger('sales')->default(0)->comment('매출액 (원)');
            $table->bigInteger('sales_profit')->default(0)->comment('영업이익 (원)');
            $table->decimal('sales_profit_rate', 6, 2)->default(0)->comment('영업이익률 (%)');
            $table->unsignedBigInteger('exports')->nullable()->comment('수출액 (원)');
            $table->decimal('exports_rate', 6, 2)->default(0)->comment('수출비중 (%)');
            $table->unsignedBigInteger('capital')->default(0)->comment('자본총계 (원)');
            $table->unsignedBigInteger('debt')->default(0)->comment('부채총계 (원)');
            $table->decimal('debt_rate', 7, 2)->default(0)->comment('부채비율 (%)');
            $table->foreignId('created_by')->constrained('users', 'user_id')->comment('등록자');
            $table->timestamps();
            $table->unique(['company_id', 'year'], 'uq_company_finances_company_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_finances');
        Schema::dropIfExists('companies');
    }
};