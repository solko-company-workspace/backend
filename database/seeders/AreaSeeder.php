<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $rows = $this->parseCsv(storage_path('app/import/korea_legal_district_codes_20250807.csv'));
        [$sidoRows, $sigunguRows] = $this->classifyRows($rows);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            DB::transaction(function () use ($sidoRows, $sigunguRows) {
                DB::table('areas')->delete();
                DB::statement('ALTER TABLE areas AUTO_INCREMENT = 1');
                $this->persistAreas($sidoRows, $sigunguRows);
            });
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function parseCsv(string $path): array
    {
        if (! file_exists($path)) {
            $this->command->error("파일 없음: {$path}");

            return [];
        }

        $file = fopen($path, 'r');

        if ($file === false) {
            $this->command->error("파일 열기 실패: {$path}");

            return [];
        }

        // BOM 제거
        $bom = fread($file, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($file);
        }

        // 헤더 skip
        fgetcsv($file);

        $rows = [];
        while (($row = fgetcsv($file)) !== false) {
            $rows[] = $row;
        }

        fclose($file);

        return $rows;
    }

    private function classifyRows(array $rows): array
    {
        $sidoRows = [];
        $sigunguRows = [];

        foreach ($rows as $row) {
            [$code, $sido, $sigungu, $eupmyeondong, , , , $deletedAt] = array_pad($row, 9, '');

            // 삭제된 코드 제외
            if (! empty($deletedAt)) {
                continue;
            }

            // 시도만 (시군구명 없음)
            if (empty($sigungu)) {
                $sidoRows[$sido] = [
                    'district_code' => $code,
                    'name' => $sido,
                    'parent_id' => null,
                    'depth' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // 시군구만 (시군구명 있고 읍면동명 없음)
            } elseif (empty($eupmyeondong)) {
                $sigunguRows[] = [
                    'sido' => $sido,
                    'district_code' => $code,
                    'name' => $sigungu,
                    'depth' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        return [$sidoRows, $sigunguRows];
    }

    private function persistAreas(array $sidoRows, array $sigunguRows): void
    {
        // 시도 insert
        $sidoMap = [];
        foreach ($sidoRows as $sido => $data) {
            $sidoMap[$sido] = DB::table('areas')->insertGetId($data);
        }

        // 시군구 insert (chunk)
        $insert = [];
        foreach ($sigunguRows as $row) {
            $sido = $row['sido'];
            unset($row['sido']);

            if (! isset($sidoMap[$sido])) {
                throw new \RuntimeException("시도를 찾을 수 없습니다: {$sido}");
            }

            $row['parent_id'] = $sidoMap[$sido];
            $insert[] = $row;

            if (count($insert) >= 500) {
                DB::table('areas')->insert($insert);
                $insert = [];
            }
        }

        if (! empty($insert)) {
            DB::table('areas')->insert($insert);
        }
    }
}
