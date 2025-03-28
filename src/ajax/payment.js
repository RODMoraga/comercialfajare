const form = document.querySelector("form");
const payment = document.querySelector("#payment");
const btnrefresh = document.querySelector("#btnrefresh");
const methodpayment = document.querySelector("#methodpayment");

let table;

/*APIS*/

/**
 * Método para anular pagos de transacciones
 * 
 * @param {string} paymentid Id. de la transacción
 */
const annularTransaction = async (paymentid) => {
    try {
        const response = await fetch(`/payment/annulartransaction?paymentid=${paymentid}`, {
            method: "PUT",
            headers: { "Content-Type": "application/json" }
        });

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span style="font-size: 1.3rem">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();

            if (data.message === "error") {
                toastr["error"](data.message, data.title);
            } else {
                toastr["success"](data.message, data.title);
                table.ajax.reload();
            }
        }

    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.3rem;">${error.message}</span>`,
            icon: "error"
        });
    }
}

/**
 * Método que permite buscar los pagos pendientes de los clientes morosos
 * 
 * @param {Array} customers Lista de elementos que contiene los Id's de los clientes
 * @param {string} datestart Fecha inicio de búsqueda
 * @param {string} dateend Fecha termino de búsqueda
 */
const findAll = async(customers, datestart, dateend) => {
    table = $("#tbllistpayment").dataTable({
        "lengthMenu": [10, 25, 75, 100],        // Mostramos el menú de registros a revisar
        "aProcessing": true,                    // Activamos el procesamiento del datatables
        "aServerSide": true,                    // Paginación y filtrado realizados por el servidor
        dom: '<Bl<f>rtip>',                     // Definimos los elementos del control de tabla
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdf'],
        "columnDefs": [
            //{ "targets":[6], render: $.fn.dataTable.render.number('.', ',', 2, ''), "className": "dt-right" },
            { "targets":[5, 6, 7], render: $.fn.dataTable.render.number('.', ',', 0, '$'), "className": "dt-right" },
            { "targets":[2, 3], "className": "dt-right" },
            { "targets":[1, 2, 3], "width": "8%" },
            { "targets":[4], "width": "25%" },
            { "targets":[0, 1, 5, 6, 7, 8], "orderable": false }
        ],
        "ajax": {
            url: `/payment/findall?customers=${customers}&datestart=${datestart}&dateend=${dateend}`,
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
        "iDisplayLength": 10,           // Paginación
        "order": [[2, "asc"], [3, "asc"]]           // Ordenar (columna, orden)
    }).DataTable();
}

/**
 * Función para obtener todos los clientes dispobibles
 */
const findAllCustomer = async () => {
    try {
        const response = await fetch("/payment/findallcustomer", {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        });

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span style="font-size: 1.3rem">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();

            let html = ``;

            Object.entries(data).forEach((value) => {
                html += `
                    <option value="${value[1][0]}">${value[1][1]}</option>
                `;
            });

            $("#establishment").html(html);
        }

    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.3rem;">${error.message}</span>`,
            icon: "error"
        });
    }
}

/**
 * Función para obtener todos los bancos dispobibles
 */
const findAllBank = async () => {
    try {
        const response = await fetch("/payment/findallbank", {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        });

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span style="font-size: 1.3rem">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();

            let html = ``;

            Object.entries(data).forEach((value) => {
                html += `
                    <option value="${value[1][0]}">${value[1][1]}</option>
                `;
            });

            $("#bankid").html(html);
        }

    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.3rem;">${error.message}</span>`,
            icon: "error"
        });
    }
}

/**
 * Función para buscar el documento para crear las transacción
 * 
 * @param {string} headerid Id. del documento
 */
const findOne = async (headerid) => {
    try {
        const response = await fetch(`/payment/findone?headerid=${headerid}`, {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        });

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span style="font-size: 1.3rem">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();
            
            $("#headerdocumentid").val(headerid);
            $("#type").val(data.type);
            $("#folio").val(data.folio);
            $("#deliverdate").val(data.deliverdate);
            $("#total").val(data.total);
            $("#payment").val("0")
            $("#balance").val(data.balance);
        }

    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.3rem;">${error.message}</span>`,
            icon: "error"
        });
    }
}

/**
 * Ver transacciones por documentos
 * 
 * @param {string} headerid Id. de la cabezera
 */
