<?php

namespace Socola\LaravelApi\Controller;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait BaseApiController
{
    /**
     * @var Model
     */
    protected $model;

    protected $limit = 25;

    protected $modelFind = 'find';

    protected $orderBy = [];

    protected $relations = [];

    /**
     * @var Builder
     */
    protected $response;
    /* resource */
    protected $resource;
    protected $resourceCollection;

    /* index */
    protected $indexSelectable = ['*'];
    protected $indexSelect = ['*'];

    protected $indexWith   = [];

    protected $indexAppends = [];

    protected $indexWithCount = [];

    /* show */
    protected $showWith    = [];

    protected $showSelect  = ['*'];

    protected $showAppends = [];

    protected $showWithCount = [];

    /**
     * @var FormRequest
     */
    protected $storeRequest;
    /**
     * @var FormRequest
     */
    protected $updateRequest;

    public function modelFind($id, $fields = ['*'])
    {
        $this->response = $this->model::query()->select($fields)->{$this->modelFind}($id);
        return $this;
    }

    public function records()
    {
        $this->response = $this->model::query();
        return $this;
    }

    public function indexQuery()
    {
        return $this;
    }

    public function paginate($limitable = 25, $columns = ['*'])
    {
        $limit = \request('limit', 0);
        if($limitable == 0 && $limit == 0) {
            $limit = $this->response->count();
        } else {
            $limit = min($limit, $limitable);
        }
        $this->response = $this->response->paginate($limit, $columns);
        return $this;
	}

    public function load(array $relationsable)
    {
        $relations = $this->commaToArray(\request('with', ''));
        $this->response->load($this->intersect($relations, $relationsable));
        return $this;
    }

    public function with(array $relationsable)
    {
        $relations = $this->commaToArray(\request('with', ''));
        $this->response->with($this->intersect($relations, $relationsable));
        return $this;
	}

    public function withCount(array $relationsable)
    {
        $relations = $this->commaToArray(\request('withCount', ''));
        $this->response->withCount($this->intersect($relations, $relationsable));
        return $this;
	}

    public function orderBy(array $columns)
    {
        $columns = $this->commaToArray(\request('order_by', '')) ?: $columns;
        foreach ($columns as $column) {
            if($column == '') {
                continue;
            }
            $order = ($column[0] == '-') ? 'DESC' : 'ASC';
            $this->response->orderBy(trim($column, '-'), $order);
        }
        return $this;
	}

    public function intersect(array $fields, array $fieldsable)
    {
        if(empty($fields)) {
            return $fieldsable;
        }

        if(count($fieldsable) == 1 && $fieldsable[0] == '*') {
            return $fields;
        }

        return array_intersect($fieldsable, $fields);
	}

    public function commaToArray($string)
    {
        if(empty($string)) {
            return [];
        }
        $arr = explode(',', $string);
        if($arr[0] == '') {
            array_shift($arr);
        }
        return $arr;
    }
    public function select(array $fieldsable)
    {
        $fields = $this->commaToArray(\request('fields', ''));
        $this->response->select($this->intersect($fields, $fieldsable));
        return $this;
	}

    /**
     * @param string $message
     * @param array $errors
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseErrors(string $message, array $errors, int $status)
    {
        $message = $message ?: $message = 'something wrong';
        $errors = array_wrap($errors);
        return response()->json([
            'message' => $message,
            'errors'  => array_map('array_wrap', $errors),
            'status'  => $status,
        ], $status);
    }

    /* đã test */

    /**
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseSuccess(string $message, $status = 200)
    {
        return response()->json([
            'message' => $message,
            'status'  => $status,
        ], $status);
    }

    /**
     * @param Builder $records
     * @return Builder
     */
    public function getResource(Builder $records)
    {
        if(!empty($this->resourceCollection)) {
            return new $this->resourceCollection($records);
        }
        return $records;
    }

    /**
     * @param LengthAwarePaginator $records
     * @return LengthAwarePaginator
     */
    public function getResourceCollection(LengthAwarePaginator $records)
    {
        if(!empty($this->resource)) {
            return new $this->resource($records);
        }
        return $records;
    }

    /**
     * @param Builder $records
     * @return Builder
     */
    public function sort(Builder $records)
    {
        foreach ($this->orderBy as $column) {
            $order = ($column[0] == '-') ? 'DESC' : 'ASC';
            $records->orderBy(trim($column, '-'), $order);
        }
        return $records;
    }

    /**
     * @return Builder | LengthAwarePaginator
     */
    public function get()
    {
        return $this->response;
    }

    public function updateRelations($record, Collection $request)
    {
        foreach ($this->relations as $model => $relation) {
            if (!$request->has($model)) {
                continue;
            }
            $record->{$model}()->{$relation}($request->get($model));
        }
    }

    public function findBy($id, string $by)
    {
        $this->response = $this->response->{$by}($id);
        return $this;
    }

    public function query()
    {
        $this->response = new $this->model();
        return $this;
    }

    public function getValidatedOfRequest($requestClass) {
        if(empty($requestClass)) {
            return \request()->all();
        }
        $updateRequest = app($requestClass);
        $updateRequest->validateResolved();
        return $updateRequest->validated();
    }
}
