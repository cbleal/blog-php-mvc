$(document).ready(function () {
    // const url_base = 'http://localhost/unset-blog/admin'
    const url_base = $('table').attr('url')

    $.extend( $.fn.dataTable.defaults, {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.3/i18n/pt-BR.json'
        },
    } );

    $('#tabela_categorias').DataTable({
        // paging: false,
        // ordering: false,
        // info: false,
        columnDefs: [
            {
                targets: [2, 3],
                orderable: false,
            },            
        ],
    });

    $('#tabela_usuarios').DataTable({
        // paging: false,
        // ordering: false,
        // info: false,
        columnDefs: [
            {
                targets: [3, 4],
                orderable: false,
            },            
        ],
    });

    $('#tabela_posts').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.3/i18n/pt-BR.json'
        },
        order: [[0, 'desc']],
        processing: true,
        serverSide: true,
        ajax: {
            url: url_base + 'admin/posts/datatable',
            type: 'POST',
        },
        columns: [
            null,
            null,            
            {
                data: null,
                render: function ( data, type, row ) {
                    if (row[2] == 1) {
                        return '<i class="fa-solid fa-circle text-success" tooltip="tooltip" title="Ativo"></i>'
                    } else {
                        return '<i class="fa-solid fa-circle text-danger" tooltip="tooltip" title="Inativo"></i>'
                    }
                }
            },
            {
                data: null,
                render: function ( data, type, row ) {
                    let html = '<a href="'+url_base+'/posts/editar/'+row[0]+'" title="Alterar Registro" class="m-1"><i class="fa-solid fa-pen"></i></a>'
                    html += '<a href="'+url_base+'/posts/deletar/'+row[0]+'" title="Excluir Registro" class="m-1 text-danger"><i class="fa-solid fa-trash"></i></a>'
                    return html
                }
            }
        ],
        columnDefs: [
            {
                targets: [2, 3],
                orderable: false,
            },            
        ],
    });
});
