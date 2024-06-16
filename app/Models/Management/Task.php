<?php

namespace App\Models\Management;

use App\Observers\TaskObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Services\Helper;
use App\Services\JsonOutput;

#[ObservedBy([TaskObserver::class])]
class Task extends Model
{
    use HasFactory, Helper;

    const DONE = 3;
    const ARCHIVE = 4;

    public static function createOrUpdateTask($request)
    {
        try {
            DB::beginTransaction();

            if (empty($request)) {
                return false;
            }

            $task = self::where("id", (isset($request["id"]) ?  $request["id"] : null))->first();
            if (empty($task)) {
                $task = new self();
                $task->user_id = auth()->user()->id;
                $task->parent_id = $request["parent_id"];
            }

            $task->title = $request["title"];
            $task->content = $request["content"];
            $task->status_id = $request["status"];
            $task->attachment = $request["attachment"];
            $task->condition_id = $request["condition_id"];
            $task->save();

            DB::commit();
            
            return self::loadResponse('Transaction Successfully Completed!', Response::HTTP_OK, new JsonOutput);
        } catch(\Throwable $th) {
            DB::rollback();
            return self::loadResponse($th->getMessage(), Response::HTTP_BAD_REQUEST, new JsonOutput);
        }
    }

    public static function loadTable($request)
    {
        try {
            $data = [];
            $tablecols = [
                0 => ['title'],
                1 => ['content'],
                2 => ['status_id'],
                3 => ['condition_id'],
                4 => ['attachment'],
                5 => ['created_at'],
            ];

            $dataset = self::select(
                "id",
                "title",
                "content",
                "status_id",
                "attachment",
                "parent_id",
                "created_at",
                "condition_id"
            );
            if ($request["parentId"]) {
                $dataset->where("id", $request["parentId"])->orWhere("parent_id", $request["parentId"]);
            }

            self::searchColumn($dataset, $request);
            if (isset($request["order"][0]['column'])) {
                $dataset->orderBy($tablecols[$request["order"][0]['column']][0], $request["order"][0]['dir']);
            }

            $data = $dataset->where("user_id", auth()->user()->id)->orderBy('title', 'asc')->get()
            ->map(function($item) {
                $description = collect(config("taskOption.status"))->where("id", $item->status_id)->first()["label"];
                $condition = collect(config("taskOption.condition"))
                    ->where("id", $item->condition_id)
                    ->first()["label"];
                $item->status_description = $description;
                $item->condition_description = $condition;
                $item->createdDate = $item->created_at->format("Y-m-d");
                $item->withChild = $item->where("parent_id", $item->id)->count() > 0 ? true : false;
                return $item;
            });
            
            return [
                "draw" => $request["draw"],
                "recordsTotal" => collect($data)->count(),
                "recordsFiltered" => collect($data)->count(),
                "data" => $data
            ];
        } catch(\Throwable $th) {
            return self::loadResponse($th->getMessage(), Response::HTTP_BAD_REQUEST, new JsonOutput);
        }
    }

    public static function searchColumn($dataset, $request)
    {
        if (isset($request["tableparam"])) {
            return collect($request["tableparam"])->map(function ($value) use (&$dataset) {
                if (!empty($value['column_value'])) {
                    if ($value['column_name'] == "status_description") {
                        $collection = collect(config("taskOption.status"))->filter(function ($data) use ($value) {
                            return stripos($data['label'], $value['column_value']) !== false;
                        });
                        $dataset->whereIn('status_id',  $collection->pluck("id"));
                    } elseif ($value['column_name'] == "condition_description") {
                        $condition = collect(config("taskOption.condition"))->filter(function ($data) use ($value) {
                            return stripos($data['label'], $value['column_value']) !== false;
                        });
                        $dataset->whereIn('condition_id',  $condition->pluck("id"));
                    } elseif ($value['column_name'] == "createdDate") {
                        $dataset->where('created_at', 'like', "%".$value['column_value']."%");
                    } else {
                        $dataset->where($value['column_name'], 'like', "%".$value['column_value']."%");
                    }
                }
            });
        }
    }
}
