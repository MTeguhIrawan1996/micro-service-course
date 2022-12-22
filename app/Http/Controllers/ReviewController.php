<?php

namespace App\Http\Controllers;

use App\Review;
use App\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class ReviewController extends ApiController
{
    public function create(Request $request)
    {
        $rules = [
            "user_id" => "required|integer",
            "course_id" => "required|integer",
            "rating" => "required|integer|min:1|max:5",
            "note" => "required|string",
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $course_id = $request->input("course_id");
        $course = Course::find($course_id);
        if (!$course) {
            return $this->errorResponse("Course not found", 404);
        }

        $userId = $request->input("user_id");
        $user = getUser($userId);
        if ($user["success"] === false) {
            return $this->errorResponse($user["message"], $user["status"]);
        }

        $isExistReview = Review::where("course_id", "=", $course_id)->where("user_id", "=", $userId)->exists();
        if ($isExistReview) {
            return $this->errorResponse("User alredy teken is review", 409);
        }

        $review = Review::create($data);
        return $this->successResponse($review, "Created success", 200);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            "rating" => "required|integer|min:1|max:5",
            "note" => "required|string",
        ];

        $data = $request->except("user_id", "course_id");

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $review = Review::find($id);

        if (!$review) {
            return $this->errorResponse("Review not found", 404);
        }

        $review->fill($data);
        $review->save();

        return $this->successResponse($review, "Update success", 200);
    }

    public function delete($id)
    {

        $review = Review::find($id);

        if (!$review) {
            return $this->errorResponse("Review not found", 404);
        }

        $review->delete();

        return $this->successResponse(null, "Delete success", 200);
    }
}