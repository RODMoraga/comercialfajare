const btnadd = document.querySelector("#btnadd")
const complex = document.querySelector("#complex")
const btncancel = document.querySelector("#btncancel")
const form = document.querySelector("form")

let table;

const findAll = async () => {
    table = $("#tblprice").dataTable({
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
            { "targets":[5], render: $.fn.dataTable.render.number('.', ',', 2, '$'), "className": "dt-right" },
            { "targets":[6], render: $.fn.dataTable.render.number('.', ',', 2, ''), "className": "dt-right" },
            //{ "targets":[5, 6], "className": "dt-right" },
            //{ "targets":[0], "width": "7%" },
            //{ "targets":[2], "width": "23%" },
            { "targets":[0], "orderable": false }
        ],
        "ajax": {
            url: "/price/findall",
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
        "order": [[2, "asc"], [4, "asc"]]   // Ordenar (columna, orden)
    }).DataTable();
}

const findAllComplex = async (type = 0) => {
    try {
        const response = await fetch("/price/findall/complex", {
            method: "GET"
        })
    
        const result = await response.json()
    
        if (Object.hasOwn(result, "status")) {
            Swal.fire({
                title: result.title,
                text: result.message,
                icon: "warning"
            })
        } else {
            let html = ``

            Object.entries(result).forEach(([index, value]) => {
                html += `
                    <option value="${value.code}">${value.name}</option>
                `
            })

            if (type == 0) {
                $("#complex-filter").html(html)
                $("#complex-filter").selectpicker("refresh")

            } else {
                $("#complex").html(html);
                $("#complex").selectpicker("refresh");
            }
        }
        
    } catch (error) {
        Swal.fire({
            title: "Error",
            text: error,
            icon: "warning"
        })
    }

}

const findAllCustomername = async (type = 0) => {
    try {
        const response = await fetch("/price/findall/customername", {
            method: "GET"
        })
    
        const result = await response.json()
    
        if (Object.hasOwn(result, "status")) {
            Swal.fire({
                title: result.title,
                text: result.message,
                icon: "warning"
            })
        } else {
            let html = ``

            Object.entries(result).forEach(([index, value]) => {
                html += `
                    <option value="${value.code}">${value.name}</option>
                `
            })

            if (type == 0) {
                $("#customername").html(html)
                $("#customername").selectpicker("refresh")
            }
        }
        
    } catch (error) {
        Swal.fire({
            title: "Error",
            text: error,
            icon: "warning"
        })
    }

}

const findAllProducts = async (type = 0) => {
    try {
        const response = await fetch("/price/findall/products", {
            method: "GET"
        })
    
        const result = await response.json()

        if (Object.hasOwn(result, "status")) {
            Swal.fire({
                title: result.title,
                text: result.message,
                icon: "warning"
            })
        } else {
            let html = ``

            Object.entries(result).forEach(([index, value]) => {
                html += `
                    <option value="${value.code}">${value.name}</option>
                `
            })

            if (type === 0) {
                $("#product-filter").html(html)
                $("#product-filter").selectpicker("refresh")
            }
        }
        
    } catch (error) {
        Swal.fire({
            title: "Error",
            text: error,
            icon: "warning"
        })
    }

}

const findLocationCustomer = async (id) => {
    try {
        const response = await fetch(`/price/find/location?id=${id}`, {
            method: "GET"
        })

        if (!response.ok) {
            Swal.fire({
                title: "Error",
                text: response.status,
                icon: "warning"
            })
        } else {
            const result = await response.json()

            // console.log(result)

            if (Object.hasOwn(result, "status")) {
                Swal.fire({
                    title: result.title,
                    text: result.message,
                    icon: "warning"
                })
            } else {
                $("#customername").val(id)
                $("#customername").selectpicker("refresh")

                $("#street").val(result.street)
                $("#phone1").val(result.phone1)
            }
        }

    } catch (error) {
        Swal.fire({
            title: "Error",
            text: error.message,
            icon: "warning"
        })
    }
}

