<?php

namespace App\Http\Controllers\ManufacturerController;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Illuminate\Support\Facades\Request;

class ManufacturerController extends Controller
{
    //
    public function index()
    {

        $manufacturers = Manufacturer::all();

        return response()->json([
            'manufacturers' => $manufacturers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
        ]);
        $manufacturer = Manufacturer::create($validated);

        return response()->json([
            'message' => 'Manufacturer added successfully',
            'manufacturer' => $manufacturer,
        ]);
    }

    public function destroy($manufacturer)
    {
        $manufacturer = Manufacturer::find($manufacturer);
        $manufacturer->delete();

        return response()->json([
            'message' => 'Manufacturer deleted successfully',
        ]);
    }
}
