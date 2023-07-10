$('.formulario').submit(function(event) {
    event.preventDefault();

    const carregando = $('.ajaxLoading');
    const botao = $(':input[type="submit"]');

    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function () {
            carregando.show().fadeIn(200);
            botao.prop('disable', 'false').addClass('disabled');
        },
        success: function(retorno) {
            if (retorno.erro) {
                // alert(retorno.erro);
                alerta(retorno.erro, 'yellow');
            }

            if (retorno.sucesso) {
                //limpar campos do formulario
                $('.formulario')[0].reset();
                //fechar a modal
                $('#contatoModal').modal('hide');
                //mostra a mensagem de alerta
                alerta(retorno.sucesso, 'green');
            }

            if (retorno.redirecionar) {
                window.location.href = retorno.redirecionar;
            }
        },
        complete: function () {
            carregando.hide().fadeOut(200);
            botao.removeClass('disabled');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR, textStatus, errorThrown);
        }
    });
});

function alerta(mensagem, cor) {
    new jBox('Notice', {
        content: mensagem,
        color: cor,
        animation: 'pulse',
        showCountdown: true,
      });
}