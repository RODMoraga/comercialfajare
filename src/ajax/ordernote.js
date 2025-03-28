const btnaddproduct = document.querySelector("#btnaddproduct")
const btnremoveall = document.querySelector("#btnremoveall")
const btnrefresh = document.querySelector("#btnrefresh")
const cbocomplex = document.querySelector("#establishment")
const btncancel = document.querySelector("#btncancel")
const btnadd = document.querySelector("#btnadd")
const form = document.querySelector("form")

let table;

// Métodos

const calculation = () => {
    let total = 0

    $("#details tbody tr").each(function() {
        const item1 = $(this).find("td:eq(3) input[type=number]").val()
        const item2 = $(this).find("td:eq(4) input[type=number]").val()
        const item3 = $(this).find("td:eq(5) input[type=number]").val()
        
        const subtotal = window.parseFloat((item1 * item2 * (1 - (item3 / 100))).toFixed(0))

        $(this).find("td:eq(6)").text((subtotal).toLocaleString())

        total += subtotal
    })

    $("#net").val((total).toString())
    $("#tax").val((total * 0.19).toFixed(0))
    $("#total").val((total + (total * 0.19)).toFixed(0))

    $("#details tfoot tr td:eq(4)").text((total).toLocaleString())
    $("#details tfoot tr td:eq(5)").text(window.parseFloat((total * 0.19).toFixed()).toLocaleString())
    $("#details tfoot tr td:eq(6)").text(window.parseFloat((total + (total * 0.19)).toFixed(0)).toLocaleString())

    // console.log((new Date()).toUTCString() + $("#typefolio").val())

    if (($("#typefolio").val()).split(" - ")[0] === "0") {
        $("#details tfoot tr td:eq(5)").text("0")
        $("#details tfoot tr td:eq(6)").text("0")

        $("#tax").val("0")
        $("#total").val("0")
    }
}

/**
 * Método para eliminar los items del documento.
 * 
 * @param {number} headerid Id. del encabezado del documento
 * @param {Array} detailid Id. del detalle del documento
 * @param {Array} params Id. o Ids del los productos ingresados
 */
const deleteBy = async (headerid, detailid, params) => {
    try {
        const response = await fetch(`/ordernote/delete/item?headerid=${headerid}&detailid=${detailid}&params=${params}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            }
        })

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span style="font-size: 1.3rem;">${response.statusText}</span>`,
                icon: "error"
            })
        } else {
            const result = await response.json()

            toastr["warning"](result.message, result.title)
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.3rem;">${error.message}</span>`,
            icon: "error"
        })
    }
}

/**
 * Método para visualizar los documentos diponibles en la tabla
 * 
 * @param {string} complex Nombre del establecimiento
 * @param {string} datestart Fecha de inicio
 * @param {string} dateend Fecha de termino
 */
const findAll = async (complex, datestart, dateend) => {
    table = $("#tbllistprice").dataTable({
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
            { "targets":[5], render: $.fn.dataTable.render.number('.', ',', 0, '$'), "className": "dt-right" },
            { "targets":[2, 3], "className": "dt-right" },
            { "targets":[0], "width": "12%" },
            { "targets":[1, 2, 3, 5, 6], "width": "9%" },
            { "targets":[4], "width": "20%" },
            { "targets":[0, 5, 6], "orderable": false }
        ],
        "ajax": {
            url: `/ordernote/findall?complex=${complex}&datestart=${datestart}&dateend=${dateend}`,
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
        "order": [[3, "desc"], [1, "asc"], [2, "desc"]]           // Ordenar (columna, orden)
    }).DataTable();
}

