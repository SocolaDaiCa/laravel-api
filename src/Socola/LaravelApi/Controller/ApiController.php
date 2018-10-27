<?php

namespace Socola\LaravelApi\Controller;

use Illuminate\Http\Request;

trait ApiController
{
    use BaseApiController;

    public function index(Request $request)
    {
        return $this->_index($request);
    }

    public function _index($request)
    {
        $request = collect($request);
        $fields = $request->get('fields', $this->indexSelect);
        $records = $this->model::select($fields);

        $limit = $request->get('limit', $this->limit) ?: $records->count();
        $records->with($this->indexWith);
        $paginate = $this->sort($records)->paginate($limit);
        return $this->getResourceCollection($paginate);
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
        if (!empty($this->storeRequest)) {
            $storeRequest = new $this->storeRequest();
            $request->validate($storeRequest->rules());
        }
        return $this->_store($request->all());
    }

    public function _store($request)
    {
        $request = collect($request);
        $attributes = $request->except(array_keys($this->relasionships))->all();
        $record = $this->model::create($attributes);
        foreach ($this->relasionships as $model => $relasionship) {
            if (!$request->has($model)) {
                continue;
            }
            $record->{$model}()->{$relasionship}($request->get($model));
        }
        return $this->responseSuccess('success');
    }

    public function update(Request $request, $id)
    {
        if (!empty($this->updateRequest)) {
            $updateRequest = new $this->updateRequest();
            $request->validate($updateRequest->rules());
        }
        return $this->_update($request->all(), $id);
    }

    public function _update($params, $id)
    {
        $params = collect($params);

        $record = $this->find($id);
        $record->update($params->except(array_keys($this->relasionships)));
        foreach ($this->relasionships as $model => $relasionship) {
            $record->{$model}()->{$relasionship}($params->get($model));
        }
        return $this->responseSuccess('success');
    }

    public function destroy($id)
    {
        return $this->_destroy($id, 'delete');
    }

    public function _destroy($id)
    {
        $this->find($id)->delete();
        return $this->responseSuccess('delete success', 204);
    }
}
