<?php

namespace App\Services;

class FavouriteService
{
    protected $max_favourites;

    public function __construct()
    {
        $this->max_favourites = config('app.data.max_favourites');
    }

    public function store($user, $medicine)
    {

        try {
            \DB::beginTransaction();
            $favourites = $user->favourites();

            if ($favourites->where('medicine_id', $medicine->id)->exists()) {
                $user->favourites()->detach($medicine->id);
                $message = 'favourite deleted successfully';

            } elseif ($user->favourites->count() < $this->max_favourites) {
                $user->favourites()->attach($medicine);
                $message = 'favourite added successfully';
            } else {
                $message = 'favourite limit exceeded you cant add more than '.$this->max_favourites.' medicines to favourites';

            }
            \DB::commit();

            return response()->json(['message' => $message]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json(['message' => 'some thing went wrong']);

        }

    }
}
