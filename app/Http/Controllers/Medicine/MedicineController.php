<?php

namespace App\Http\Controllers\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medicine\MedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $medicines = Medicine::Search($request->search)
            ->query(function ($query) use ($request) {
                $query->select(['id', 'type', 'scientific_name', 'trade_name', 'price', 'quantity', 'manufacturer_id', 'expires_at', 'discount', 'photo']);
                $query->filterExpiredAndTrashed($request);
                $query->with('manufacturer');
            })
            ->paginate();

        return MedicineResource::collection($medicines);

    }

    public function store(MedicineRequest $request)
    {

        $validated = $request->validated();

        if ($request->photo != null) {
            $validated['photo'] = $request->photo->store('photos', 'public'); // photo path
        } else {
            $validated['photo'] = 'photos/Untitled.jpeg';
        }
        try {
            \DB::beginTransaction();
            $medicine = Auth::user()->medicines()->create($validated);
            $medicine->load('manufacturer');
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'medicine' => new MedicineResource($medicine),

        ]);
    }

    public function show(Medicine $medicine)
    {
        $medicine->load('manufacturer');

        return response()->json([
            'medicine' => new MedicineResource($medicine),

        ]);
    }

    public function update(MedicineRequest $request, Medicine $medicine)
    {
        $validated = $request->validated();

        try {
            \DB::beginTransaction();
            $medicine->update($validated);
            $medicine->load('manufacturer');
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json([
            'medicine' => new MedicineResource($medicine),
        ]);

    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();

        return response()->json([
            'message' => 'medicine deleted successfully',
        ]);

    }

    public function restore($medicine)
    {

        $medicine = Medicine::onlyTrashed()->findOrFail($medicine);

        $medicine->restore();

        return response()->json([
            'message' => 'medicine restored successfully',
        ]);

    }
}
