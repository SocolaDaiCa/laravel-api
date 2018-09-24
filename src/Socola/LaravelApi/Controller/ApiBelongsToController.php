<?php
/**
 * Created by PhpStorm.
 * User: Socola
 * Date: 24/09/2018
 * Time: 10:10 AM
 */

namespace Socola\LaravelApi\Controller;


use Illuminate\Http\Request;

class ApiBelongsToController
{
    use BaseApiController;

    protected $belongsToModel;

    public function index(Request $request, $id)
    {
        return $this->_index(collect($request->all()), $id);
    }

    public function _index($request, $id)
    {
        return $this->find($id)->{$this->belongsToModel}();
    }


}
