<?php namespace Modules\Izcore\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class IzCoreController extends Controller {
	
	public function index()
	{
		return view('izcore::index');
	}
	
}