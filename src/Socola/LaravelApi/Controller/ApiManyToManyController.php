<?php
/**
 * Created by PhpStorm.
 * User: Socola
 * Date: 04/09/2018
 * Time: 9:35 PM
 */

namespace Socola\LaravelApi\Controller;


use Illuminate\Http\Request;

class ApiManyToManyController
{
    use BaseApiController;

    public function index(Request $request, $id)
    {
        return $this->_index(collect($request->all()), $id);
    }

    public function _index($params, $id)
    {

    }
}
