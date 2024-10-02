<?php

namespace App\Services;

class FavouriteService
{
    protected const MAX_FAVOURITES = 100;

    public function store($user, $medicine)
    {
        try {
            \DB::beginTransaction();
            $favourites = $user->favourites();

            if ($favourites->where('medicine_id', $medicine->id)->exists()) {
                $favourites->detach($medicine->id);
                $message = 'favourite deleted successfully';

            } elseif ($favourites->count() < self::MAX_FAVOURITES) {
                $favourites->syncWithoutDetaching($medicine);
                $message = 'favourite added successfully';
            } else {
                $message = 'favourite limit exceeded you cant add more than '.self::MAX_FAVOURITES.'medicines to favourites';
            }
            \DB::commit();

            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json(['message' => 'some thing went wrong']);

        }

    }
}
