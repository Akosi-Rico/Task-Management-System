<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\ProcessRequest;
use App\Models\Management\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManageController extends Controller
{
     
    public function index()
    {
        $data = [
            "statusOption" => config("taskOption.status"),
            "taskUpdateUrl" => route("store"),
            "tasks" => Task::get()->map(function($item) {
                $item->createdDate = $item->created_at->format("Y-m-d");
                return $item;
            }),
            "currentUser" => auth()->user()->name,
            "currentDate" => now()->format('F j, Y'),
            "taskUrl" => route("load.task"),
            "logoutUrl" => route("logout.account"),
            "loginUrl" => route("login.index"),
        ];
        return view("modules.index", compact('data'));
    } 

    public function create()
    {
        //
    }

    public function store(ProcessRequest $request)
    {
        return Task::createOrUpdateTask(request()->payload);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function loadTask()
    {
        try {
            $data = Task::get()->map(function($item) {
                $item->createdDate = $item->created_at->format("Y-m-d");
                return $item;
            });
            
          return [
                "draw" => request()->draw,
                "recordsTotal" => collect($data)->count(),
                "recordsFiltered" => collect($data)->count(),
                "data" => $data
            ];
        } catch(\Throwable $th) {

        }
    }
}