const findAllComplex = async () => {
    try {
        const response = await fetch("/ordernote/findallcomplex", {
            method: "GET",
            headers: {
                "content-type": "application/json"
            }
        })

        if (!response.ok) {
            Swal.fire({
                title: "Atención",
                html: `<span stule='font-size: 1.2rem;'>${response.statusText}</span>`,
                icon: "error"
            })
        } else {
            const result = await response.json()

            if (Object.hasOwn(result, "message")) {
                Swal.fire({
                    title: result.title,
                    html: `<span style='font-size: 1.2rem;'>${result.message}</span>`,
                    icon: "warning"
                })
            } else {
                let html = ``

                Object.entries(result).forEach((value, index) => {
                    html += `
                        <option value="${value[1].code}">${value[1].name}</option>
                    `
                })

                $("#complex-filter").html(html)
                $("#complex-filter").selectpicker("refresh")
                $("#establishment").html(html)
                $("#establishment").selectpicker("refresh")
            }
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style='font-size: 1.2rem;'>${error.message}</span>`,
            icon: "error"
        })
    }
}

/**
 * Este método permite buscar un cliente seleccionado.
 * 
 * @param {number} id Id. del cliente a buscar
 */
const findOneCustomer = async (id) => {
    try {
        const response = await fetch(`/ordernote/findonecustomer?id=${id}`, {
            method: "GET",
            headers: {
                "content-type": "application/json"
            }
        })

        if (!response.ok) {
            Swal.fire({
                title: "Atención",
                html: `<span stule='font-size: 1.2rem;'>${response.statusText}</span>`,
                icon: "error"
            })
        } else {
            const result = await response.json()

            if (Object.hasOwn(result, "message")) {
                Swal.fire({
                    title: result.title,
                    html: `<span style='font-size: 1.2rem;'>${result.message}</span>`,
                    icon: "warning"
                })
            } else {
                $("#customername").val(result.customername)
                $("#street").val(result.street)
                $("#email").val(result.email)
                $("#phone1").val(result.phone1)
                $("#phone2").val(result.phone2)
                $("#typefolio").val(window.parseInt(result.typefolio) === 0 ? "0 - Sin Factura": "1 - Con Factura")
                $("#applytotal").val(result.typefolio)
            }
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style='font-size: 1.2rem;'>${error.message}</span>`,
            icon: "error"
        })
    }
}

/**
 * Este método permite buscar un documento seleccionado.
 * 
 * @param {number} id Id. del documento a buscar
 */
const findOneDocument = async (id) => {
    try {
        const response = await fetch(`/ordernote/findonedocument?headerid=${id}`, {
            method: "GET",
            headers: {
                "content-type": "application/json"
            }
        })

        if (!response.ok) {
            Swal.fire({
                title: "Atención",
                html: `<span stule='font-size: 1.3rem;'>${response.statusText}</span>`,
                icon: "error"
            })
        } else {
            const result = await response.json()

            if (Object.hasOwn(result, "message")) {
                Swal.fire({
                    title: result.title,
                    html: `<span style='font-size: 1.3rem;'>${result.message}</span>`,
                    icon: "warning"
                })
            } else {
                let tbody = ``;

                $("#headerdocumentid").val("")
                $("#details tbody tr").remove()

                Object.entries(result).forEach(value => {
                    if ($("#headerdocumentid").val() === "") {
                        $("#headerdocumentid").val(value[1][0])
                        $("#type").val(value[1][1])
                        $("#type").selectpicker("refresh")
                        $("#folio").val(value[1][2])
                        $("#deliverdate").val(value[1][3])
                        $("#dateorder").val(value[1][4])
                        $("#establishment").val(value[1][5])
                        $("#establishment").selectpicker("refresh")
                        $("#customername").val(value[1][6])
                        $("#street").val(value[1][7])
                        $("#email").val(value[1][8])
                        $("#phone1").val(value[1][9])
                        $("#phone2").val(value[1][10])
                        $("#typefolio").val(value[1][11])
                        $("#applytotal").val((value[1][11]).split(" - ")[0])
                        $("#gloss").val(value[1][12])
                        $("#net").val(value[1][13])
                        $("#tax").val(value[1][14])
                        $("#total").val(value[1][15])

                        $("#details tfoot tr td:eq(4)").text((value[1][13]).toLocaleString())
                        $("#details tfoot tr td:eq(5)").text((value[1][14]).toLocaleString())
                        $("#details tfoot tr td:eq(6)").text((value[1][15]).toLocaleString())

                        // Obtener la lista de precios de ese cliente
                        getListPrice(window.parseInt(value[1][5]))
                    }

                    tbody += `
                        <tr>
                            <td><button type="button" class="btn btn-danger delete-item-product" data-item-product="${value[1][16]}" data-item-description="${value[1][18]}">Eliminar</button></td>
                            <td>${value[1][17]}</td>
                            <td>${value[1][18]}</td>
                            <td><input type="number" class="form-control text-right" name="itemdetail1[]" onkeyup="calculation()" value="${value[1][19]}" /></td>
                            <td><input type="number" class="form-control text-right" name="itemdetail2[]" onkeyup="calculation()" value="${value[1][20]}" /></td>
                            <td><input type="number" class="form-control text-right" name="itemdetail3[]" onkeyup="calculation()" value="${value[1][21]}" /></td>
                            <td>${(value[1][22]).toLocaleString()}</td>
                            <td class="hidden"><input type="number" name="itemdetail4[]" value="${value[1][23]}" /></td>
                            <td class="hidden"><input type="number" name="itemdetail5[]" value="${value[1][16]}" /></td>
                        </tr>
                    `
                })

                $("#details tbody").append(tbody)

                // Visualizar y ocultar los paneles indicado
                $("#btnadd").hide()
                $("#btnremoveall").show()
                $("#btnaddproduct").show()
                $("#panelfilters").hide()
                $("#listordernotes").hide()
                $("#panelordernote").show()
                $("#textboxfilter").show()
                $("#details tfoot tr").show()
            }
        }
    } catch (error) {
        console.log("Hola")
        Swal.fire({
            title: "Error",
            html: `<span style='font-size: 1.3rem;'>${error.message}</span>`,
            icon: "error"
        })
    }
}

