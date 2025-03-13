const btnadd = document.querySelector("#btnadd")
const complex = document.querySelector("#complex")
const btncancel = document.querySelector("#btncancel")
const btnrefresh = document.querySelector("#btnrefresh")
const form = document.querySelector("form")

let table;

const deleteBy = async (header, detail, product) => {

    try {
        const response = await fetch(`/price/delete?header=${header}&detail=${detail}&product=${product}`, {
            method: "DELETE"
        })

        if (!response.ok) {
            Swal.fire({
                title: `Error: ${response.status}`,
                text: response.statusText,
                icon: "warning"
            })
        } else {
            const result = await response.json()

            if (result.status === "error") {
                Swal.fire({
                    title: result.title,
                    text: result.message,
                    icon: "error"
                })

            } else {
                toastr["success"](result.message, result.title)
                table.ajax.reload();
            }
        }
    } catch (error) {
        Swal.fire({
            title: "¡Error!",
            text: error.message,
            icon: "error"
        })
    }

}

const findAll = async () => {
    const customer = $("#complex-filter").val()
    const products = $("#product-filter").val()

    table = $("#tblprice").dataTable({
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
            { "targets":[5], render: $.fn.dataTable.render.number('.', ',', 2, '$'), "className": "dt-right" },
            { "targets":[6], render: $.fn.dataTable.render.number('.', ',', 2, ''), "className": "dt-right" },
            //{ "targets":[5, 6], "className": "dt-right" },
            //{ "targets":[0], "width": "7%" },
            //{ "targets":[2], "width": "23%" },
            { "targets":[0, 1, 6], "orderable": false }
        ],
        "ajax": {
            url: `/price/findall?customer=${customer}&products=${products}`,
            type: "GET",
            //data: { customer: customer, products: products },
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
                        <td class="hidden">${value[0]}</td>
                        <td class="hidden">${value[1]}</td>
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

    $("#complex").val("0")
    $("#complex").selectpicker("refresh")
    $("#customername").val("0")
    $("#customername").selectpicker("refresh")
    $("#filter-text-table").val("")
    $("#street").val("")
    $("#phone1").val("")

    $("#details tbody tr").remove()
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

    findAll()
})

complex.addEventListener("change", async (e) => {
    window.setTimeout(() => {
        findLocationCustomer(e.target.value)

        window.setTimeout(() => {
            loadPriceCustomer(e.target.value)
        }, 250)

    }, 0)
})

btnrefresh.addEventListener("click", async () => {
    findAll()
})

form.addEventListener("submit", async (e) => {
    e.preventDefault()

    const product = []
    const details = []
    const prices  = []
    const dcto1   = []

    let addition = 0;

    for (const [key, value] of new FormData(form).entries()) {
        switch (key) {
            case "price[]":
                prices.push(value)
                break
        
            default:
                break
        }
    }

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

    $("#details tbody tr").each(function() {
        $(this).children("td:eq(4)").each(function() {
            const val_input = $(this).find("input[type=number]")

            if (!isNaN(val_input.val())) {
                addition += Number(val_input.val())
            }
        })
    })

    if (addition === 0) {
        Swal.fire({
            title: "Precios",
            html: `<p style="font-size: 1.3rem;">No se han especificados los precios, para guardar.</p>`,
            icon: "error"
        })

        return
    }

    const params = new URLSearchParams()

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

    $(document).on("click", ".btn-delete-price", function(e) {
        const detail  = $(this).attr("data-table-detail");
        const header  = $(this).attr("data-table-header");
        const product = $(this).attr("data-table-product");
        const names   = $(this).attr("data-table-names");
        const list    = names.split(" - ")

        Swal.fire({
            title: `${list[0]}`,
            html: `¿Estas seguro de eliminar el producto: <b>${list[1]}</b> de la base de datos?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, Eliminarlo!"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteBy(header, detail, product)
            }
        })
    })

    $("#filter-text-table").on("keyup", function() {
        const value = $(this).val().toLowerCase();

        $("#details > tbody > tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
})