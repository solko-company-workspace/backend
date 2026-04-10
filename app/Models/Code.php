<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    protected $primaryKey = 'code_id';

    protected $fillable = ['group_key', 'code_key', 'name', 'description', 'sort', 'is_active'];

    // group_key + code_key 로 code_id 반환
    public static function getId(string $groupKey, string $codeKey): ?int
    {
        return static::where('group_key', $groupKey)
            ->where('code_key', $codeKey)
            ->value('code_id');
    }

    // group_key 전체 목록 반환
    public static function getGroup(string $groupKey): \Illuminate\Support\Collection
    {
        return static::where('group_key', $groupKey)
            ->where('is_active', true)
            ->orderBy('sort')
            ->get(['code_id', 'code_key', 'name']);
    }
}
