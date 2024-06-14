<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\ProcessRequest;
use App\Models\Management\Task;
use App\Services\Helper;
class ManageController extends Controller
{
    use Helper;

    public function index()
    {
        $data = self::loadDetails();
        return view("modules.index", compact('data'));
    }

    public function store(ProcessRequest $request)
    {
        return Task::createOrUpdateTask(request()->payload);
    }

    public function loadTask()
    {
       return Task::loadTable(request()->all());
    }

    public function uploadFile()
    {   
        return self::moveFile(request()->file('file'));
    }
}
