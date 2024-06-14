<?php

namespace App\Services;

use App\Contracts\TaskInterface;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JsonOutput;

trait Helper
{
    public static function loadResponse($message, $code, TaskInterface $response)
    {
        return $response->output($message, $code);
    }

    public static function loadDetails()
    {
        return [
            "statusOption" => config("taskOption.status"),
            "taskUpdateUrl" => route("store"),
            "currentUser" => auth()->user()->name,
            "currentDate" => now()->format('F j, Y'),
            "taskUrl" => route("load.task"),
            "logoutUrl" => route("logout.account"),
            "loginUrl" => route("login.index"),
            "uploadUrl" => route("upload.file"),
        ];
    }

    public static function moveFile($results) 
    {
        $fileName = null;
        if (empty($results)) {
            return false;
        }

        foreach ($results as $file) {
            if ($file->getClientOriginalName()) {
                $fileName = $file->getClientOriginalName();
                Storage::disk("temporary")->put($file->getClientOriginalName(), file_get_contents($file));
            }
        }

        return self::loadResponse($fileName, Response::HTTP_OK, new JsonOutput);
    }
}
