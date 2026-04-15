<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('areas')->truncate();

        $path = storage_path('app/import/korea_legal_district_codes_20250807.csv');
        $file = fopen($path, 'r');

        // BOM 제거
        $bom = fread($file, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($file);
        }

        // 헤더 skip
        fgetcsv($file);

        $sidoMap = [];    // 시도명 => area_id
        $sidoRows = [];
        $sigunguRows = [];

        while (($row = fgetcsv($file)) !== false) {
            [, $sido, $sigungu, $eupmyeondong, , $sort, , $deletedAt] = array_pad($row, 8, '');

            // 삭제된 코드 제외
            if (!empty($deletedAt)) {
                continue;
            }

            // 시도만 (시군구명 없음)
            if (empty($sigungu)) {
                $sidoRows[$sido] = [
                    'name'      => $sido,
                    'parent_id' => null,
                    'depth'     => 1,
                    'sort'      => (int) $sort,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 시군구만 (시군구명 있고 읍면동명 없음)
            if (!empty($sigungu) && empty($eupmyeondong)) {
                $sigunguRows[] = [
                    'sido'      => $sido,
                    'name'      => $sigungu,
                    'depth'     => 2,
                    'sort'      => (int) $sort,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        fclose($file);

        // 시도 insert
        foreach ($sidoRows as $sido => $data) {
            $id = DB::table('areas')->insertGetId($data);
            $sidoMap[$sido] = $id;
        }

        // 시군구 insert (chunk)
        $insert = [];
        foreach ($sigunguRows as $row) {
            $sido = $row['sido'];
            unset($row['sido']);
            $row['parent_id'] = $sidoMap[$sido] ?? null;
            $insert[] = $row;

            if (count($insert) >= 500) {
                DB::table('areas')->insert($insert);
                $insert = [];
            }
        }

        if (!empty($insert)) {
            DB::table('areas')->insert($insert);
        }
    }
}
