<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminMessage;
use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // user_approval_logs insert에 사용

class UserController extends Controller
{
    // 전체 유저 목록 (미승인 상단 정렬, status 필터 가능)
    public function index(Request $request): JsonResponse
    {
        $query = User::select(['user_id', 'name', 'login_id', 'phone', 'email', 'member_type_id', 'is_approved', 'created_at']);

        if ($request->status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($request->status === 'approved') {
            $query->where('is_approved', true);
        }

        $users = $query->orderBy('is_approved')  // 미승인(0) 먼저
            ->orderBy('name')
            ->get();

        return $this->success(data: $users);
    }

    // 승인
    public function approve(Request $request, int $id): JsonResponse
    {
        $statusId = Code::getId('USER_APPROVAL_STATUS', 'APPROVED');

        $done = DB::transaction(function () use ($id, $request, $statusId) {
            $user = User::where('user_id', $id)->lockForUpdate()->firstOrFail();

            if ($user->is_approved) {
                return false;
            }

            $user->update(['is_approved' => true]);

            DB::table('user_approval_logs')->insert([
                'user_id'      => $user->user_id,
                'processed_by' => $request->user()->user_id,
                'status_id'    => $statusId,
                'reason'       => null,
                'created_at'   => now(),
            ]);

            return true;
        });

        if (! $done) {
            return $this->failure(message: AdminMessage::USER_ALREADY_APPROVED, status: 409);
        }

        return $this->success(message: AdminMessage::USER_APPROVE_SUCCESS);
    }

    // 거절
    public function reject(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $statusId = Code::getId('USER_APPROVAL_STATUS', 'REJECTED');

        $done = DB::transaction(function () use ($id, $request, $statusId) {
            $user = User::where('user_id', $id)->lockForUpdate()->firstOrFail();

            if ($user->is_approved) {
                return false;
            }

            DB::table('user_approval_logs')->insert([
                'user_id'      => $user->user_id,
                'processed_by' => $request->user()->user_id,
                'status_id'    => $statusId,
                'reason'       => $request->reason,
                'created_at'   => now(),
            ]);

            return true;
        });

        if (! $done) {
            return $this->failure(message: AdminMessage::USER_ALREADY_APPROVED, status: 409);
        }

        return $this->success(message: AdminMessage::USER_REJECT_SUCCESS);
    }
}