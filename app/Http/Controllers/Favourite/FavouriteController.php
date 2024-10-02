<?php

namespace App\Http\Controllers\Favourite;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Services\FavouriteService;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    protected FavouriteService $favouriteService;

    public function __construct(FavouriteService $favouriteService)
    {
        $this->favouriteService = $favouriteService;
    }

    public function store(Medicine $medicine)
    {
        $user = Auth::user();

        return $this->favouriteService->store($user, $medicine);
    }

    public function index()
    {
        $user = Auth::user();

        return response()->json([
            'favourites' => $user->favourites,
        ]);
    }
}
