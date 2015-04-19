@extends('app')

@section('content')

<div class="container">
  <div class="row">
    <div class="col-md-6">
      <form class="form form-horizontal" action="/entregar" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <legend>Configurar mensagem</legend>

        <div class="form-group">
          <label class="col-md-2 control-label">Campanha</label>

          <div class="col-md-10">
            <select class="form-control" name="tipo_companha"  id="tipo_campanha">
              <option value="texto" selected>Mensagem de Texto</option>
              <option value="imagem">Mensagem de Imagem</option>
            </select>
          </div>
        </div>

        <!-- <div class="form-group camp_imagem" style="display: none;">
          <label class="col-md-2 control-label">Arquivo de Imagem</label>
          <div class="col-md-10">
            <input type="file" name="imagem" class="form-control"/>
          </div>
        </div> -->

        <div class="form-group camp_imagem" style="display: none;">
          <label class="col-md-2 control-label">Link Imagem</label>
          <div class="col-md-10">
            <input type="text" name="url_imagem" class="form-control" placeholder="http://www.exemplo.com/img.jpg"/>
          </div>
        </div>

        <div class="form-group camp_texto">
          <label class="col-md-2 control-label">Mensagem</label>
          <div class="col-md-10">
            <textarea name="texto" rows="8" cols="40" class="form-control"></textarea>
          </div>
        </div>

        <div class="col-md-offset-2" style="float: right;">
          <a href="{{ URL::previous() }}" class="btn btn-default btn-lg">Voltar</a>
          <button type="submit" name="Entregar" class="btn btn-primary btn-lg">Enviar <i class="fa fa-paper-plane"></i> </button>
        </div>

      </form>
    </div>

    <div class="col-md-4">
      <legend>Resultado importação</legend>
      Total de telefones: {{ count($telefones) }}

      <div class="col-md-12" style="max-height: 300px;overflow-y: scroll;">
        <table class="table table-hover table-striped">
          <thead>
            <th>
              Número
            </th>
          </thead>
          <tbody>
            @foreach($telefones as $telefone)
            <tr>
              <td>
                {{$telefone}}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

@endsection
