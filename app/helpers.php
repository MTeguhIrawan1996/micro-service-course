<?php

use Illuminate\Support\Facades\Http;

function getUser($userId)
{
  $url = env("SERVICE_USER_URL") . "users/" . $userId;

  try {
    $response = Http::timeout(10)->get($url);

    $data = $response->json();
    return $data;
  } catch (\Throwable $e) {
    return [
      "success" => false,
      "status" => 500,
      "message" => "Internal Server Error"
    ];
  }
}

function getUserByIds($userIds = [])
{
  $url = env("SERVICE_USER_URL") . "users/";

  try {
    if (count($userIds) === 0) {
      return [
        "success" => true,
        "status" => 200,
        "detail" => []
      ];
    }

    $response = Http::timeout(10)->get($url, ["user_ids[]" => $userIds]);

    $data = $response->json();
    // $data["http_code"] = $response->getStatusCode();

    return $data;
  } catch (\Throwable $e) {
    return [
      "success" => false,
      "status" => 500,
      "message" => "Internal Server Error"
    ];
  }
}

function postOrder($params)
{
  $url = env("SERVICE_ORDER_URL") . 'api/orders';

  try {
    $response = Http::post($url, $params);
    $data = $response->json();
    return $data;
  } catch (\Throwable $e) {
    return [
      "success" => false,
      "status" => 500,
      "message" => "Service order payment unavailble"
    ];
  }
}