/**
 * Verifica que los campos estén correctamente utilizados
 * 
 * @return {boolean}
 */
const isValid = () => {
    const isBlank = $("#details tbody tr td:eq(6)").filter(function() {
        return $(this).text().length === 1
    }).parent("tr")

    const fielfs = [
        $("#folio").val(),
        $("#establishment").val(),
        $("#details tbody tr").length,
        (isBlank.length > 0)
    ]

    // fielfs.forEach((e) => console.log(e))

    // console.log(isBlank)

    return !(fielfs.includes("") || fielfs.includes(null) || fielfs.includes(0) || fielfs.includes("0") || fielfs.includes(true))
}

/**
 * Método que permite buscar un producto repetido, para no agregarlo en la tabla de
 * productos.
 * 
 * @param {number} id Id del producto a buscar
 * @return {boolean}
 */
const existsProductItems = (id) => {
    let counter = 0

    $("#details tbody tr").each(function(index) {
        const idItem = $(this).find("td:eq(8) input[type=number]").val()

        // console.log(`index: ${index}, id: ${id}, Item: ${idItem}, id: ${typeof(id)}, idItem: ${typeof(idItem)}`)

        if (id === window.parseInt(idItem)) {
            counter++
        }
    })

    // console.log(`counter: ${counter}, counter > 0: ${(counter > 0)}`)
    return (counter > 0)
}

/**
 * Este método permite buscar el precio del cliente seleccionado.
 * 
 * @param {number} id Id. del cliente a buscar
 */
const getListPrice = async (id) => {
    $("#listproducts").dataTable({
        "lengthMenu": [5, 10],
        "aProcessing": true,                    // Activamos el procesamiento del datatables
        "aServerSide": true,                    // Paginación y filtrado realizados por el servidor
        "columnDefs": [
            { "targets":[3, 4], "className": "dt-right" },
            { "targets":[0, 3, 4], "orderable": false }
        ],
        "ajax": {
            url: `/ordernote/getlistprice?id=${id}`,
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
        "order": [[2, "asc"], [1, "asc"]]   // Ordenar (columna, orden)
    }).DataTable();
}

/**
 * Obtiene el último documento de la base de datos
 */
const lastDocument = async () => {
    try {
        const response = await fetch("/ordernote/lastdocument", {
            method: "GET",
            headers: {
                "content-type": "application/json"
            }
        })

        if (!response.ok) {
            Swal.fire({
                title: `Error: ${response.status}`,
                html: `<span style="font-size: 1.3rem;">${response.statusText}</span>`,
                icon: "error"
            })

        } else {
            const result = await response.json()

            if (Object.hasOwn(result, "message")) {
                Swal.fire({
                    title: result.title,
                    html: `<span style="font-size: 1.3rem;">${result.message}</span>`,
                    icon: "warning"
                })
            } else {
                const folio = Number(result.folio) + 1
                $("#folio").val(folio)
            }
        }
    } catch (error) {
        Swal.fire({
            title: "¡Error!",
            html: `<span style="font-size: 1.3rem;">${error.message}</span>`,
            icon: "error"
        })
    }
}

/**
 * Método para generar el documento pdf por el usuario.
 * 
 * @param {number} headerid Id. del documento
 */
const printFPDF = async (headerid) => {
    try {
        const response = await fetch(`/ordernote/generatefpdf?headerid=${headerid}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/pdf"
            },
        })

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span style="font-size: 1.3rem;">${response.statusText}</span>`,
                icon: "warning"
            })
        } else {
            const result = await response.json()

            // Mostrar mensaje
            toastr["success"](result.message, result.title)
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.3rem;">${error.message}</span>`,
            icon: "error"
        })
    }
}

