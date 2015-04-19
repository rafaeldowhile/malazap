$(function() {

  $('.camp_imagem').hide();
  
  $('#tipo_campanha').change(funcCampanha);

  //////////////////////////////////////////////////////////////////////////////

  function funcCampanha(eventData) {
    var opcao = $('#tipo_campanha option:selected')[0].value;

    if (opcao === 'texto') {
      $('.camp_texto').show();
      $('.camp_imagem').hide();
    } else if (opcao === 'imagem'){
      $('.camp_imagem').show();
      $('.camp_texto').hide();
    }
  };

});
