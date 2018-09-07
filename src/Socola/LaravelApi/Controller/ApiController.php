<?php

namespace Socola\LaravelApi\Controller;

use Illuminate\Http\Request;

trait ApiController {
	use BaseApiController;

	public function index(Request $request)
	{
		return $this->_index($request);
	}

	public function _index(Request $request)
	{
		$limit = $this->limit($request);
		if($limit === 0) {
			$limit = $this->model::count();
		}
		return $this->model::select($this->select($request))->paginate($limit);
	}

	public function show($id)
	{
		return $this->_show($id);
	}

	public function _show($id)
	{
		return $this->find($id);
	}

	public function store(Request $request)
	{
		$this->model::store($request->all());
	}
}