/**
 * Este método guardar el documento ingresado
 * 
 * @param {URLSearchParams} formData Información del formulario
 */
const save = async (formData) => {
    try {
        const response = await fetch("/ordernote/save", {
            method: "POST",
            headers: {
                "content-type": "application/json"
            },
            body: formData
        })

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span>${response.statusText}</span>`,
                icon: "warning"
            })
        } else {
            const result = await response.json()
            
            // Mostrar mensaje
            toastr["success"](result.message, result.title)

            // Refrescar tabla
            table.ajax.reload()

            window.setTimeout(() => {
                $("#btncancel").click()
            }, 500)
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span>${error.message}</span>`,
            icon: "error"
        })
    }
}

/**
 * Actualizar el documento
 * 
 * @param {number} headerid Id. de la cabezera
 * @param {URLSearchParams}
 * @param {boolean} formClose true o false para cerrar el formulario
 */
const update = async(headerid, formData, formClose = true) => {
    try {
        const response = await fetch(`/ordernote/update?headerid=${headerid}`, {
            method: "PUT",
            headers: {
                "content-type": "application/json"
            },
            body: formData
        })

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span>${response.statusText}</span>`,
                icon: "warning"
            })
        } else {
            const result = await response.json()
            
            // Mostrar mensaje
            toastr["success"](result.message, result.title)

            // Refrescar tabla
            table.ajax.reload()

            if (formClose) {
                window.setTimeout(() => {
                    $("#btncancel").click()
                }, 500)
            }
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span>${error.message}</span>`,
            icon: "error"
        })
    }
}

/**
 * Método para cambiar el estado del documento
 * 
 * @param {number} id Id. del cabezera del documento
 */
