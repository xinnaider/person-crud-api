<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
       
    }
}