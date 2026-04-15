<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id('area_id');
            $table->string('district_code', 10)->unique()->comment('법정동코드');
            $table->string('name', 100)->comment('지역명 | ex) 서울특별시, 강남구');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('areas', 'area_id')
                ->nullOnDelete()
                ->comment('상위 지역 ID | null이면 시도(최상위)');
            $table->unsignedTinyInteger('depth')->default(1)->comment('계층 깊이 | 1:시도 2:시군구');
            $table->boolean('is_active')->default(true)->comment('사용 여부');
            $table->timestamps();
        });

        // 담당자 - 담당 지역 다대다 (담당자 1명이 복수 지역 담당 가능)
        Schema::create('user_areas', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnDelete()->comment('담당자 ID');
            $table->foreignId('area_id')->constrained('areas', 'area_id')->cascadeOnDelete()->comment('담당 지역 ID');

            $table->primary(['user_id', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_areas');
        Schema::dropIfExists('areas');
    }
};
