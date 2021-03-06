<?php

namespace Socola\LaravelApi\Controller;

use Illuminate\Http\Request;

trait BaseApiController
{
	protected $limit = 25;
	protected $model;
	protected $fields = '*';
	protected $key = 'id';

	public function find($id)
	{
		return $this->model::where($this->key, $id)->first();
	}

	public function limit(Request $request)
	{
		$limit = $request->get('limit', -1);
		switch ($limit) {
			case 0:
				return 0;
			case -1:
				return $this->limit;
			default:
				return $limit;
		}
	}

    public function select(Request $request)
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
}
