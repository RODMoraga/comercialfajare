const regions = document.querySelector("#regionid")
const cities = document.querySelector("#cityid")
const btnadd = document.querySelector("#btnadd")
const btncancel = document.querySelector("#btncancel")
const form = document.querySelector("form")

let table;

/**
 * Este método busca un cliente para editarlo.
 * 
 * @param {number} id Id. del cliente
 */
const findOne = async (id) => {
    const response = await fetch(`/customer/findone?id=${id}`, {
        method: "GET"
    })

    const result = await response.json()

    if (Object.hasOwn(result, "customercode")) {
        $("#panelcustomer").show()
        $("#listcustomer").hide()
        $("#btnadd").hide()

        window.setTimeout(() => {
            findAllCity(result.regionid)

            window.setTimeout(() => {
                findAllCommune(result.cityid)

                window.setTimeout(() => {
                    $("#customerid").val(result.customerid)
                    $("#customercode").val(result.customercode)
                    $("#customername").val(result.customername)
                    $("#complex").val(result.complex)
                    $("#commercialbusiness").val(result.commercialbusiness)
                    $("#regionid").val(result.regionid)
                    $("#regionid").selectpicker("refresh")
                    $("#cityid").val(result.cityid)
                    $("#cityid").selectpicker("refresh")
                    $("#communeid").val(result.communeid)
                    $("#communeid").selectpicker("refresh")
                    $("#street").val(result.street)
                    $("#paymentid").val(result.paymentid)
                    $("#paymentid").selectpicker("refresh")
                    $("#expiration").val(result.expiration)
                    $("#phone1").val(result.phone1)
                    $("#phone2").val(result.phone2)
                    $("#email").val(result.email)
                    $("#credit").val(result.credit)
                    $("#typefolio").val(result.typefolio)
                    $("#typefolio").selectpicker("refresh")
                    $("#typeorder").val(result.typeorder);            
                }, 750)

            }, 500)

        }, 250)
    }
}

