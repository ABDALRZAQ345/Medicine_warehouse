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
    /**
     * showing the medicines and filtering
     * @authenticated
     * @header Authorization Bearer {access_token}
     * @queryParam filter['type'] filter medicine by type
     * @queryParam filter['trade_name'] filter medicine by trade_name
     * @queryParam filter['scientific_name'] filter medicine by scientific_name
     * @queryParam trashed just pass it if you want to show trashed medicines
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     * @response  200 {
     * "data": [
     * {
     * "id": 679,
     * "type": "Arden Hammes",
     * "scientific_name": "Briana Senger",
     * "trade_name": "Prof. Abbigail Hodkiewicz",
     * "price": 985,
     * "quantity": 228,
     * "manufacturer_id": 14,
     * "expires_at": "2025-03-18 21:11:03",
     * "expires_at_human": "5 months from now",
     * "manufacturer": {
     * "id": 14,
     * "name": "Tracey Stiedemann I",
     * "created_at": "2024-09-18T21:11:03.000000Z",
     * "updated_at": "2024-09-18T21:11:03.000000Z"
     * }
     * },
     * {
     * "id": 1086,
     * "type": "Amani Kutch",
     * "scientific_name": "Miss Pearline Roberts",
     * "trade_name": "Estel Abbott",
     * "price": 524,
     * "quantity": 506,
     * "manufacturer_id": 1,
     * "expires_at": "2025-03-18 21:11:04",
     * "expires_at_human": "5 months from now",
     * "manufacturer": {
     * "id": 1,
     * "name": "Tracey Stiedemann I",
     * "created_at": "2024-09-18T21:11:03.000000Z",
     * "updated_at": "2024-09-18T21:11:03.000000Z"
     * }
     * }
     * ],
     * "links": {
     * "first": "http://127.0.0.1:8000/api/medicines?page=1",
     * "last": "http://127.0.0.1:8000/api/medicines?page=1",
     * "prev": null,
     * "next": null
     * },
     * "meta": {
     * "current_page": 1,
     * "from": 1,
     * "last_page": 1,
     * "links": [
     * {
     * "url": null,
     * "label": "&laquo; Previous",
     * "active": false
     * },
     * {
     * "url": "http://127.0.0.1:8000/api/medicines?page=1",
     * "label": "1",
     * "active": true
     * },
     * {
     * "url": null,
     * "label": "Next &raquo;",
     * "active": false
     * }
     * ],
     * "path": "http://127.0.0.1:8000/api/medicines",
     * "per_page": 10,
     * "to": 2,
     * "total": 2
     * }
     * }
     */
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

    /**
     * Store new medicine
     *
     * @description This endpoint is accessible only to users with the admin role.
     * @authenticated
     * @group admin
     * @header Authorization Bearer {access_token}
     * @param MedicineRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response  403 {
     * "message": "User does not have the right roles."
     * }
     *
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     *
     * @response 200 {
     * "medicine": {
     * "scientific_name": "1",
     * "trade_name": "1",
     * "type": "1",
     * "manufacturer_id": "100",
     * "quantity": "1",
     * "price": "11",
     * "expires_at": "2024-10-20T19:00:27.554469Z",
     * "creator_id": 1,
     * "updated_at": "2024-09-19T19:00:27.000000Z",
     * "created_at": "2024-09-19T19:00:27.000000Z",
     * "id": 10002
     * }
     * }
     *
     * @response  422
     * {
     * "message": "The scientific name field is required. (and 8 more errors)",
     * "errors": {
     * "scientific_name": [
     * "The scientific name field is required."
     * ],
     * "trade_name": [
     * "The trade name field is required."
     * ],
     * "type": [
     * "The type field is required."
     * ],
     * "manufacturer_id": [
     * "The selected manufacturer id is invalid."
     * ],
     * "quantity": [
     * "The quantity field is required."
     * ],
     * "price": [
     * "The price field is required."
     * ],
     * "days": [
     * "The days field is required."
     * ],
     * "months": [
     * "The months field is required."
     * ],
     * "years": [
     * "The years field is required."
     * ]
     * }
     * }
     */
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

        $medicine = Auth::user()->medicines()->create($validated);
        $medicine->load('manufacturer');
        return response()->json([
            'medicine' => new MedicineResource($medicine),

        ]);
    }

    /**
     * Show a specific medicine
     *
     * @authenticated
     * @header Authorization Bearer {access_token}
     * @param MedicineRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     *
     * @response 200 {
     * {
     * "medicine": {
     * "id": 2,
     * "type": "Dr. Glenna Mann Jr.",
     * "scientific_name": "Jabari D'Amore PhD",
     * "trade_name": "Daron Dickens",
     * "price": 731,
     * "quantity": 661,
     * "manufacturer_id": 1,
     * "expires_at": "2025-03-24 16:05:08",
     * "expires_at_human": "5 months from now",
     * "manufacturer": {
     * "id": 1,
     * "name": "Johathan Gaylord III",
     * "created_at": "2024-09-24T16:05:08.000000Z",
     * "updated_at": "2024-09-24T16:05:08.000000Z"
     * }
     * }
     * }
     * }
     *
     * @response 404 {
     *  "message" => "object not found "
     * }
     *
     *
     */
    public function show(Medicine $medicine)
    {
        $medicine->load('manufacturer');
        return response()->json([
            'medicine' => new MedicineResource($medicine),

        ]);
    }

    /**
     * Searching in medicines
     *
     * @authenticated
     * @header Authorization Bearer {access_token}
     * @param MedicineRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @queryParam search entre what you want to search for
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     *
     * @response 200 {
     * {
     * "medicine": {
     * "id": 2,
     * "type": "Dr. Glenna Mann Jr.",
     * "scientific_name": "Jabari D'Amore PhD",
     * "trade_name": "Daron Dickens",
     * "price": 731,
     * "quantity": 661,
     * "manufacturer_id": 1,
     * "expires_at": "2025-03-24 16:05:08",
     * "expires_at_human": "5 months from now",
     * "manufacturer": {
     * "id": 1,
     * "name": "Johathan Gaylord III",
     * "created_at": "2024-09-24T16:05:08.000000Z",
     * "updated_at": "2024-09-24T16:05:08.000000Z"
     * }
     * }
     * }
     * }
     *
     *
     */
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

    /**
     * updating a specific medicine
     *
     * @description This endpoint is accessible only to users with the admin role.
     * @authenticated
     * @group admin
     * @header Authorization Bearer {access_token}
     * @param MedicineRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response  403 {
     * "message": "User does not have the right roles."
     * }
     *
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     *
     * @response 200 {
     * "medicine": {
     * "scientific_name": "1",
     * "trade_name": "1",
     * "type": "1",
     * "manufacturer_id": "100",
     * "quantity": "1",
     * "price": "11",
     * "expires_at": "2024-10-20T19:00:27.554469Z",
     * "creator_id": 1,
     * "updated_at": "2024-09-19T19:00:27.000000Z",
     * "created_at": "2024-09-19T19:00:27.000000Z",
     * "id": 10002
     * }
     * }
     *
     * @response  422
     * {
     * "message": "The scientific name field is required. (and 8 more errors)",
     * "errors": {
     * "scientific_name": [
     * "The scientific name field is required."
     * ],
     * "trade_name": [
     * "The trade name field is required."
     * ],
     * "type": [
     * "The type field is required."
     * ],
     * "manufacturer_id": [
     * "The selected manufacturer id is invalid."
     * ],
     * "quantity": [
     * "The quantity field is required."
     * ],
     * "price": [
     * "The price field is required."
     * ],
     * "days": [
     * "The days field is required."
     * ],
     * "months": [
     * "The months field is required."
     * ],
     * "years": [
     * "The years field is required."
     * ]
     * }
     * }
     */

    public function update(MedicineRequest $request, Medicine $medicine)
    {
        $validated = $request->validated();

        $validated['expires_at'] = ($medicine->created_at)->addDays($validated['days'] + $validated['months'] * 30 + $validated['years'] * 365);
        $medicine->update(Arr::except($validated, ['days', 'months', 'years']));
        $medicine->load('manufacturer');
        return response()->json([
            'medicine' => new MedicineResource($medicine)
        ]);

    }

    /**
     * moving  a specific medicine to trash
     *
     * @description This endpoint is accessible only to users with the admin role.
     * @authenticated
     * @group admin
     * @header Authorization Bearer {access_token}
     * @param MedicineRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response  403 {
     * "message": "User does not have the right roles."
     * }
     *
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     *
     * @response 200 {
     * "message": "medicine deleted successfully"
     * }
     *
     * @response  404
     * {
     * "message": " not found."
     * }
     *
     */

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        return response()->json([
            'message' => 'medicine deleted successfully'
        ]);

    }

    /**
     * restore a specific medicine from trash
     *
     * @description This endpoint is accessible only to users with the admin role.
     * @authenticated
     * @group admin
     * @header Authorization Bearer {access_token}
     * @param MedicineRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response  403 {
     * "message": "User does not have the right roles."
     * }
     *
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     *
     * @response 200 {
     * 'message' => 'medicine restored successfully'
     * }
     *
     * @response  404
     * {
     * "message": " not found."
     * }
     *
     * @response 400 {
     *     'message' => 'medicine not restored because it is already restored'
     * }
     */

    public function restore($medicine)
    {

        $medicine = Medicine::withTrashed()->find($medicine);

        if ($medicine->trashed()) {
            $medicine->restore();
            return response()->json([
                'message' => 'medicine restored successfully'
            ]);
        }
        return response()->json([
            'message' => 'medicine not restored because it is already restored'
        ], Response::HTTP_BAD_REQUEST);

    }

    /**
     * delete a specific medicine forever from trash
     *
     * @description This endpoint is accessible only to users with the admin role.
     * @authenticated
     * @group admin
     * @header Authorization Bearer {access_token}
     * @param MedicineRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @response  403 {
     * "message": "User does not have the right roles."
     * }
     *
     * @response  401 {
     * "message": "Unauthenticated."
     * }
     *
     * @response 200 {
     * 'message' => 'medicine deleted successfully'
     * }
     *
     * @response  404
     * {
     * "message": " not found."
     * }
     *
     * @response 400 {
     *     'message' => 'medicine cant be deleted'
     * }
     */

    public function force_delete($medicine)
    {
        $medicine = Medicine::onlyTrashed()->find($medicine);
        $medicine->forceDelete();
        return response()->json([
            'message' => 'medicine deleted successfully'
        ]);


    }


}
