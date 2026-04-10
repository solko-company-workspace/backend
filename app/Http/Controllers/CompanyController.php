<?php

namespace App\Http\Controllers\Admin;

use App\Constants\AdminMessage;
use App\Constants\Message;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // 업체 목록
    public function companies(Request $request): JsonResponse
    {
        $query = Company::query()
            ->orderBy('company_name')
            ->orderBy('business_number');

        if ($request->status) {
            $query->where('status_id', (int) $request->status);
        }
        if ($request->company_type) {
            $query->where('company_type_id', (int) $request->company_type);
        }
        if ($request->company_name) {
            $query->whereLike('company_name', $request->company_name);
        }

        $companies = $query->get();

        return $this->success(data: $companies);
    }

    // 추가
    public function store(Request $request): JsonResponse
    {
        $request->merge([
            'business_number' => str_replace('-', '', $request->business_number),
        ]);

        $request->validate([
            'company_name' => 'required|string',
            'business_number' => 'required|string|size:10',
            'ceo_name' => 'required|string',
            'contact' => 'required|string',
            'email' => 'nullable|string',
            'address' => 'nullable|string',
            'establish_date' => 'nullable|date',
            'employee_count' => 'nullable|integer',
            'tax_office_code' => 'nullable|string',
            'ksic_code' => 'nullable|string',
            'main_items' => 'nullable|string',
            'memo' => 'nullable|string',
            'company_type_id' => 'required|integer',
            'status_id' => 'required|integer',
            'created_by' => 'required|string',
        ]);

        $company = Company::create([
            'company_name' => $request->company_name,
            'business_number' => $request->business_number,
            'ceo_name' => $request->ceo_name,
            'contact' => $request->contact,
            'email' => $request->email,
            'address' => $request->address,
            'establish_date' => $request->establish_date,
            'employee_count' => $request->employee_count,
            'tax_office_code' => $request->tax_office_code,
            'ksic_code' => $request->ksic_code,
            'main_items' => $request->main_items,
            'memo' => $request->memo,
            'company_type_id' => $request->company_type_id,
            'status_id' => $request->status_id,
            'created_by' => $request->created_by,
        ]);

        return $this->success(data: $company, status: 201);
    }

    // 수정
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'company_name' => 'required|string',
            'business_number' => 'required|string|size:10',
            'ceo_name' => 'required|string',
            'contact' => 'required|string',
            'email' => 'nullable|string',
            'address' => 'nullable|string',
            'establish_date' => 'nullable|date',
            'employee_count' => 'nullable|integer',
            'tax_office_code' => 'nullable|string',
            'ksic_code' => 'nullable|string',
            'main_items' => 'nullable|string',
            'memo' => 'nullable|string',
            'company_type_id' => 'required|integer',
            'status_id' => 'required|integer',
        ]);

        $company = Company::findOrFail($id);
        $company->update($request->only([
            'company_name',
            'business_number',
            'ceo_name',
            'contact',
            'email',
            'address',
            'establish_date',
            'employee_count',
            'tax_office_code',
            'ksic_code',
            'main_items',
            'memo',
            'company_type_id',
            'status_id',
        ]));

        return $this->success(data: $company);
    }

    // 활성 토글
    public function toggle(int $id): JsonResponse
    {
        $company = Company::findOrFail($id);
        $company->update(['is_active' => ! $company->is_active]);

        $message = $company->is_active ? Message::ACTIVATED : Message::DEACTIVATED;

        return $this->success(message: $message);
    }
}
