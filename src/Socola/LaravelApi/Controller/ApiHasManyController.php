<?php

namespace Socola\LaravelApi\Controller;

use Socola\LaravelApi\Controller\BaseApiController;

trait ApiHasMayController
{
	use BaseApiController;
	protected $hasManyModel;

	public function index(Request $request, $id)
	{
		return $this->_index($request);
	}

	public function _index(Request $request, $id)
	{
		$limit = $this->limit($request);
		$records = $this->model::find($id)
			->{$this->hasManyModel}();
		if ($limit === 0) {
			$limit = $records->count();
		}
		return $records->paginate($limit);
	}

	public function store(Request $request, $id)
	{
		return $this->_store($request, $id);
	}

	public function _store(Request $request, $id)
	{
		$this->model::find($id)
			->{$this->hasManyModel}()
			->create($request->all());
		
	}
}