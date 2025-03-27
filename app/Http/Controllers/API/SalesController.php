<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('filter') || $request->query('search')) {
            $filterDate = $request->query('filter');
            $searchValue = $request->query('search');

            if ($filterDate !== null && $searchValue !== null) {
                $data['sales'] = Sales::where('sales_date', $filterDate)->where('business_name', 'like', "%".$searchValue.'%')->get();
            } elseif ($filterDate !== null && $searchValue == null) {
                $data['sales'] = Sales::where('sales_date', $filterDate)->get();
            } elseif ($filterDate == null && $searchValue !== null) {
                $data['sales'] = Sales::where('business_name', 'like', $searchValue)->get();
            }
            return response()->json(['status' => "success", 'data' => $data], 200);
        } else {
            $data['sales'] = Sales::all();
            return response()->json(['status' => "success", 'data' => $data], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->input(), [
            'business_name' => 'required|string',
            'services' => 'required|string',
            'paid_amount' => 'required|integer',
            'due_amount' => 'nullable|integer',
            'sales_date' => 'required|date',
            'remarks' => 'required',
        ]);

        if ($validate->fails()) {
            $data['message'] = $validate->errors();
            return response()->json(['status' => 'error', 'data' => $data], 422);
        }

        try {
            $input = $request->input();
            $fileExist = $request->has('file');
            if ($fileExist == true) {
                $file = File::get($request->file('file'));
                $fileName = time() . '-' . $request->file('file')->getClientOriginalName();
                $filePath = '/sales/' . $fileName;
                $store = Storage::disk('public')->put($filePath, $file);
                $filePath = '/storage'. $filePath;
            }

            $sale = Sales::create([
                'user_id' => Auth::id(),
                'business_name' => $input['business_name'],
                'services' => $input['services'],
                'paid_amount' => $input['paid_amount'],
                'due_amount' => $input['due_amount'] ?: 0,
                'sales_date' => $input['sales_date'],
                'remarks' => $input['remarks'],
                'file' => $filePath,
            ]);
            $data['message'] = 'The sale entry has been stored.';
            $data['sale'] = $sale;
            return response()->json(['status' => "success", 'data' => $data], 200);
        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
            return response()->json(['status' => 'error', 'data' => $data], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data['sale'] = Sales::find($id);
        if ($data['sale'] == null) {
            $data['message'] = 'Sales not found of the given id.';
            return response()->json(['status' => 'error', 'data' => $data], 400);
        }
        return response()->json(['status' => "success", 'data' => $data], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->input(), [
            'business_name' => 'required|string',
            'services' => 'nullable|string',
            'paid_amount' => 'nullable|integer',
            'due_amount' => 'nullable|integer',
            'sales_date' => 'nullable|date',
            'remarks' => 'nullable',
        ]);

        if ($validate->fails()) {
            $data['message'] = $validate->errors();
            return response()->json(['status' => 'error', 'data' => $data], 422);
        }

        try {
            $input = $request->input();
            $fileExist = $request->has('file');
            $filePath = null;
            if ($fileExist == true) {
                $file = File::get($request->file('file'));
                $fileName = time() . '-' . $request->file('file')->getClientOriginalName();
                $filePath = '/sales/' . $fileName;
                $store = Storage::disk('public')->put($filePath, $file);
                $filePath = '/storage'. $filePath;
            }
            $sale = Sales::find($id);
            if ($sale == null) {
                $data['message'] = 'Sales not found of the given id.';
                return response()->json(['status' => 'error', 'data' => $data], 400);
            }
            $sale->update([
                'user_id' => Auth::id(),
                'business_name' => $request->input('business_name') ?: $sale->business_name,
                'services' => $request->input('services') ?: $sale->services,
                'paid_amount' => $request->input('paid_amount') ?: $sale->paid_amount,
                'due_amount' => $request->input('due_amount') ?: $sale->due_amount,
                'sales_date' => $request->input('sales_date') ?: $sale->sales_date,
                'remarks' => $request->input('remarks') ?: $sale->remarks,
                'file' => $filePath ?: $sale->file,
            ]);
            $data['message'] = 'The sale has been updated.';
            $data['sale'] = $sale;
            return response()->json(['status' => "success", 'data' => $data], 200);
        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
            return response()->json(['status' => 'error', 'data' => $data], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sale = Sales::find($id);
        if ($sale == null) {
            $data['message'] = 'Sales not found of the given id.';
            return response()->json(['status' => 'error', 'data' => $data], 400);
        }
        $sale->destroy($sale->id);
        $data['message'] = 'The sale of given id has been deleted';
        return response()->json(['status' => 'success', 'data' => $data], 200);
    }
}
