<?php namespace App\Http\Controllers;

use Carbon;
use Auth;
use Excel;
use Input;
use Cache;

class UploadController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

  public function upload() {
   $arquivo = Input::file('arquivo');

   $telefones = array();

   \Excel::load($arquivo, function($reader) {
     $id = Auth::user()->id;
     $telefones = array();
     $linhas = $reader->get(array('contato'));
     foreach($linhas as $resultado) {
       $telefones[] = $resultado->contato;
     };
     Cache::put('contatos_' . $id, $telefones, 100);
   });
   $id = Auth::user()->id;

   $telefones = Cache::get('contatos_' . $id);

   return view('configurar', array('telefones' => $telefones));

  }

}
