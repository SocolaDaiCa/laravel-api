<?php

namespace Socola\LaravelApi\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait ApiController
{
    use BaseApiController;

    public function index()
    {
        return $this->_index();
    }

    public function _index()
    {
        $paginate = $this->records()
            ->select($this->indexSelect)
            ->with($this->indexWith)
            ->withCount($this->indexWithCount)
            ->orderBy($this->orderBy)
            ->paginate($this->limit, $this->indexSelect)
            ;
        return $this->getResourceCollection($paginate->get());
    }

    public function show($id)
    {
        return $this->_show($id);
    }

    public function _show($id)
    {
        return $this->modelFind($id, $this->showSelect)
            ->with($this->showWith)
            ->withCount($this->showWithCount)
            ->get();
    }

    public function store(Request $request)
    {
        if (!empty($this->storeRequest)) {
            $storeRequest = new $this->storeRequest();
            $request->validate($storeRequest->rules());
        }
        return $this->_store($request->all());
    }

    public function _store($request)
    {
        $request = collect($request);
        $attributes = $request->except(array_keys($this->relations))->all();
        $record = $this->model::query()->create($attributes);
        $this->updateRelations($record, $request);
        return $this->responseSuccess('success');
    }

    public function update(Request $request, $id)
    {
        if (!empty($this->updateRequest)) {
            $updateRequest = new $this->updateRequest();
            $request->validate($updateRequest->rules());
        }
        return $this->_update(collect($request->all()), $id);
    }

    public function _update(Collection $request, $id)
    {
        $this->updateRelations($this->modelFind($id)->get(), $request);
        return $this->responseSuccess('success');
    }

    public function destroy($id)
    {
        return $this->_destroy($id);
    }

    public function _destroy($id)
    {
        $this->modelFind($id)->get()->delete();
        return $this->responseSuccess('delete success', 204);
    }
}
