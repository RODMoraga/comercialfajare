const btntablerefresh = document.querySelector("#btntablerefresh");

let table;

/**
 * Mostrar en la lista cantidad de productos vendidos
 * 
 * @param {Array} customers Id's de clientes
 * @param {Array} products Id's de products
 * @param {string} datestart Fecha de inicio proceso
 * @param {string} dateend Fecha Termino Proceso
 */
const findAll = async (customers, products, datestart, dateend) => {
    table = $('#tblproductsummary').dataTable({
        "lengthMenu": [10, 25, 75, 100],    // Mostramos el menú de registros a revisar
        "aProcessing": true,                // Activamos el procesamiento del datatables
        "aServerSide": true,                // Paginación y filtrado realizados por el servidor
        dom: '<Bl<f>rtip>',                 // Definimos los elementos del control de tabla
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdf'
        ],
        "columnDefs": [
            //{ "targets":[3], render: $.fn.dataTable.render.number('.', ',', 0, '$'), "className": "dt-right" },
            //{ "targets":[5, 6, 7], "className": "dt-right" },
            { "targets":[3], render: $.fn.dataTable.render.number('.', ',', 0, ''), "className": "dt-right" },
            { "targets":[0, 3], "width": "14%" },
            { "targets":[0, 1], "orderable": false }
        ],
        "rowCallback": function(row, data, index) {
            /*if (data[4] == 5) {
                $(row).find('td:eq(4)').css('color', 'red');
            }*/
        },
        "ajax": {
            url: `/report/productsalessummary?customers=${customers}&products=${products}&datestart=${datestart}&dateend=${dateend}`,
            type: "GET",
            dataType: "JSON",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "language": {
            "lengthMenu": "Mostrar : _MENU_ registros",
            "buttons": {
                "copyTitle": "Tabla Copiada",
                "copySuccess": {
                    _: '%d líneas copiadas',
                    1: '1 línea copiada'
                }
            }
        },
        "bDestroy": true,
        "iDisplayLength": 10,   // Paginación
        "order": [[3, "desc"], [2, "asc"]]   // Ordenar (columna, orden)
    }).DataTable();
}

/**
 * Obtener todos los clientes de la base de datos
 */
const findAllCustomer = async () => {
    try {
        const response = await fetch(`/report/findallcustomer`, {
            method: "GET",
            headers: { "Content-Type": "Application/json" }
        });

        if (!response.ok) {
            Swal.fire({
                title: `Error: ${response.status}`,
                html: `<span style="font-size: 1.4rem;">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();
            
            let opt = ``;

            Object.entries(data).forEach((value) => {
                opt += `
                    <option value="${value[1][0]}">${value[1][1]}</option>
                `;
            });

            $("#establishment").html(opt);
            $("#establishment").selectpicker("refresh")
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.4rem;">${error.message}</span>`,
            icon: "error"
        });
    }
}

/**
 * Ver transacciones por productos
 * 
 * @param {string} datestart Fecha inicio proceso
 * @param {string} dateend Fecha termino proceso
 * @param {number} productid Id. del producto
 */
const viewProduct = async (datestart, dateend, productid) => {
    $("#tbltransaction").dataTable({
        "lengthMenu": [5, 10],
        "aProcessing": true,                    // Activamos el procesamiento del datatables
        "aServerSide": true,                    // Paginación y filtrado realizados por el servidor
        "columnDefs": [
            { "targets":[2], "width": "50%" },
            { "targets":[0, 1, 3], "className": "dt-right" },
            { "targets":[1, 2, 3], "orderable": false }
        ],
        "ajax": {
            url: `/report/viewproduct?productid=${productid}&datestart=${datestart}&dateend=${dateend}`,
            type: "GET",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "language": {
            "lengthMenu": "Mostrar : _MENU_ registros",
            "buttons": {
                "copyTitle": "Tabla Copiada",
                "copySuccess": {
                    _: '%d líneas copiadas',
                    1: '1 línea copiada'
                }
            }
        },
        "bDestroy": true,
        "iDisplayLength": 10,                // Paginación
        "order": [[0, "asc"]]   // Ordenar (columna, orden)
    }).DataTable();
}

/**
 * Obtener todos los products de la base de datos
 */
const findAllProduct = async () => {
    try {
        const response = await fetch(`/report/findallproduct`, {
            method: "GET",
            headers: { "Content-Type": "Application/json" }
        });

        if (!response.ok) {
            Swal.fire({
                title: `Error: ${response.status}`,
                html: `<span style="font-size: 1.4rem;">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();
            
            let opt = ``;

            Object.entries(data).forEach((value) => {
                opt += `
                    <option value="${value[1][0]}">${value[1][1]}</option>
                `;
            });

            $("#productname").html(opt);
            $("#productname").selectpicker("refresh")
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.4rem;">${error.message}</span>`,
            icon: "error"
        });
    }
}

btntablerefresh.addEventListener("click", () => {
    const datestart = $("#datestart").val();
    const dateend   = $("#dateend").val();
    const customers = $("#establishment").val();
    const products  = $("#productname").val();

    findAll(customers, products, datestart, dateend);
});

window.addEventListener("DOMContentLoaded", async (e) => {
    e.preventDefault();

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    const datestart = $("#datestart").val();
    const dateend   = $("#dateend").val();
    const customers = $("#establishment").val();
    const products  = $("#productname").val();

    window.setTimeout(() => {
        findAllCustomer();
        findAllProduct();

        window.setTimeout(() => {
            findAll(customers, products, datestart, dateend);
        }, 500);
    }, 1000);

    // Botón ver de la lista
    $(document).on("click", ".btn-product-views", function() {
        const productid = $(this).attr("data-toggle-views");
        const productnm = $(this).attr("data-toggle-name");
        const datestart = $("#datestart").val();
        const dateend   = $("#dateend").val();

        viewProduct(datestart, dateend, productid);

        $("#modalProductTransactionLabel").text("Transacciones: " + productnm);
        $("#modalProductTransaction").modal("show")                // initializes and invokes show immediately
    });
});