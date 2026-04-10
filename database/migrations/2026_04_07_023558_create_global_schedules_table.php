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
        Schema::create('global_schedules', function (Blueprint $table) {
            $table->id('global_schedule_id');
            $table->foreignId('company_id')->constrained('companies', 'company_id')->comment('업체 id');
            $table->foreignId('type_id')
                ->nullable()
                ->constrained('codes', 'code_id')
                ->comment('일정 유형 | codes.group_key=SCHEDULE_TYPE | 개인/공유/기업방문/사업접수 등');
            $table->foreignId('department_id')->constrained('departments', 'department_id');
            $table->string('title', 200)->comment('제목');
            $table->text('content')->nullable()->comment('내용');
            $table->dateTime('start_datetime')->comment('시작일시');
            $table->dateTime('end_datetime')->nullable()->comment('종료일시');
            $table->boolean('is_all_day')->default(false)->comment('종일여부');
            $table->text('attendee')->nullable()->comment('참석자');
            $table->foreignId('writer_id')->constrained('users', 'user_id')->comment('작성자');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['start_datetime', 'end_datetime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_schedules');
    }
};
