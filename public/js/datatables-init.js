// =========================================================
// 👥 DataTables: Usuários
// =========================================================
$(document).ready(function() {
    const table = $('#usuariosTable').DataTable({
        responsive: true,

        // ✅ Layout do cabeçalho com busca global e controle de quantidade
        dom: '<"row mb-3"' +
                '<"col-md-4"l>' +  // seletor de quantidade
                '<"col-md-4 text-center"f>' + // 🔍 campo de pesquisa global
                '<"col-md-4 text-end"B>' +    // botões
             '>' +
             'rt' + // tabela
             '<"row mt-3"' +
                '<"col-md-5"i>' +
                '<"col-md-7"p>' +
             '>', 

        buttons: [
            { extend: 'copy', text: 'Copiar' },
            { extend: 'csv', text: 'CSV' },
            { extend: 'excel', text: 'Excel' },
            { extend: 'pdf', text: 'PDF' },
            { extend: 'print', text: 'Imprimir' }
        ],

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Todos"]
        ],

        order: [[1, 'asc']],

        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
            search: "🔍 Buscar:",
            lengthMenu: "Mostrar _MENU_ registros por página",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoFiltered: "(filtrado de _MAX_ registros totais)",
            buttons: {
                copyTitle: 'Copiado!',
                copySuccess: { _: '%d linhas copiadas', 1: '1 linha copiada' }
            }
        }
    });

    // 🔍 Filtros individuais nas colunas
    $('#usuariosTable tfoot th').each(function (i) {
        const input = $(this).find('input, select');
        if (input.length) {
            $(input).on('keyup change', function () {
                if (table.column(i).search() !== this.value) {
                    table.column(i).search(this.value).draw();
                }
            });
        }
    });
});

// =========================================================
// 🏫 DataTables: Escolas e Secretarias
// =========================================================
$(document).ready(function() {
  const table2 = $('#escolasTable').DataTable({
    responsive: true,
    dom: '<"row mb-3"' +
            '<"col-md-4"l>' +
            '<"col-md-4 text-center"f>' +
            '<"col-md-4 text-end"B>' +
         '>' +
         'rt' +
         '<"row mt-3"' +
            '<"col-md-5"i>' +
            '<"col-md-7"p>' +
         '>',
    buttons: [
      { extend: 'copy', text: 'Copiar' },
      { extend: 'csv', text: 'CSV' },
      { extend: 'excel', text: 'Excel' },
      { extend: 'pdf', text: 'PDF' },
      { extend: 'print', text: 'Imprimir' }
    ],
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "Todos"]
    ],
    order: [[1, 'asc']],
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
      search: "🔍 Buscar:",
      lengthMenu: "Mostrar _MENU_ registros por página",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoFiltered: "(filtrado de _MAX_ registros totais)"
    }
  });

  // 🔍 filtros individuais
  $('#escolasTable tfoot th').each(function (i) {
    const input = $(this).find('input, select');
    if (input.length) {
      $(input).on('keyup change', function () {
        if (table2.column(i).search() !== this.value) {
          table2.column(i).search(this.value).draw();
        }
      });
    }
  });
});


// =========================================================
// 🧩 DataTables: Associações Escola ↔ Filhas
// =========================================================
$(document).ready(function() {
  const table3 = $('#associacoesTable').DataTable({
    responsive: true,
    dom: '<"row mb-3"' +
            '<"col-md-4"l>' +
            '<"col-md-4 text-center"f>' +
            '<"col-md-4 text-end"B>' +
         '>' +
         'rt' +
         '<"row mt-3"' +
            '<"col-md-5"i>' +
            '<"col-md-7"p>' +
         '>',
    buttons: [
      { extend: 'copy', text: 'Copiar' },
      { extend: 'csv', text: 'CSV' },
      { extend: 'excel', text: 'Excel' },
      { extend: 'pdf', text: 'PDF' },
      { extend: 'print', text: 'Imprimir' }
    ],
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "Todos"]
    ],
    order: [[1, 'asc']],
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
      search: "🔍 Buscar:",
      lengthMenu: "Mostrar _MENU_ registros por página",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoFiltered: "(filtrado de _MAX_ registros totais)"
    }
  });

  // 🔍 filtros individuais nas colunas
  $('#associacoesTable tfoot th').each(function (i) {
    const input = $(this).find('input, select');
    if (input.length) {
      $(input).on('keyup change', function () {
        if (table3.column(i).search() !== this.value) {
          table3.column(i).search(this.value).draw();
        }
      });
    }
  });
});

/*
// =========================================================
// 🧩 DataTables: Associações Escola ↔ Filhas
// =========================================================
$(document).ready(function() {
  const table3 = $('#associacoesTable').DataTable({
    responsive: true,
    dom: '<"row mb-3"' +
            '<"col-md-4"l>' +
            '<"col-md-4 text-center"f>' +
            '<"col-md-4 text-end"B>' +
         '>' +
         'rt' +
         '<"row mt-3"' +
            '<"col-md-5"i>' +
            '<"col-md-7"p>' +
         '>',
    buttons: [
      { extend: 'copy', text: 'Copiar' },
      { extend: 'csv', text: 'CSV' },
      { extend: 'excel', text: 'Excel' },
      { extend: 'pdf', text: 'PDF' },
      { extend: 'print', text: 'Imprimir' }
    ],
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "Todos"]
    ],
    order: [[1, 'asc']],
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/pt-BR.json',
      search: "🔍 Buscar:",
      lengthMenu: "Mostrar _MENU_ registros por página",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoFiltered: "(filtrado de _MAX_ registros totais)"
    }
  });

  // 🔍 filtros individuais nas colunas
  $('#associacoesTable tfoot th').each(function (i) {
    const input = $(this).find('input, select');
    if (input.length) {
      $(input).on('keyup change', function () {
        if (table3.column(i).search() !== this.value) {
          table3.column(i).search(this.value).draw();
        }
      });
    }
  });
});
*/