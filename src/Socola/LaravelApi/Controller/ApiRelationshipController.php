<?php
/**
 * Created by PhpStorm.
 * User: Socola
 * Date: 28/09/2018
 * Time: 1:42 PM
 */

namespace Socola\LaravelApi\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait ApiRelationshipController
{
    use BaseApiController;

    protected $relationFind = 'find';

    protected $relation;
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
        return $this->_index(collect($request->all()), $modelId);
    }

    public function relation()
    {
        $this->response->{$this->relation}();
        return $this;
    }

    public function _index($modelId = null)
    {
        return $this->modelFind($modelId ?: $this->modelId)
            ->relation()
            ->with($this->indexWith)
            ->withCount($this->indexWithCount)
            ->orderBy($this->orderBy)
            ->paginate($this->limit)->get();
    }

    public function show($modelId, $relationId = null)
    {
        return $this->_show($modelId, $relationId);
    }

    public function _show($modelId, $relationId)
    {
        if(empty($relationId)) {
            $relationId = $modelId;
            $modelId = $this->modelId;
        }
        return $this->query()
            ->findBy($modelId, $this->modelFind)
            ->relation()
            ->select($this->showSelect)
            ->findBy($relationId, $this->relationFind)
            ->with($this->showWith)
            ->withCount($this->showWithCount)
            ->get();
    }

    public function store(Request $request, $modelId = null)
    {
        $modelId = $modelId ?? $this->modelId;
        return $this->_store(collect($request->all()), $modelId);
    }

    public function _store(Collection $request, $modelId = null)
    {
        $record = $this->query()
            ->findBy($modelId ?: $this->model, $this->modelFind)
            ->response
            ->create($request->except(array_keys($this->relations))->toArray());
        $this->updateRelations($record, $request);
        return $this->responseSuccess('', 204);
    }

    public function update(Request $request, $modelId, $relationId = null)
    {
        return $this->_update(collect($request->all()), $modelId, $relationId);
    }

    public function _update(Collection $request, $modelId, $relationId = null)
    {
        if(empty($relationId)) {
            $relationId = $modelId;
            $modelId = $this->modelId;
        }
        $record = $this->query()
            ->findBy($modelId, $this->modelFind)
            ->relation()
            ->findBy($relationId, $relationId)
            ->response
            ->update($request->except(array_keys($this->relations))->toArray());
        $this->updateRelations($record, $request);
        return $this->responseSuccess('', 204);
    }

    /**
     * @param $modelId
     * @param $relationshipModelId
     * @return mixed
     */
    public function destroy($modelId, $relationId = null)
    {
        return $this->_destroy($modelId, $relationId);
    }

    public function _destroy($modelId, $relationId = null)
    {
        if(empty($relationId)) {
            $relationId = $modelId;
            $modelId = $this->modelId;
        }
        $this->query()
            ->findBy($modelId, $this->modelFind)
            ->relation()
            ->findBy($relationId, $this->relationFind)
            ->response
            ->delete();
        return $this->responseSuccess('', 204);
    }
}
