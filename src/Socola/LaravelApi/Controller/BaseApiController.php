<?php

namespace Socola\LaravelApi\Controller;

trait BaseApiController
{
	protected $limit;
	protected $model;

	public function find($id)
	{
		return $this->model::find($id);
	}

	public function limit(Request $request)
	{
		$limit = $request->get('limit');
		switch ($limit) {
			case 0:
				return 0;
			case null:
				return $this->limit;
			default:
				return $limit;
		}
	}
}