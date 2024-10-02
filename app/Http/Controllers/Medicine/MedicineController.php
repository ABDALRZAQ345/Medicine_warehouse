<?php

namespace App\Http\Controllers\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medicine\MedicineRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Medicine
 */
class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $medicines = QueryBuilder::for(Medicine::class)
            ->allowedFilters([
                'type', 'scientific_name', 'trade_name',
            ])
            ->select(['id', 'type', 'scientific_name', 'trade_name', 'price', 'quantity', 'manufacturer_id', 'expires_at', 'discount', 'photo'])
            ->filterExpiredAndTrashed($request)
            ->with('manufacturer')
            ->paginate();

        return MedicineResource::collection($medicines);

    }

    public function store(MedicineRequest $request)
    {

        $validated = $request->validated();
        $validated['expires_at'] = now()->addDays($validated['days'] + $validated['months'] * 30 + $validated['years'] * 365);
        unset($validated['days'], $validated['months'], $validated['years']);

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

    public function search(Request $request)
    {
        $medicines = Medicine::Search($request->search)
            ->query(function ($query) {
                $query->where('expires_at', '>', now());
                $query->with('manufacturer');
            })
            ->paginate();

        return response()->json($medicines);
    }

    public function update(MedicineRequest $request, Medicine $medicine)
    {
        $validated = $request->validated();

        $validated['expires_at'] = now()->addDays($validated['days'] + $validated['months'] * 30 + $validated['years'] * 365);
        try {
            \DB::beginTransaction();
            $medicine->update(Arr::except($validated, ['days', 'months', 'years']));
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

        $medicine = Medicine::withTrashed()->find($medicine);

        if ($medicine->trashed()) {
            $medicine->restore();

            return response()->json([
                'message' => 'medicine restored successfully',
            ]);
        }

        return response()->json([
            'message' => 'medicine not restored because it is already restored',
        ], Response::HTTP_BAD_REQUEST);

    }

    public function force_delete($medicine)
    {
        $medicine = Medicine::onlyTrashed()->find($medicine);
        $medicine->forceDelete();

        return response()->json([
            'message' => 'medicine deleted successfully',
        ]);

    }
}
