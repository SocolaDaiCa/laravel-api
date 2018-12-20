<?php
/**
 * Created by PhpStorm.
 * User: Socola
 * Date: 28/09/2018
 * Time: 1:42 PM
 */

namespace Socola\LaravelApi\Controller;


use Illuminate\Http\Request;

trait ApiRelationshipController
{
    use BaseApiController;

    protected $relasionshipModelFind = 'find';
    protected $relasionshipModel;
    protected $modelId;

    protected $delete = 'delete';
    protected $store  = 'store';
    protected $create = 'create';
    protected $update = 'update';

    /**
     * @param Request $request
     * @param $modelId
     * @return mixed
     */
    public function index(Request $request, $modelId = null)
    {
        $modelId = $modelId ?? $this->modelId;
        return $this->_index($request->all(), $modelId);
    }

    public function _index($request, $modelId = null)
    {
        $modelId = $modelId ?? $this->modelId;
        $request = collect($request);
        $records = $this->relationshipModel($modelId);
        $limit   = $request->get('limit', $this->limit) ?: $records->count();
        return $this->sort($records)
            ->select($this->indexSelect)
            ->withCount($this->indexWithCount)
            ->with($this->indexWith)
            ->paginate($limit, $this->indexWith)
            ;
    }

    /**
     * @param Request $request
     * @param $modelId
     * @param $relationshipModelId
     * @return mixed
     */
    public function show(Request $request, $modelId, $relationshipModelId = null)
    {
        if($relationshipModelId == null) {
            $relationshipModelId = $modelId;
            $modelId = $this->modelId;
        }
        return $this->_show($request->all(), $modelId, $relationshipModelId);
    }

    public function _show($request, $modelId, $relationshipModelId)
    {
        $request = collect($request);
        return $this->relationshipModelFind($modelId, $relationshipModelId)
            ->load($this->showWith);
    }

    /**
     * @param Request $request
     * @param $modelId
     * @param $relationshipModel
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $modelId = null)
    {
        $modelId = $modelId ?? $this->modelId;
        return $this->_store($request->all(), $modelId);
    }

    public function _store($request, $modelId)
    {
        $request = collect($request);
        $relasionshipFields = $request->except(array_keys($this->relasionships));
        $record = $this->relationshipModel($modelId)
            ->{$this->store}($request->except($relasionshipFields));

        foreach ($this->relasionships as $field => $type) {
            if(!$request->has($field)){
                continue;
            }
            $record->{$field}()->{$type}($request->get($field));
        }
        return $this->responseSuccess('', 204);
    }

    /**
     * @param Request $request
     * @param $modelId
     * @param $relationshipModelId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $modelId, $relationshipModelId = null)
    {
        if($relationshipModelId == null) {
            $relationshipModelId = $modelId;
            $modelId = $this->modelId;
        }
        return $this->_update($request->all(), $modelId, $relationshipModelId);
    }

    public function _update($request, $modelId, $relationshipModelId)
    {
        $request = collect($request);
        $record = $this->relationshipModelFind($modelId, $relationshipModelId);

        $relasionshipFields = $request->except(array_keys($this->relasionships));
        $record->store($request->except($relasionshipFields));

        foreach ($this->relasionships as $field => $type) {
            if(!$request->has($field)){
                continue;
            }
            $record->{$field}()->{$type}($request->get($field));
        }
        return $this->responseSuccess('', 204);
    }

    /**
     * @param $modelId
     * @param $relationshipModelId
     * @return mixed
     */
    public function destroy($modelId, $relationshipModelId = null)
    {
        $relationshipModelId = $relationshipModelId ?? $modelId;
        $modelId = $modelId ?? $this->modelId;
        return $this->_destroy($modelId, $relationshipModelId, 'delete');
    }

    public function _destroy($request, $modelId, $relationshipModelId, $str)
    {
        $request = collect($request);
        if($this->delete == $str) {
            $this->relationshipModelFind($modelId, $relationshipModelId)
                ->delete($request->all());
        } else {
            $this->relationshipModel($modelId)
                ->{$this->delete}($request->all());
        }
        return $this->responseSuccess('', 204);
    }

    public function relationshipModel($modelId)
    {
        return $this->modelFind($modelId)->{$this->relasionshipModel}();
    }

    public function relationshipModelFind($modelId, $relationshipModelId)
    {
        return $this->relationshipModel($modelId)
            ->{$this->relasionshipModelFind}($relationshipModelId);
    }
}