const loadPriceCustomer = async (id) => {
    try {
        const response = await fetch(`/price/load/customer?id=${id}`, {
            method: "GET",
            headers: {
                "content-type": "application/json"
            }
        })

        if (!response.ok) {
            Swal.fire({
                title: response.status,
                text: response.statusText,
                icon: "warning"
            })
        } else {
            const result = await response.json()

            let html = ``

            $("#details tbody tr").remove()

            Object.entries(result).forEach(([index, value]) => {
                html += `
                    <tr>
                        <td>${value[0]}</td>
                        <td>${value[1]}</td>
                        <td>${value[2]}</td>
                        <td>${value[3]}</td>
                        <td><input type="number" class="form-control input-group-sm text-right" name="price[]" value="${value[4]}" /></td>
                        <td><input type="number" class="form-control input-group-sm text-right" name="discount1[]" value="${value[5]}" /></td>
                    </tr>
                `
            })

            $("#details tbody").append(html)
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            text:error.message,
            icon: "Warning"
        })
    }
}

// Events
btnadd.addEventListener("click", (e) => {
    $("#panelprice").show()
    $("#listprices").hide()
    $("#panelfilters").hide()
    $("#btnadd").hide()

    $("#complex-filter").val("0")
    $("#complex-filter").selectpicker("refresh")
    $("#product-filter").val("0")
    $("#product-filter").selectpicker("refresh")
})

btncancel.addEventListener("click", (e) => {
    $("#panelprice").hide()
    $("#listprices").show()
    $("#panelfilters").show()
    $("#btnadd").show()

    $("#details tbody tr").remove()
    
    $("#complex-filter").val("0")
    $("#complex-filter").selectpicker("refresh")
    $("#product-filter").val("0")
    $("#product-filter").selectpicker("refresh")
})

complex.addEventListener("change", async (e) => {
    window.setTimeout(() => {
        findLocationCustomer(e.target.value)

        window.setTimeout(() => {
            loadPriceCustomer(e.target.value)
        }, 250)

    }, 0)
})

form.addEventListener("submit", async (e) => {
    e.preventDefault()

    const product = []
    const details = []
    const prices  = []
    const dcto1   = []

    for (const [key, value] of new FormData(form).entries()) {
        switch (key) {
            case "price[]":
                prices.push(value)
                break
        
            default:
                break
        }
    }

    const params = new URLSearchParams()

    $("#details tbody tr").each(function(i) {
        $(this).children("td:eq(0)").each(function() {
            if (prices[i] !== "0") {
                details.push($(this).text())
            }
        })
        $(this).children("td:eq(1)").each(function() {
            if (prices[i] !== "0") {
                product.push($(this).text())
            }
        })
        $(this).children("td:eq(5)").each(function() {
            const dcto = $(this).find("input[type=number]")
            if (prices[i] !== "0") {
                dcto1.push(dcto.val())
            }
        })
    })

    params.append("customerid", $("#customername").val())
    params.append("product", product)
    params.append("details", details)
    params.append("prices", prices.filter((e) => !e.startsWith("0")))
    params.append("dcto1", dcto1)

    try {
        const response = await fetch("/price/save", {
            method: "POST",
            headers: {
                "content-type": "application/json"
            },
            body: params
        })

        if (!response.ok) {
            Swal.fire({
                title: `Error Server: ${response.status}`,
                text: response.statusText,
                icon: "warning"
            })
        } else {
            const result = await response.json()

            // Mostrar mensaje
            toastr["success"](result.message, result.title)

            // Actualizar la tabla
            table.ajax.reload();

            // Visualizar lista y ocultar formulario.
            $("#panelprice").hide()
            $("#listprices").show()
            $("#panelfilters").show()
            $("#btnadd").show()

            $("#details tbody tr").remove()
            
            $("#complex-filter").val("0")
            $("#complex-filter").selectpicker("refresh")
            $("#product-filter").val("0")
            $("#product-filter").selectpicker("refresh")
        }
    } catch (error) {
        Swal.fire({
            title: "Error de Respuesta",
            text: error.message,
            icon: "warning"
        })
    }
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

    findAll();
    findAllComplex()
    findAllComplex(1)
    findAllCustomername()
    findAllProducts()

    $("#panelprice").hide()
})