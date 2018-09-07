<?php

namespace Socola\LaravelApi\Controller;

use Illuminate\Http\Request;

trait BaseApiController
{
	protected $limit = 25;
	protected $model;
	protected $fields = '*';

	public function find($id)
	{
		return $this->model::find($id);
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

    public function select($fi)
    {
        $fields = explode(',', $request->get('fields'));
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
}
