<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustryCodeMappingSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/import/industry_code_mappings.csv');

        if (! file_exists($path)) {
            $this->command->error("파일 없음: {$path}");

            return;
        }

        $file = fopen($path, 'r');

        if ($file === false) {
            $this->command->error("파일 열기 실패: {$path}");

            return;
        }

        $header = fgetcsv($file);

        if ($header === false) {
            $this->command->error('헤더를 읽을 수 없습니다.');
            fclose($file);

            return;
        }

        $bom = "\xEF\xBB\xBF";
        $header[0] = ltrim($header[0], $bom);

        $chunk = [];
        $now = now();
        $count = 0;
        $line = 1;

        while (($row = fgetcsv($file)) !== false) {
            $line++;

            if (count($header) !== count($row)) {
                $this->command->warn("{$line}행 스킵: 컬럼 수 불일치");

                continue;
            }

            $data = array_combine($header, $row);

            $chunk[] = [
                'row_no' => $data['row_no'] !== '' ? (int) $data['row_no'] : null,
                'tax_office_code' => $data['tax_office_code'] ?: null,
                'tax_main_code' => $data['tax_main_code'] ?: null,
                'tax_main_name' => $data['tax_main_name'] ?: null,
                'tax_mid_code' => $data['tax_mid_code'] ?: null,
                'tax_mid_name' => $data['tax_mid_name'] ?: null,
                'tax_sub_code' => $data['tax_sub_code'] ?: null,
                'tax_sub_name' => $data['tax_sub_name'] ?: null,
                'tax_detail_code' => $data['tax_detail_code'] ?: null,
                'tax_detail_name' => $data['tax_detail_name'] ?: null,
                'tax_subdetail_name' => $data['tax_subdetail_name'] ?: null,
                'ksic_code' => $data['ksic_code'] ?: null,
                'ksic_main_code' => $data['ksic_main_code'] ?: null,
                'ksic_main_name' => $data['ksic_main_name'] ?: null,
                'ksic_mid_code' => $data['ksic_mid_code'] ?: null,
                'ksic_mid_name' => $data['ksic_mid_name'] ?: null,
                'ksic_sub_code' => $data['ksic_sub_code'] ?: null,
                'ksic_sub_name' => $data['ksic_sub_name'] ?: null,
                'ksic_detail_code' => $data['ksic_detail_code'] ?: null,
                'ksic_detail_name' => $data['ksic_detail_name'] ?: null,
                'ksic_subdetail_name' => $data['ksic_subdetail_name'] ?: null,
                'description' => $data['description'] ?: null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($file);

        DB::transaction(function () use ($chunk, &$count) {
            DB::table('industry_code_mappings')->delete();
            DB::statement('ALTER TABLE industry_code_mappings AUTO_INCREMENT = 1');

            foreach (array_chunk($chunk, 500) as $batch) {
                DB::table('industry_code_mappings')->insert($batch);
                $count += count($batch);
            }
        });

        $this->command->info("완료: {$count}건 삽입");
    }
}
