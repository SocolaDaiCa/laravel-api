<?php
/**
 * Created by PhpStorm.
 * User: Socola
 * Date: 14/09/2018
 * Time: 2:02 PM
 */

namespace Socola\LaravelApi\Controller;


use Illuminate\Support\Facades\Request;

trait ApiHasOneController
{
    use BaseApiController;
    protected $hasOneModel;

    public function index(Request $request, $id)
    {
        return $this->_index(collect($request->all()));
    }

    public function _index($params)
    {
        
    }
}