const transaction = async (headerid) => {
    try {
        const response = await fetch(`/payment/transaction?headerid=${headerid}`, {
            method: "GET",
            headers: { "Content-Type": "application/json" }
        });

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span style="font-size: 1.3rem">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();
            
            let html = ``;
            let total = 0;
            let button = ``;

            Object.entries(data).forEach((value) => {
                if (Number(value[1][4]) === 0) {
                    button = `<button type="button" class="btn btn-info btn-sm" disabled>Anular</button>`;
                } else {
                    button = `<button type="button" class="btn btn-info btn-sm annular-transaction" data-toggle-annular-transaction="${value[1][1]}">Anular</button>`;
                }

                html += `
                    <tr>
                        <td>${button}</td>
                        <td class="${value[1][4] === 0 ? "text-danger text-bold": ""}">${value[1][4] === 0 ? "Transacción anulada": value[1][2]}</td>
                        <td class="text-right">${value[1][3]}</td>
                        <td class="text-right text-bold">${(value[1][4]).toLocaleString()}</td>
                    </tr>
                `;
                total += Number(value[1][4]);
            });

            $("#tbltransaction tbody tr").remove();
            $("#tbltransaction tfoot tr").remove();
            $("#tbltransaction tbody").append(html);
            $("#tbltransaction tfoot").append(`<tr><td colspan="3" class="text-bold">TOTAL TRANSACCIONES:</td><td class="text-right text-bold">${(total).toLocaleString()}</td></tr>`);
        }

    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.3rem;">${error.message}</span>`,
            icon: "error"
        });
    }
}

/*
 * Eventos controlados
 */
btnrefresh.addEventListener("click", () => {
    const customers = $("#establishment").val();
    const datestart = $("#datestart").val();
    const dateend   = $("#dateend").val();

    findAll(customers, datestart, dateend);
});

methodpayment.addEventListener("change", (e) => {
    if (e.target.value === "0") {
        $("#bankid").val("0");
        $("#bankid").selectpicker("refresh");
    } else {
        $("#bankid").val("7");
        $("#bankid").selectpicker("refresh");
    }
});

payment.addEventListener("keyup", (e) => {
    if (!window.isNaN(e.target.value)) {
        const amount = Number($("#total").val());
        const payment = Number(e.target.value);
        const balance = amount - payment;

        if (balance < 0) {
            Swal.fire({
                title: `$ ${(balance).toLocaleString()}`,
                html: `<span style="font-size: 1.4rem;">El valor del pago establecido excede el saldo.</span>`,
                icon: "warning"
            });
        }
    }
});

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const params = new URLSearchParams();
    const payment = Number($("#payment").val());
    const balance = Number($("#balance").val());

    if (payment > balance) {
        Swal.fire({
            title: "Atención",
            html: `<span style="font-size: 1.4rem;">El valor pagago excede el valor del documento.</span>`,
            icon: "warning"
        });

        return;
    }

    for ([key, value] of new FormData(form).entries()) {
        params.append(key, value);
    }

    try {
        const response = await fetch("/payment/save", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: params
        });

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span style="font-size: 1.4rem;">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();

            if (data.status === "error") {
                toastr["error"](data.message, data.title);
            } else {
                toastr["success"](data.message, data.title);
                table.ajax.reload();
                $("#btncancel").click();
            }
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.4rem;">${error.message}</span>`,
            icon: "error"
        });
    }
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

    findAll(customers, datestart, dateend);
    findAllCustomer()
    findAllBank()

    // Botón nuevo
    $(document).on("click", ".btn-new-payment", function() {
        const headerid = $(this).attr("data-toggle-payment");

        findOne(headerid)

        $("#comment").val("");
        $("#document").val("");
        $("#modalPayment").modal("show")                // initializes and invokes show immediately
    });

    // Botón de transacciones
    $(document).on("click", ".btn-transaction", function() {
        const headerid = $(this).attr("data-toggle-transaction");

        transaction(headerid);

        window.setTimeout(() => {
            $("#modalPaymentList").modal("show");
        }, 1000);
    })

    // Botones de la tabla transacciones
    $(document).on("click", ".annular-transaction", function() {
        const paymentid = $(this).attr("data-toggle-annular-transaction");

        annularTransaction(paymentid);

        window.setTimeout(() => {
            $("#btncancellist").click();
        }, 500);
    });
});
