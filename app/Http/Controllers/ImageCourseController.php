<?php

namespace App\Http\Controllers;

use App\ImageCourse;
use App\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class ImageCourseController extends ApiController
{
    public function create(Request $request)
    {
        $rules = [
            "image" => "required|string",
            "course_id" => "required|integer",
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

        $image = ImageCourse::create($data);

        return $this->successResponse($image, "Created success", 200);
    }

    public function delete($id)
    {

        $image = ImageCourse::find($id);

        if (!$image) {
            return $this->errorResponse("Image course not found", 404);
        }

        $image->delete();

        return $this->successResponse(null, "Delete success", 200);
    }
}