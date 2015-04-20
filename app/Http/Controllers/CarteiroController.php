<?php namespace App\Http\Controllers;

use Auth;
use Cache;
use Input;
use WA;
use Config;

class CarteiroController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function entregar() {

		$id        = Auth::user()->id;
		$campanha  = Input::get('tipo_campanha');
		$telefones = Cache::get('contatos_' . $id);
		$mensagem  = Input::get('texto');
		$imagem    = Input::get('imagem');

		if ($campanha == 'texto') {
			foreach($telefones as $telefone) {
				WA::sendMessage(strval($telefone), $mensagem);
			}
		} else {
			foreach($telefones as $telefone) {
				WA::sendMessageImage(strval($telefone), $imagem);
			}
		}


		return redirect('/home');
	}

	public function teste() {

		WA::sendMessage('555181612772', 'Teste');

		return response()->json('sucesso');
	}


}