const statusDocument = async(id) => {
    try {
        const response = await fetch(`/ordernote/status?headerid=${id}`, {
            method: "GET",
            headers: {
                "content-type": "application/json"
            },
        })

        if (!response.ok) {
            Swal.fire({
                title: `${response.status}`,
                html: `<span>${response.statusText}</span>`,
                icon: "warning"
            })
        } else {
            const result = await response.json()
            
            // Mostrar mensaje
            toastr["success"](result.message, result.title)

            // Refrescar tabla
            table.ajax.reload()
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span>${error.message}</span>`,
            icon: "error"
        })
    }
}

// Eventos del formulario
btnadd.addEventListener("click", async () => {
    $("#btnadd").hide()
    $("#panelfilters").hide()
    $("#listordernotes").hide()
    $("#panelordernote").show()

    lastDocument()

    $("#headerdocumentid").val("")
    $("#establishment").val("0")
    $("#establishment").selectpicker("refresh")
    $("#customername").val("")
    $("#street").val("")
    $("#email").val("")
    $("#phone1").val("")
    $("#phone2").val("")
    $("#gloss").val("")
    $("#typefolio").val("")
    $("#net").val("")
    $("#tax").val("")
    $("#total").val("");
    $("#applytotal").val("")

    $("#folio").prop("disabled", false)

    $("#textboxfilter").hide()
    $("#btnaddproduct").hide()
    $("#btnremoveall").hide()
    
    window.setTimeout(() => {
        $("#folio").focus().select()
    }, 1000)

    $("#details tbody tr").remove()
})

btncancel.addEventListener("click", () => {
    $("#btnadd").show()
    $("#panelfilters").show()
    $("#listordernotes").show()
    $("#panelordernote").hide()
    $("#details tfoot tr").hide()
    $("#details tfoot tr td:eq(4)").text("0")
    $("#details tfoot tr td:eq(5)").text("0")
    $("#details tfoot tr td:eq(6)").text("0")
})

btnrefresh.addEventListener("click", () => {
    findAll($("#complex-filter").val(), $("#datestart").val(), $("#dateend").val())
})

cbocomplex.addEventListener("change", (e) => {
    findOneCustomer(e.target.value)
    getListPrice(e.target.value)

    $("#btnaddproduct").show()
})

btnremoveall.addEventListener("click", () => {
    const details = []
    const products = []

    if ($("#headerdocumentid").val() !== "") {
    
        $("#details tbody tr").each(function(index) {
            const idp = $(this).find("td:eq(7) input[type=number]").val()
            const idd = $(this).find("td:eq(8) input[type=number]").val()

            details.push(window.parseInt(idd))
            products.push(window.parseInt(idp))
        })
    }

    Swal.fire({
        title: "Eliminar Items",
        html: `<span style="font-size: 1.3rem;">¿Estas seguro de eliminar todos los items del documento?</span>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "<span style=\"font-size: 1.3rem;\">Cancelar</span>",
        confirmButtonText: "<span style=\"font-size: 1.3rem;\">Si, Eliminar todos</span>"
    }).then((result) => {
        if (result.isConfirmed) {
            $("#details tbody tr").remove()
            $("#details tfoot tr").hide()
            $("#btnremoveall").hide()

            $("#details tfoot tr td:eq(4)").val("0");
            $("#details tfoot tr td:eq(5)").val("0");
            $("#details tfoot tr td:eq(6)").val("0");

            if (products.length) {
                const headerid = window.parseInt($("#headerdocumentid").val())
                deleteBy(headerid, details, products)
            }
        }
    })
})

