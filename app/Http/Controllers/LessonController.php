<?php

namespace App\Http\Controllers;

use App\Lesson;
use App\Chapter;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class LessonController extends ApiController
{
    public function index(Request $request)
    {

        $lessons = Lesson::query();
        $chapter_id = $request->query("chapter_id");

        // FIlter berdasrakan Chapter id
        $lessons->when($chapter_id, function ($query) use ($chapter_id) {
            return $query->where("chapter_id", "=", $chapter_id);
        });

        return $this->successResponse($lessons->get(), "Get Lesson success", 200);
    }

    public function show($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return $this->errorResponse("Lesson not found", 404);
        }

        return $this->successResponse($lesson, "Get lesson success", 200);
    }


    public function create(Request $request)
    {
        $rules = [
            "name" => "required|string",
            "video" => "required|string",
            "chapter_id" => "required|integer",
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $chapter_id = $request->input("chapter_id");

        $chapter = Chapter::find($chapter_id);

        if (!$chapter) {
            return $this->errorResponse("Chapter not found", 404);
        }

        $lesson = Lesson::create($data);

        return $this->successResponse($lesson, "Created success", 200);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            "name" => "required|string",
            "video" => "required|string",
            "chapter_id" => "required|integer",
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $lesson = Lesson::find($id);

        if (!$lesson) {
            return $this->errorResponse("Lesson not found", 404);
        }

        $chapter_id = $request->input("chapter_id");

        if ($chapter_id) {
            $chapter = Chapter::find($chapter_id);
            if (!$chapter) {
                return $this->errorResponse("Chapter not found", 404);
            }
        }

        $lesson->fill($data);
        $lesson->save();

        return $this->successResponse($lesson, "Update success", 200);
    }

    public function delete($id)
    {

        $lesson = Lesson::find($id);

        if (!$lesson) {
            return $this->errorResponse("Lesson not found", 404);
        }

        $lesson->delete();

        return $this->successResponse(null, "Delete success", 200);
    }
}