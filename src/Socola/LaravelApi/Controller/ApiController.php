<?php

namespace Socola\LaravelApi\Controller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

trait ApiController
{
    use BaseApiController;

    public function index()
    {
        return $this->_index();
    }

    protected function _index()
    {
        $this->records()
            ->select($this->indexSelect)
            ->indexQuery()
            ->with($this->indexWith)
            ->withCount($this->indexWithCount)
            ->orderBy($this->orderBy)
            ->paginate($this->limit, $this->indexSelect)
            ;
        return $this->getResourceCollection($this->response);
    }

    public function show($id)
    {
        return $this->_show($id);
    }

    protected function _show($id)
    {
        return $this->modelFind($id, $this->showSelect)
            ->with($this->showWith)
            ->withCount($this->showWithCount)
            ->get();
    }

    public function store(Request $request)
    {
        return $this->_store($this->getValidatedOfRequest($this->storeRequest));
    }

    protected function _store($request)
    {
        $request = collect($request);
        $attributes = $request->except(array_keys($this->relations))->toArray();
        $record = $this->model::query()->create($attributes);
        $this->updateRelations($record, $request);
        return $this->responseSuccess('success');
    }

    public function update(Request $request, $id)
    {
        return $this->_update(collect($this->getValidatedOfRequest($this->updateRequest)), $id);
    }

    protected function _update(Collection $request, $id)
    {
        $record = $this->modelFind($id)->get();
        $record->update($request->except(array_keys($this->relations))->toArray());
        $this->updateRelations($this->modelFind($id)->get(), $request);
        return $this->responseSuccess('success');
    }

    public function destroy($id)
    {
        return $this->_destroy($id);
    }

    protected function _destroy($id)
    {
        $object = $this->modelFind($id)->get();
        if ($object == null) {
            return $this->responseErrors('Target not found.', [], 404);
        }
        $object->delete();
        return $this->responseSuccess('delete success', 204);
    }
}