/* Event formulario modal al activarse
$("#formModal").on("show.bs.modal", function () {
    console.log($("#establishment").val())
    getListPrice($("#establishment").val())
});*/

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

    // Propiedades de panel
    $("#panelordernote").hide()
    $("#details tfoot tr").hide()

    // Accionar métodos
    findAll("", $("#datestart").val(), $("#dateend").val())
    findAllComplex()

    // Evento click del botón "Agregar Producto" que se encuentra en el
    // formulario Modal
    $(document).on("click", ".button-add-product", function(e) {
        // 1.- Id del producto
        // 2.- Código SKU
        // 3.- Nombre SKU
        // 4.- Precio
        // 5.- Dcto
        const products = ($(this).attr("data-toggle-price")).split("&&&")

        if (existsProductItems(parseInt(parseInt(products[0])))) {
            return
        }

        // console.log(`existsProductItems: ${existsProductItems(window.parseInt(products[0]))}`)
        
        html = `
            <tr>
                <td><button type="button" class="btn btn-danger delete-item-product" data-item-product="${products[0]}" data-item-description="${products[2]}">Eliminar</button></td>
                <td>${products[1]}</td>
                <td>${products[2]}</td>
                <td><input type="number" class="form-control text-right" name="itemdetail1[]" onkeyup="calculation()" value="0" /></td>
                <td><input type="number" class="form-control text-right" name="itemdetail2[]" onkeyup="calculation()" value="${products[3]}" /></td>
                <td><input type="number" class="form-control text-right" name="itemdetail3[]" onkeyup="calculation()" value="${products[4]}" /></td>
                <td>0</td>
                <td class="hidden"><input type="number" name="itemdetail4[]" value="0" /></td>
                <td class="hidden"><input type="number" name="itemdetail5[]" value="${products[0]}" /></td>
            </tr>
        `
        $("#details tbody").append(html)

        if (!$("#btnremoveall").show()) {
            $("#btnremoveall").show()
        }

        if (!$("#textboxfilter").show()) {
            $("#textboxfilter").show()
        }

        if (!$("#details tfoot tr").show()) {
            $("#details tfoot tr").show()
        }
    })

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

    // TextBox para filtra o buscar un producto
    $("#textboxfilter").on("keyup", function() {
        const value = $(this).val().toLowerCase();

        $("#details > tbody > tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        })
    })

    // Botón editar para hacer actualizaciones en la lista
    $(document).on("click", ".button-edit", function() {
        const id = $(this).attr("data-toggle-edit-document")

        $("#folio").prop("disabled", true)

        findOneDocument(id)
    })

    // Botón de la lista, para cambiar el estado del documento.
    $(document).on("click", ".button-annular", function() {
        const id = $(this).attr("data-toggle-annular-document")
        
        statusDocument(id)
    })

    // Botón para eliminar el item del documento.
    $(document).on("click", ".delete-item-product", function() {
        const products = [$(this).attr("data-item-product"), $(this).attr("data-item-description")]
        const details = []
        const params = []

        Swal.fire({
            title: "Eliminando",
            html: `<span style="font-size: 1.3rem;">¿Estas seguro de eliminar el producto: <b>${products[1]}</b>, del documento?</span>`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "<span style=\"font-size: 1.3rem;\">Cancelar</span>",
            confirmButtonText: "<span style=\"font-size: 1.3rem;\">Si, Eliminarlo!</span>"
        }).then((result) => {
            if (result.isConfirmed) {
                $("#details tbody tr").each(function() {
                    const det = $(this).find("td:eq(7) input[type=number]").val()
                    const row = $(this).find("td:eq(8) input[type=number]").val()
                    const obj = $(this).find("td:eq(8)")

                    if (typeof row === "string" && typeof products[0] === "string") {
                        if (row === products[0]) {
                            details.push(window.Number(det))
                            params.push(window.Number(row))

                            obj.parent("tr").remove()   // Elimina el item seleccionado
                            
                            calculation()               // Recalcular el documento

                            if (!$("#details tbody tr").length) {
                                $("#details tfoot tr").hide()
                            }

                            window.setTimeout(() => {
                                if (details.length && !details.includes(0)) {
                                    const headerid = window.Number($("#headerdocumentid").val())
                                    deleteBy(headerid, details, params)
                                }
                            }, 500)

                            window.setTimeout(() => {
                                if (details.length && !details.includes(0)) {
                                    const headerid = window.Number($("#headerdocumentid").val())
                                    const params = new URLSearchParams()

                                    for ([key, value] of new FormData(form).entries()) {
                                        if (key !== "headerdocumentid") {
                                            params.append(key, value)
                                        }
                                    }

                                    update(headerid, params, false)
                                }
                            }, 1000)
                        }
                    }
                })
            }
        })

    })

    // Botón imprimir pdf
    $(document).on("click", ".button-printer", function(e) {
        const headerid = $(this).attr("data-toggle-print-document")
        printFPDF(headerid)
    })
})

window.addEventListener("submit", async (e) => {
    e.preventDefault()

    if (!isValid()) {
        Swal.fire({
            title: "Atención",
            html: `<span style="font-size: 1.3rem; font-weight: 300;">Hay campos importante que no se han completados.</span>`,
            icon: "warning"
        })

        return
    }

    const params = new URLSearchParams()

    for (const [key, value] of new FormData(form).entries()) {
        params.append(key, value)
    }

    if ($("#headerdocumentid").val() === "") {
        save(params)
    } else {
        const folio = $("#folio").val()

        Swal.fire({
            title: "Actualizando",
            html: `<span style="font-size: 1.4rem;">¿Estas seguro de aplicar estos cambios en el documento Nro.: <b>${folio}</b>.?</span>`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "<b style=\"font-size: 1.4rem;\">Cancelar</b>",
            confirmButtonText: "<b style=\"font-size: 1.4rem;\">Si, Aplicar Cambios!</b>"
        }).then((result) => {
            if (result.isConfirmed) {
                const headerid = Number($("#headerdocumentid").val())

                params.delete("headerdocumentid")

                update(headerid, params)
            }
        })
    }
})