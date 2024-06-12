<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class Task extends Model
{
    use HasFactory;


    public static function createOrUpdateTask($request)
    {
        try {
            DB::beginTransaction();

            if (empty($request)) {
                return false;
            }

            $task = self::where("id", $request["id"])->first();
            if (empty($task)) {
                $task = new self();
            }

            $task->title = $request["title"];
            $task->content = $request["content"];
            $task->status_id = $request["status"];
            $task->save();

            DB::commit();
            
            return response()->json([
                "message" => "Transaction Successfully Completed!",
            ], Response::HTTP_OK);
        } catch(\Throwable $th) {
            DB::rollback();
            return response()->json([
                "message" => $th->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
