<?php

namespace App\Http\Controllers\General\Todo;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\General\TodoRequest;
use App\Http\Resources\General\TodoCollection;
use App\Http\Resources\General\TodoResource;
use App\Models\General\Todo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\HttpFoundation\Response;
use Unlu\Laravel\Api\QueryBuilder;

class TodoController extends Controller
{
    use ApiResponse;

    private $module = 'Todo';

    private $searchable = ['user', 'type', 'name', 'ip_address', 'date', 'mail', 'pop'];
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        try {
            $params = $request->all();
            $query = array();
            foreach ($this->searchable as $prop ) {
                if (in_array($prop, array_keys($params))) {
                    $query[$prop] = $params[$prop];
                }
            }

            return $this->sendResponse(TodoCollection::collection(Todo::where($query)->get()), $this->module . ' ' . config('responseMessages.retrieveSuccess'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TodoRequest $request
     * @return void
     */
    public function store(TodoRequest $request)
    {
        try {
            $todo = new Todo();
            $todo->type = $request->type;
            $todo->name = $request->name;
            $todo->description = $request->description;
            $todo->ip_address = request()->ip();
            if (strtotime($request->date) === false) {
                return $this->sendError(config('responseMessages.invalidDate'), [], Response::HTTP_BAD_REQUEST);
            }
            $todo->date = new Carbon(new \DateTime($request->date));
            $todo->pop = isset($request->pop) ? $request->pop : true;
            $todo->mail = isset($request->mail) ? $request->mail : false;
            if ($todo->save()) {
                return $this->sendResponse($todo, $this->module . ' ' . config('responseMessages.createSuccess'));
            }
            return $this->sendError(config('responseMessages.createFail'), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Todo $todo
     * @return void
     */
    public function show(Todo $todo)
    {
        return $this->sendResponse(new TodoResource($todo), $this->module .' '.config('responseMessages.retrieveSuccess'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Todo $todo
     * @return void
     */
    public function edit(Todo $todo)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Todo $todo
     * @return void
     */
    public function update(Request $request, Todo $todo)
    {
        try {
            if (isset($request->date)) {
                if (strtotime($request->date) === false) {
                    return $this->sendError(config('responseMessages.invalidDate'), [], Response::HTTP_BAD_REQUEST);
                }
                $date = new Carbon(new \DateTime($request->date));
                $todo->date = $date;
            }
            $todo->ip_address = request()->ip();
//            print_r($request->all());
//            die();

            if ($todo->fill($request->all())->save()) {
                return $this->sendResponse($todo, $this->module . ' ' . config('responseMessages.updateSuccess'));
            }
            return $this->sendError(config('responseMessages.updateFail'), [],Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Todo $todo
     * @return void
     */
    public function destroy(Todo $todo)
    {
        try {
            if ($todo->delete()) {
                return $this->sendResponse($todo, $this->module .' '. config('responseMessages.deleteSuccess'));
            } else {
                return $this->sendError(config('responseMessages.deleteFail'), [], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
