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
        Schema::create('department_schedules', function (Blueprint $table) {
            $table->id('department_schedule_id');
            $table->foreignId('company_id')->constrained('companies', 'company_id')->comment('업체 id');
            $table->foreignId('type_id')
                ->nullable()
                ->constrained('codes', 'code_id')
                ->comment('부서 필터 | codes.group_key={DEPT_CODE}_FILTER | ex) DEVELOP_FILTER, SALES_FILTER');
            $table->foreignId('department_id')->constrained('departments', 'department_id')->comment('부서 id');
            $table->string('title', 200)->comment('제목');
            $table->text('content')->nullable()->comment('내용');
            $table->dateTime('start_datetime')->comment('시작일시');
            $table->dateTime('end_datetime')->nullable()->comment('종료일시');
            $table->boolean('is_all_day')->default(false)->comment('종일여부');
            $table->foreignId('writer_id')->constrained('users', 'user_id')->comment('작성자');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['department_id', 'start_datetime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_schedules');
    }
};
