const form = document.querySelector("form");
const btnrefresh = document.querySelector("#btnrefresh");

/**
 * Mostrar en la lista los documentos pendientes
 * 
 * @param {Array} customers Id's de clientes
 * @param {string} datestart Fecha de inicio proceso
 * @param {string} dateend Fecha Termino Proceso
 */
const findAll = async (customers, datestart, dateend) => {
    table = $('#tblcanceled').dataTable({
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
            { "targets":[1, 2, 3, 4, 5], "className": "dt-right" },
            { "targets":[7], render: $.fn.dataTable.render.number('.', ',', 0, ''), "className": "dt-right" },
            { "targets":[0, 1, 2, 3, 4, 5, 7, 8], "width": "8%" },
            { "targets":[0, 5, 6, 7, 8], "orderable": false }
        ],
        "ajax": {
            url: `/report/canceled?customers=${customers}&datestart=${datestart}&dateend=${dateend}`,
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
        "order": [[4, "desc"], [1, "asc"], [2, "asc"], [3, "asc"]]  // Ordenar (columna, orden)
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
 * Este médoto permite obtener los toales de los documentos cancelados, cantidad de documentos y clientes
 * 
 * @param {Array} customers Id's de los clientes seleccionados
 * @param {string} datestart Fecha inicio de proceso
 * @param {string} dateend Fecha termino de proceso
 */
const getTotales = async (customers, datestart, dateend) => {
    try {
        const response = await fetch(`/report/canceledtotal?customers=${customers}&datestart=${datestart}&dateend=${dateend}`, {
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
            
            $("#total").text(Number(data.total).toLocaleString());
            $("#quantity").text(Number(data.items).toLocaleString());
            $("#customers").text(Number(data.customers).toLocaleString());
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.4rem;">${error.message}</span>`,
            icon: "error"
        });
    }
}

btnrefresh.addEventListener("click", () => {
    const customers = $("#establishment").val();
    const datestart = $("#datestart").val();
    const dateend   = $("#dateend").val();

    window.setTimeout(() => {
        findAll(customers, datestart, dateend);

        window.setTimeout(() => {
            getTotales(customers, datestart, dateend);

        }, 500);

    }, 250);
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

    window.setTimeout(() => {
        findAllCustomer();

        window.setTimeout(() => {
            findAll(customers, datestart, dateend);
            getTotales(customers, datestart, dateend);

        }, 750);

    }, 250);
});