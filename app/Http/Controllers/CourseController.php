<?php

namespace App\Http\Controllers;

use App\Chapter;
use App\Course;
use App\Mentor;
use App\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\MyCourse;
use Illuminate\Support\Facades\Validator;


class CourseController extends ApiController
{
    public function index(Request $request)
    {
        $courses = Course::query();

        $q = $request->query("q");
        $status = $request->query("status");

        // FIlter berdasrakan nama
        $courses->when($q, function ($query) use ($q) {
            return $query->whereRaw("name LIKE '%" . strtolower($q) . "%'");
        });

        // FIlter berdasrakan status
        $courses->when($status, function ($query) use ($status) {
            return $query->where("status", "=", $status);
        });

        return $this->successResponse($courses->paginate(5), "Get Courses success", 200);
    }

    public function show($id)
    {
        $course = Course::with("chapters.lessons")->with("mentor")->with("images")->find($id);
        if (!$course) {
            return $this->errorResponse("Course not found", 404);
        }

        $reviews = Review::where("course_id", "=", $id)->get()->toArray();
        if (count($reviews) > 0) {
            $userIds = array_column($reviews, "user_id");
            $users = getUserByIds($userIds);
            if ($users["success"] === false) {
                $reviews = [];
            } else {
                foreach ($reviews as $key => $review) {
                    $userIndex = array_search($review["user_id"], array_column($users["details"], "id"));
                    $reviews[$key]["users"] = $users["details"][$userIndex];
                }
            }
        }
        $totalStudent = MyCourse::where("course_id", "=", $id)->count();
        $totalVideo = Chapter::where("course_id", "=", $id)->withCount("lessons")->get()->toArray();
        $finalTotalVideo = array_sum(array_column($totalVideo, "lessons_count"));

        $course["reviews"] = $reviews;
        $course["total_videos"] = $finalTotalVideo;
        $course["total_student"] = $totalStudent;

        return $this->successResponse($course, "Get course success", 200);
    }

    public function create(Request $request)
    {
        $rules = [
            "name" => "required|string",
            "certificate" => "required|boolean",
            "thumbnail" => "string",
            "type" => "required|in:free,premium",
            "status" => "required|in:draft,published",
            "price" => "required|integer",
            "level" => "required|in:all-level,beginner,intermediate,advance",
            "mentor_id" => "required|integer",
            "description" => "string"
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $mentor_id = $request->input("mentor_id");

        $mentor = Mentor::find($mentor_id);

        if (!$mentor) {
            return $this->errorResponse("Mentor not found", 404);
        }

        $course = Course::create($data);

        return $this->successResponse($course, "Created success", 200);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            "name" => "required|string",
            "certificate" => "required|boolean",
            "thumbnail" => "string",
            "type" => "required|in:free,premium",
            "status" => "required|in:draft,published",
            "price" => "required|integer",
            "level" => "required|in:all-level,beginner,intermediate,advance",
            "mentor_id" => "required|integer",
            "description" => "string"
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $course = Course::find($id);

        if (!$course) {
            return $this->errorResponse("Course not found", 404);
        }

        $mentor_id = $request->input("mentor_id");

        if ($mentor_id) {
            $mentor = Mentor::find($mentor_id);
            if (!$mentor) {
                return $this->errorResponse("Mentor not found", 404);
            }
        }

        $course->fill($data);
        $course->save();

        return $this->successResponse($course, "Update success", 200);
    }

    public function delete($id)
    {

        $course = Course::find($id);

        if (!$course) {
            return $this->errorResponse("Course not found", 404);
        }

        $course->delete();

        return $this->successResponse(null, "Delete success", 200);
    }
}