const findAll = async () => {
    table = $("#tblcustomer").dataTable({
        "lengthMenu": [5, 10, 25, 75, 100],     // Mostramos el menú de registros a revisar
        "aProcessing": true,                    // Activamos el procesamiento del datatables
        "aServerSide": true,                    // Paginación y filtrado realizados por el servidor
        dom: '<Bl<f>rtip>',                     // Definimos los elementos del control de tabla
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdf'],
        "columnDefs": [
            //{ "targets":[7], render: $.fn.dataTable.render.number('.', ',', 0, '$'), "className": "dt-right" },
            //{ "targets":[2, 3], "className": "dt-right" },
            { "targets":[0], "width": "7%" },
            { "targets":[2, 3], "width": "23%" },
            { "targets":[0, 1, 10], "orderable": false }
        ],
        "ajax": {
            url: "/customer/findall",
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
        "iDisplayLength": 10,   // Paginación
        "order": [[2, "asc"]]   // Ordenar (columna, orden)
    }).DataTable();
}

/**
 * Obtetien todas las comunas de una ciudad determinada
 * 
 * @param {string} id Id. de la ciudad
 */
const findAllCommune = async (id) => {
    let html = `<option value="0">*** Seleccione una Comuna ***</option>`

    // console.log(id)

    if (id !== "0") {

        const response = await fetch(`/customer/findallcommunes?id=${id}`, {
            method: "GET"
        })

        const result = await response.json()

        // console.log(result)

        Object.entries(result).forEach(([index, value]) => {
            html += `
                <option value="${value["code"]}">${value["name"]}</option>\n
            `
        })

    }

    $("#communeid").html(html)
    $("#communeid").selectpicker("refresh")
}

/**
 * Obtetien todas las ciudades de una region determinada
 * 
 * @param {string} id Id. de la región
 */
const findAllCity = async (id) => {
    let html = `<option value="0">*** Seleccione una Ciudad ***</option>`

    // console.log(id)

    if (id !== "0") {

        const response = await fetch(`/customer/findallcities?id=${id}`, {
            method: "GET"
        })

        const result = await response.json()

        // console.log(result)

        Object.entries(result).forEach(([index, value]) => {
            html += `
                <option value="${value["code"]}">${value["name"]}</option>\n
            `
        })

    }

    $("#cityid").html(html)
    $("#cityid").selectpicker("refresh")
}

const findAllRegion = async () => {
    const response = await fetch("/customer/findallregions", {
        method: "GET"
    })

    const result = await response.json()

    // console.log(result)

    let html = `<option value="0">*** Seleccione una Región ***</option>`

    Object.entries(result).forEach(([i, value]) => {
        // console.log(`index: ${i} value: ${value["code"]} - ${value["name"]}`)
        html += `
            <option value="${value["code"]}">${value["name"]}</option>\n
        `
    })

    $("#regionid").html(html)
    $("#regionid").selectpicker("refresh")
}

const isFieldsValid = () => {
    const fields = [
        ($("#customername").val()).trim(),
        ($("#complex").val()).trim(),
        $("#paymentid").val(),
        $("#regionid").val(),
        $("#cityid").val(),
        $("#communeid").val(),
        ($("#street").val()).trim(),
        ($("#phone1").val()).trim()
    ];
    
    return !(fields.includes(null) || fields.includes("") || fields.includes("0"));
}

/**
 * Este método permite cambiar el estado del cliente.
 * 
 * @param {number} id Id. del cliente
 */
const statusChange = async (id) => {
    const response = await fetch(`/customer/status?id=${id}`, {
        method: "GET"
    })

    const result = await response.json()

    if (result.status === "error") {
        toastr["error"](result.message, result.title)
    } else {
        toastr["success"](result.message, result.title)
    }

    table.ajax.reload();
}

// Events
btnadd.addEventListener("click", () => {
    $("#customerid").val("")
    $("#customercode").val("")
    $("#customername").val("")
    $("#complex").val("")
    $("#commercialbusiness").val("")
    $("#regionid").val("0")
    $("#regionid").selectpicker("refresh")
    $("#cityid").val("0")
    $("#cityid").selectpicker("refresh")
    $("#communeid").val("0")
    $("#communeid").selectpicker("refresh")
    $("#street").val("")
    $("#paymentid").val("0")
    $("#paymentid").selectpicker("refresh")
    $("#expiration").val("0")
    $("#phone1").val("")
    $("#phone2").val("")
    $("#email").val("")
    $("#credit").val("0")
    $("#userid").val("")
    $("#typefolio").val("0")
    $("#typefolio").selectpicker("refresh")
    $("#typeorder").val("0");

    $("#panelcustomer").show()
    $("#listcustomer").hide()
    $("#btnadd").hide()
    //$("#btnadd").prop("disabled", true);
})

btncancel.addEventListener("click", () => {
    $("#panelcustomer").hide()
    $("#listcustomer").show()
    $("#btnadd").show()
})

cities.addEventListener("change", (e) => {
    findAllCommune(e.target.value)
})

regions.addEventListener("change", (e) => {
    findAllCity(e.target.value)
})

form.addEventListener("submit", async (e) => {
    e.preventDefault()

    if (!isFieldsValid()) {
        Swal.fire({
            title: "Campos Nulos",
            text: "Hay campos importantes que no se han completos.",
            icon: "warning"
        });

        return;
    }

    const param = new URLSearchParams()

    for (const [key, value] of new FormData(form).entries()) {
        param.append(key, value)
    }

    if ($("#customerid").val() === "") {
        const response = await fetch("/customer/save", {
            method: "POST",
            headers: {
                "content-type": "application/x-www-form-urlencoded"
            },
            body: param
        })

        const result = await response.json()
    
        if (result.status === "error") {
            toastr["error"](result.message, result.title)
        } else {
            toastr["success"](result.message, result.title)
        }
    } else {
        param.delete("customerid")

        const response = await fetch(`/customer/save?id=${$("#customerid").val()}`, {
            method: "PUT",
            headers: {
                "content-type": "application/x-www-form-urlencoded"
            },
            body: param
        })

        const result = await response.json()
    
        if (result.status === "error") {
            toastr["error"](result.message, result.title)
        } else {
            toastr["success"](result.message, result.title)
        }
    }

    table.ajax.reload();

    $("#panelcustomer").hide()
    $("#listcustomer").show()
    $("#btnadd").show()
})

window.addEventListener("DOMContentLoaded", async (e) => {
    e.preventDefault()

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

    findAllRegion()
    findAll()

    $("#panelcustomer").hide()
})