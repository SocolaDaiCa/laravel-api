<?php

namespace Socola\LaravelApi\Controller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait BaseApiController
{
	protected $limit = 25;

	protected $fields      = '*';
	protected $indexWith   = [];
	protected $showWith    = [];
	protected $indexSelect = '*';
	protected $showSelect  = '*';

    protected $model;
	protected $modelFind = 'find';
	protected $resource;
	protected $resourceCollection;

	protected $relasionships = [
	    /* 'cars' => 'sync' */
    ];

	/* Request */
    protected $storeRequest;
    protected $updateRequest;

	protected $resources = [
	    'index'   => null,
        'store'   => null,
        'update'  => null,
        'destroy' => null,
    ];

    public function find($id)
    {
        return $this->modelFind($id);
    }
    public function modelFind($id)
    {
        return $this->model::{$this->modelFind}($id);
	}

    public function paginate($records, $limit)
    {
        return $records->paginate($this->limit($limit));
	}

	public function limit($limit)
	{
		switch ($limit) {
			case 0:
				return 0;
			case null:
				return $this->limit;
			default:
				return $limit;
		}
	}

    public function select($fields)
    {
        $fields = explode(',', $fields);
        $fields = array_filter($fields, function($item) {
            return $item != '';
        });
        if(!empty($fields)) {
            return $fields;
        }
        return $this->fields;
	}
    public function responseErrors($message, $errors, $status)
    {
        $message = $message ?: $message = 'something wrong';
        $errors = array_wrap($errors);
        return response()->json([
            'message' => $message,
            'errors'  => array_map('array_wrap', $errors),
            'status'  => $status,
        ], $status);
    }

    public function responseSuccess($message, $status = 200)
    {
        return response()->json([
            'message' => $message,
            'status'  => $status,
        ], $status);
    }


    public function getResource($records)
    {
        if(!empty($this->resourceCollection)) {
            return new $this->resourceCollection($records);
        }
        return $records;
    }

    public function getResourceCollection($records)
    {
        if(!empty($this->resource)) {
            return new $this->resource($records);
        }
        return $records;
    }
}
