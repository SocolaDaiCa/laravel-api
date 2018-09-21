<?php

namespace Socola\LaravelApi\Controller;

use Illuminate\Http\Request;

trait ApiHasManyController
{
	use BaseApiController;
	protected $hasManyModel;

	public function index(Request $request, $id)
	{
		return $this->_index(collect($request->all()), $id);
	}

	public function _index($params, $id)
	{
		$limit = $this->limit($params->get('limit'));
		$records = $this->model::find($id)
			->{$this->hasManyModel}();
		if ($limit === 0) {
			$limit = $records->count();
		}
		return $records->paginate($limit);
	}

	public function store(Request $request, $id)
	{
		return $this->_store(collect($request->all()), $id);
	}

	public function _store($params, $id)
	{
		$this->model::find($id)
			->{$this->hasManyModel}()
			->create($params);
		return $this->responseSuccess('created');
	}
}
