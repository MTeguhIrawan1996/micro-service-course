<?php

namespace App\Http\Controllers;

use App\Course;
use App\Chapter;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class ChapterController extends ApiController
{
    public function index(Request $request)
    {

        $chapters = Chapter::query();
        $course_id = $request->query("course_id");

        // FIlter berdasrakan courses_id
        $chapters->when($course_id, function ($query) use ($course_id) {
            return $query->where("course_id", "=", $course_id);
        });
        // if (count($chapters->get()) === 0) {
        //     return $this->errorResponse("Chapters not found", 404);
        // }

        return $this->successResponse($chapters->get(), "Get Chapter success", 200);
    }

    public function show($id)
    {
        $chapter = Chapter::find($id);

        if (!$chapter) {
            return $this->errorResponse("Chapter not found", 404);
        }

        return $this->successResponse($chapter, "Get chapter success", 200);
    }

    public function create(Request $request)
    {
        $rules = [
            "name" => "required|string",
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

        $chapter = Chapter::create($data);

        return $this->successResponse($chapter, "Created success", 200);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            "name" => "required|string",
            "course_id" => "required|integer",
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $chapter = Chapter::find($id);

        if (!$chapter) {
            return $this->errorResponse("Chapter not found", 404);
        }

        $course_id = $request->input("course_id");

        if ($course_id) {
            $course = Course::find($course_id);
            if (!$course) {
                return $this->errorResponse("Course not found", 404);
            }
        }

        $chapter->fill($data);
        $chapter->save();

        return $this->successResponse($chapter, "Update success", 200);
    }

    public function delete($id)
    {

        $chapter = Chapter::find($id);

        if (!$chapter) {
            return $this->errorResponse("Chapter not found", 404);
        }

        $chapter->delete();

        return $this->successResponse(null, "Delete success", 200);
    }
}