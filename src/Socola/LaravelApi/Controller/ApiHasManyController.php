<?php

namespace Socola\LaravelApi\Controller;

use Illuminate\Http\Request;

trait ApiHasManyController
{
	use ApiRelationshipController;
	protected $hasManyModel;

	public function store(Request $request, $id)
	{
		return $this->_store(collect($request->all()), $id);
	}

	public function _store($params, $id)
	{
		$this->find($id)
			->{$this->hasManyModel}()
			->create($params);
		return $this->responseSuccess('created');
	}
}
