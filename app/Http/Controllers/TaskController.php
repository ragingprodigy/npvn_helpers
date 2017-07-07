<?php
declare(strict_types=1);

/**
 * Created by: dapo <o.omonayajo@gmail.com>
 * Created on: 7/7/17, 9:33 PM
 */

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Task::orderBy('updated_at', 'desc')->get());
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function download(int $id)
    {
        /** @var Task $task */
        $task = Task::whereId($id)->first();

        if ($task === null) {
            throw new NotFoundHttpException('Task Not found');
        }

        if (empty($task->output_file)) {
            return response('No file available for download', 404);
        }

        return response()->download($task->output_file);
    }
}
