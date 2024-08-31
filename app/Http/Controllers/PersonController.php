<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonController extends ApiController
{
    public function index()
    {
        $persons = Person::all()->toArray();

        return $this->success($persons);
    }

    public function show($id)
    {
        $person = Person::find($id);

        if (!$person) {
            return $this->notFound();
        }

        return $this->success($person->toArray());
    }

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
