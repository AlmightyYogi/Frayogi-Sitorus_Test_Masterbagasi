<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        return Voucher::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:vouchers',
            'expires_at' => 'required|date',
        ]);

        $voucher = Voucher::create($request->all());

        return response()->json($voucher, 201);
    }

    public function activate(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->is_active = true;
        $voucher->save();

        return response()->json($voucher, 200);
    }

    public function apply(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $voucher = Voucher::where('code', $request->code)->first();

        if (!$voucher || $voucher->expires_at < now() || !$voucher->is_active) {
            return response()->json(['message' => 'Voucher is invalid or expired'], 400);
        }

        return response()->json(['message' => 'Voucher applied successfully!'], 200);
    }
}
