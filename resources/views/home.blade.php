@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Importar contatos</div>

				<div class="panel-body">
					<form class="form-horizontal text-center" action="/upload" enctype="multipart/form-data" method="POST">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<p>
							Importe um arquivo XLS com uma coluna chamada CONTATO e os numéros de telefone com o seguinte padrao: 555191154551.
						</p>
						<div class="form-group row">
							<div class="col-md-6 col-md-offset-3">
								<input type="file" name="arquivo" class="form-control input-lg"/>
							</div>
						</div>
						<div class="form-group row">
							<button type="submit" class="btn btn-primary btn-lg">Enviar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
