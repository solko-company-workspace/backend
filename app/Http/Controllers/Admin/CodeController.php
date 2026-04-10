<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminMessage;
use App\Constants\Message;
use App\Http\Controllers\Controller;
use App\Models\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    // 그룹 목록
    public function groups(): JsonResponse
    {
        $groups = Code::select('group_key')
            ->distinct()
            ->orderBy('group_key')
            ->pluck('group_key');

        return $this->success(data: $groups);
    }

    // 그룹별 코드 목록
    public function index(string $groupKey): JsonResponse
    {
        $codes = Code::where('group_key', $groupKey)
            ->orderBy('sort')
            ->get(['code_id', 'code_key', 'name', 'description', 'sort', 'is_active']);

        return $this->success(data: $codes);
    }

    // 추가
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'group_key'   => 'required|string|max:50',
            'code_key'    => 'required|string|max:50',
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort'        => 'nullable|integer',
        ]);

        $exists = Code::where('group_key', $request->group_key)
            ->where('code_key', $request->code_key)
            ->exists();

        if ($exists) {
            return $this->failure(message: AdminMessage::CODE_ALREADY_EXISTS, status: 409);
        }

        $code = Code::create([
            'group_key'   => $request->group_key,
            'code_key'    => $request->code_key,
            'name'        => $request->name,
            'description' => $request->description,
            'sort'        => $request->sort ?? 0,
            'is_active'   => true,
        ]);

        return $this->success(data: $code, status: 201);
    }

    // 수정
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name'        => 'sometimes|string|max:100',
            'description' => 'sometimes|nullable|string',
            'sort'        => 'sometimes|integer',
            'is_active'   => 'sometimes|boolean',
        ]);

        $code = Code::findOrFail($id);
        $code->update($request->only(['name', 'description', 'sort', 'is_active']));

        return $this->success(data: $code);
    }

    // 활성 토글
    public function toggle(int $id): JsonResponse
    {
        $code = Code::findOrFail($id);
        $code->update(['is_active' => ! $code->is_active]);

        $message = $code->is_active ? Message::ACTIVATED : Message::DEACTIVATED;

        return $this->success(message: $message);
    }
}
