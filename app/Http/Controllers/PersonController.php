<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/person",
     *     tags={"Person"},
     *     summary="Get all persons",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of persons",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Person")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $pQuery = Person::query()->with('user');

        // Sqlite não suporta ILIKE :D
        if ($request->has('search')) {
            $pQuery->where(function($query) use ($request) {
                $searchTerm = strtolower($request->search);
                $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(title) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(relationship) LIKE ?', ["%{$searchTerm}%"]);
            });
        }


        return $this->success($pQuery->paginate());
    }

    /**
     * @OA\Get(
     *     path="/api/person/{id}",
     *     tags={"Person"},
     *     summary="Get a specific person",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person details",
     *         @OA\JsonContent(ref="#/components/schemas/Person")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Resource not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $person = Person::find($id);

        if (!$person) {
            return $this->notFound();
        }

        return $this->success($person->toArray());
    }

    /**
     * @OA\Post(
     *     path="/api/person",
     *     tags={"Person"},
     *     summary="Create a new person",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","title","birth_date","relationship"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="title", type="string", example="Mr."),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="relationship", type="string", enum={"single","married","divorced","widowed"}, example="single")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Person created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Person")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation error message")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'title' => 'required',
            'birth_date' => 'required|date|before:today|date_format:Y-m-d',
            'relationship' => 'required|in:single,married,divorced,widowed'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 400);
        }

        $validated = $validator->validated();

        $person = Person::create($validated);

        return $this->success($person->toArray(), 201, 'Person created successfully');
    }

    /**
     * @OA\Patch(
     *     path="/api/person/{id}",
     *     tags={"Person"},
     *     summary="Update an existing person",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="title", type="string", example="Mr."),
     *             @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="relationship", type="string", enum={"single","married","divorced","widowed"}, example="single")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Person")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Resource not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Person $person)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
            'title' => 'nullable',
            'birth_date' => 'nullable|date|before:today|date_format:Y-m-d',
            'relationship' => 'nullable|in:single,married,divorced,widowed'
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 400);
        }

        $validated = $validator->validated();

        $person->update($validated);

        return $this->success($person->toArray(), 200, 'Person updated successfully');
    }
}
