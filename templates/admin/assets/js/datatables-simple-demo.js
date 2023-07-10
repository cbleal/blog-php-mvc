// window.addEventListener('DOMContentLoaded', event => {
//     // Simple-DataTables
//     // https://github.com/fiduswriter/Simple-DataTables/wiki

//     const datatablesSimple = document.getElementById('datatablesSimple');
//     if (datatablesSimple) {
//         new simpleDatatables.DataTable(datatablesSimple);
//     }
// });

$(document).ready(function() {
    $('#tabela_post').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json',
        },
        processing: true,
        serverSide: true,
        // ajax: 'http://localhost/unset-blog/admin/posts/datatable',
        ajax: {
            url: 'http://localhost/unset-blog/admin/posts/datatable',
        },
        columns: [
            { data: 'id' },
            { data: 'titulo' }
        ]
        
    });
});

//let table = new DataTable('#myTable');
