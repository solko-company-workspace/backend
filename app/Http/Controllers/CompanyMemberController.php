<?php

namespace App\Http\Controllers;

use App\Constants\Message;
use App\Models\CompanyMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyMemberController extends Controller
{
    private const ALLOWED_SORTS = ['company_name', 'user_name'];

    // 전체 멤버 목록
    public function index(Request $request): JsonResponse
    {
        $query = CompanyMember::query()
            ->select(
                'company_members.*',
                'users.name as user_name',
                'users.email as user_email',
                'users.phone as user_phone',
                'companies.company_name',
                'departments.name as department_name',
                'codes.name as position_name',
            )
            ->join('users', 'users.user_id', '=', 'company_members.user_id')
            ->join('companies', 'companies.company_id', '=', 'company_members.company_id')
            ->leftJoin('departments', 'departments.department_id', '=', 'company_members.department_id')
            ->leftJoin('codes', 'codes.code_id', '=', 'company_members.position_id');

        if ($request->company_id) {
            $query->where('company_members.company_id', (int) $request->company_id);
        }
        if ($request->user_id) {
            $query->where('company_members.user_id', (int) $request->user_id);
        }

        $sort = in_array($request->sort, self::ALLOWED_SORTS) ? $request->sort : 'company_name';
        $secondSort = $sort === 'company_name' ? 'user_name' : 'company_name';
        $query->orderBy($sort)->orderBy($secondSort);

        return $this->success(data: $query->get());
    }

    // 멤버 등록
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'company_id' => 'required|integer|exists:companies,company_id',
            'user_id' => 'required|integer|exists:users,user_id',
            'department_id' => 'nullable|integer|exists:departments,department_id',
            'position_id' => 'nullable|integer|exists:codes,code_id',
            'role_id' => 'nullable|integer|exists:roles,role_id',
            'is_primary' => 'boolean',
            'memo' => 'nullable|string',
            'join_date' => 'nullable|date',
            'leave_date' => 'nullable|date|after_or_equal:join_date',
        ]);

        $data = [
            'company_id' => $request->company_id,
            'user_id' => $request->user_id,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'role_id' => $request->role_id,
            'is_primary' => $request->boolean('is_primary', false),
            'memo' => $request->memo,
            'join_date' => $request->join_date,
            'leave_date' => $request->leave_date,
        ];

        $member = DB::transaction(function () use ($data) {
            $existing = CompanyMember::withTrashed()
                ->where('company_id', $data['company_id'])
                ->where('user_id', $data['user_id'])
                ->lockForUpdate()
                ->first();

            if ($existing?->trashed()) {
                $existing->restore();
                $existing->update($data);

                return $existing;
            }

            if ($existing) {
                return null; // 중복
            }

            return CompanyMember::create($data);
        });

        if ($member === null) {
            return $this->failure(message: Message::DUPLICATE, status: 409);
        }

        return $this->success(data: $member, status: 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'department_id' => 'nullable|integer|exists:departments,department_id',
            'position_id' => 'nullable|integer|exists:codes,code_id',
            'role_id' => 'nullable|integer|exists:roles,role_id',
            'is_primary' => 'boolean',
            'memo' => 'nullable|string',
            'join_date' => 'nullable|date',
            'leave_date' => 'nullable|date|after_or_equal:join_date',
        ]);

        $company_member = CompanyMember::findOrFail($id);

        $company_member->update($request->only([
            'department_id',
            'position_id',
            'role_id',
            'is_primary',
            'memo',
            'join_date',
            'leave_date',
        ]));

        return $this->success(data: $company_member);
    }

    public function delete(int $id): JsonResponse
    {
        $company_member = CompanyMember::findOrFail($id);

        $company_member->delete();

        return $this->success(message: Message::DELETED);
    }
}
