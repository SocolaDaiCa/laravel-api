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
	    $records = $this->model::select($request->get('fields', $this->fields));

	    $limit = $request->get('limit', $this->limit) ?: $records->count();
	    $records->load($this->indexWith);
	    $paginate = $records->paginate($limit);
		return $paginate;
	}

	public function show($id)
	{
		return $this->_show($id);
	}

	public function _show($id)
	{
		return $this->find($id)->load($this->showWith);
	}

	public function store(Request $request)
	{
	    return $this->_store($request->all());
	}

    public function _store($params)
    {
        try{
            $this->model::create($params);
        } catch (\Exception $e) {
            return $this->responseErrors('store fail', $e->getTrace(), 500);
        }
	}
}
