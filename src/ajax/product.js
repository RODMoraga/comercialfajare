const btnadd = document.querySelector("#btnadd")
const btncancel = document.querySelector("#btncancel")
const form = document.querySelector("form")

let table;

/**
 * Cargar todas las categorias disponible
 */
const findAllCategory = async () => {
    const response = await fetch("/product/findallcategories", {
        method: "GET"
    })

    const result = await response.json()

    let html = ``

    Object.entries(result).forEach(([index, value]) => {
        html += `
            <option value="${value.code}">${value.name}</option>\n
        `
    })

    $("#categoryid").html(html)
    $("#categoryid").selectpicker("refresh")
}

/**
 * Cargar todas las unidad de medida disponible
 */
const findAllUnitOfMeasure = async () => {
    const response = await fetch("/product/findalluom", {
        method: "GET"
    })

    const result = await response.json()

    let html = ``

    Object.entries(result).forEach(([index, value]) => {
        html += `
            <option value="${value.code}">${value.name}</option>
        `
    })

    $("#uomid").html(html)
    $("#uomid").selectpicker("refresh")
}

/**
 * Este método busca un producto para editarlo.
 * 
 * @param {number} id Id. del producto
 */
const findOne = async (id) => {
    const response = await fetch(`/product/findone?id=${id}`, {
        method: "GET"
    })

    const result = await response.json()

    if (Object.hasOwn(result, "productcode")) {
        $("#panelproduct").show()
        $("#listproduct").hide()
        $("#btnadd").hide()

        $("#productid").val(result.productid)
        $("#productcode").val(result.productcode)
        $("#productname").val(result.productname)
        $("#barcode").val(result.barcode)
        $("#uomid").val(result.uomid)
        $("#uomid").selectpicker("refresh")
        $("#categoryid").val(result.categoryid)
        $("#categoryid").selectpicker("refresh")
        $("#weight").val(result.weight)
        $("#volume").val(result.volume)
        $("#long").val(result.long)
        $("#width").val(result.width)
        $("#height").val(result.height)
        $("#brand").val(result.brand)
    }
}

const findAll = async () => {
    table = $("#tblproduct").dataTable({
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
            { "targets":[2], "width": "23%" },
            { "targets":[0, 7], "orderable": false }
        ],
        "ajax": {
            url: "/product/findall",
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

const isFieldsValid = () => {
    const fields = [
        ($("#productcode").val()).trim(),
        ($("#productname").val()).trim(),
        $("#uomid").val(),
        $("#categoryid").val()
    ];
    
    return !(fields.includes(null) || fields.includes("") || fields.includes("0"));
}

/**
 * Este método permite cambiar el estado del cliente.
 * 
 * @param {number} id Id. del cliente
 */
const statusChange = async (id) => {
    const response = await fetch(`/product/status?id=${id}`, {
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
    $("#productid").val("")
    $("#productcode").val("")
    $("#productname").val("")
    $("#barcode").val("")
    $("#uomid").val("0")
    $("#uomid").selectpicker("refresh")
    $("#categoryid").val("0")
    $("#categoryid").selectpicker("refresh")
    $("#weight").val("0")
    $("#volume").val("0")
    $("#long").val("0")
    $("#width").val("0")
    $("#height").val("")
    $("#brand").val("")

    $("#panelproduct").show()
    $("#listproduct").hide()
    $("#btnadd").hide()
    //$("#btnadd").prop("disabled", true);
})

btncancel.addEventListener("click", () => {
    $("#panelproduct").hide()
    $("#listproduct").show()
    $("#btnadd").show()
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

    if ($("#productid").val() === "") {
        const response = await fetch("/product/save", {
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
        param.delete("productid")

        const response = await fetch(`/product/save?id=${$("#productid").val()}`, {
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

    $("#panelproduct").hide()
    $("#listproduct").show()
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

    findAll()
    findAllCategory()
    findAllUnitOfMeasure()

    $("#panelproduct").hide()
})