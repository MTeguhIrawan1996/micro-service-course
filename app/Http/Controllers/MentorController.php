<?php

namespace App\Http\Controllers;

use App\Mentor;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class MentorController extends ApiController
{
    public function index()
    {

        $mentors = Mentor::all();

        return $this->successResponse($mentors, "Get Mentors success", 200);
    }

    public function show($id)
    {

        $mentor = Mentor::find($id);

        if (!$mentor) {
            return $this->errorResponse("Mentor not found", 404);
        }

        return $this->successResponse($mentor, "Get mentor success", 200);
    }

    public function create(Request $request)
    {
        $rules = [
            "name" => "required|string",
            "profile" => "required|string",
            "profession" => "required|string",
            "email" => "required|email"
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        if ($data["email"]) {
            $checkEmail = Mentor::where("email", "=", $data["email"])->exists();
            if ($checkEmail) {
                return $this->errorResponse("Email already exists", 409);
            }
        }

        $mentor = Mentor::create($data);

        return $this->successResponse($mentor, "Created success", 200);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            "name" => "string",
            "profile" => "string",
            "profession" => "string",
            "email" => "email"
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $mentor = Mentor::find($id);

        if (!$mentor) {
            return $this->errorResponse("Mentor not found", 404);
        }

        if ($data["email"]) {
            $checkEmail = Mentor::where("email", "=", $data["email"])->exists();
            if ($checkEmail && $data["email"] !== $mentor["email"]) {
                return $this->errorResponse("Email already exists", 409);
            }
        }

        $mentor->fill($data);
        $mentor->save();
        return $this->successResponse($mentor, "Update success", 200);
    }

    public function delete($id)
    {

        $mentor = Mentor::find($id);

        if (!$mentor) {
            return $this->errorResponse("Mentor not found", 404);
        }

        $mentor->delete();

        return $this->successResponse(null, "Delete success", 200);
    }
}