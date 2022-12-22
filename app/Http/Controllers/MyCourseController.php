<?php

namespace App\Http\Controllers;

use App\MyCourse;
use App\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class MyCourseController extends ApiController
{
    public function index(Request $request)
    {

        $myCourses = MyCourse::query()->with('course');

        $user_id = $request->query("user_id");

        // FIlter berdasrakan User id
        $myCourses->when($user_id, function ($query) use ($user_id) {
            return $query->where("user_id", "=", $user_id);
        });

        return $this->successResponse($myCourses->get(), "Get my courses success", 200);
    }

    public function create(Request $request)
    {
        $rules = [
            "course_id" => "required|integer",
            "user_id" => "required|integer",
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

        $isExistMyCourse = MyCourse::where("course_id", "=", $course_id)->where("user_id", "=", $userId)->exists();
        if ($isExistMyCourse) {
            return $this->errorResponse("User alredy teken is course", 409);
        }

        if ($course->type === 'premium') {
            if ($course->price === 0) {
                return $this->errorResponse('price can\'t be 0', 500);
            }
            $order = postOrder([
                'user' => $user['details'],
                'course' => $course->toArray(),
            ]);

            if ($order['success'] === false) {
                return $this->errorResponse($order['message'], $order['status']);
            }
            return $this->successResponse($order['details'], $order['message'], $order['status']);
        } else {
            $myCourse = MyCourse::create($data);
            return $this->successResponse($myCourse, "Created Free Course success", 200);
        }
    }

    public function createPremiumAccess(Request $request)
    {
        $data = $request->all();

        $myCourse = MyCourse::create($data);
        return $this->successResponse($myCourse, "Created Premium Access success", 200);
    }
}