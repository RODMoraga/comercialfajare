const form = document.querySelector("form");
const closed = document.querySelector("#btnclosed");

let tablehtml;

/**
 * Método que ejecuta la eliminación de las Categorias.
 * 
 * @param {number} categoryid Id. de la Categoria
 */
const deleteById = async (categoryid) => {
    try {
        const response = await fetch(`/category/delete?categoryid=${categoryid}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            }
        });

        if (!response.ok) {
            Swal.fire({
                title: `Error: ${response.status}`,
                html: `<span style="font-size: 1.4rem;">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();
            
            if (data.status === "error")
                toastr["error"](data.message, data.title);
            else
                toastr["success"](data.message, data.title);

            tablehtml.ajax.reload();
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
 * Mostrar las Categorias disponibles
 */
const findAll = async () => {
    tablehtml = $('#tblcategory').dataTable({
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
            // { "targets":[3], render: $.fn.dataTable.render.number('.', ',', 0, '$'), "className": "dt-right" },
            // { "targets":[0], "className": "dt-right" },
            // { "targets":[0], render: $.fn.dataTable.render.number('.', ',', 0, ''), "className": "dt-right" },
            { "targets":[0], "width": "25%" },
            { "targets":[0, 2, 3], "orderable": false }
        ],
        "ajax": {
            url: `/category/findall`,
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
        "order": [[1, "asc"]]  // Ordenar (columna, orden)
    }).DataTable();
}

/**
 * Método para buscar el id. de la Categorias
 * 
 * @param {string} categoryid Id de la Categorias
 */
const findOne = async (categoryid) => {
    try {
        const response = await fetch(`/category/findone?categoryid=${categoryid}`, {
            method: "GET",
            headers: { "Content-Type": "application/x-www-form-urlencoded" }
        });

        if (!response.ok) {
            Swal.fire({
                title: `Error: ${response.status}`,
                html: `<span style="font-size: 1.4rem;">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();

            if (data.status === "error") {
                Swal.fire({
                    title: `${data.title}`,
                    html: `<span style="font-size: 1.3rem;">${data.message}</span>`,
                    icon: "error"
                });
            } else {
                $("#categoryid").val(data.categoryid);
                $("#description").val(data.description);
            }
        }
    } catch (error) {
        Swal.fire({
            title: `Error`,
            html: `<span style="font-size: 1.3rem;">${error.message}</span>`,
            icon: "error"
        });
    }
}

/**
 * Inicializa los objetos del formulario
 */
const initalizeControl = () => {
    $("#categoryid").val("");
    $("#description").val("");
}

/**
 * Método para guardar los cambios realizados en la Categorias
 * 
 * @param {URLSearchParams} params Los valores a guardar
 */
const save = async (params) => {
    try {
        const response = await fetch("/category/save", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: params
        });

        if (!response.ok) {
            Swal.fire({
                title: `Error: ${response.status}`,
                html: `<span style="font-size: 1.4rem;">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();
            
            if (Array.isArray(data.message)) {
                let items = ``;
                let counter = 1;

                for (const item of data.message) {
                    items += `<p style="font-size: 1.2rem;text-align: start; margin: 0; padding: 0;">${counter}.- ${item}</p>`;
                    counter++;
                }

                Swal.fire({
                    title: `${data.title}`,
                    html: items,
                    icon: "warning"
                });
            } else {
                if (data.status === "error")
                    toastr["error"](data.message, data.title);
                else
                    toastr["success"](data.message, data.title);

                tablehtml.ajax.reload();

                initalizeControl();

                window.setTimeout(() => {
                    $("#btnclosed").click();
                }, 500);
            }
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
 * Método para actualizar los cambios realizados en la Categorias
 * 
 * @param {URLSearchParams} params Los valores a actualizar
 * @param {string} categoryid Id de la Categoria
 */
const update = async (categoryid, params) => {
    try {
        const response = await fetch(`/category/update?categoryid=${categoryid}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: params
        });

        if (!response.ok) {
            Swal.fire({
                title: `Error: ${response.status}`,
                html: `<span style="font-size: 1.4rem;">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();
            
            if (Array.isArray(data.message)) {
                let items = ``;
                let counter = 1;

                for (const item of data.message) {
                    items += `<p style="font-size: 1.2rem;text-align: start; margin: 0; padding: 0;">${counter}.- ${item}</p>`;
                    counter++;
                }

                Swal.fire({
                    title: `${data.title}`,
                    html: items,
                    icon: "warning"
                });
            } else {
                if (data.status === "error")
                    toastr["error"](data.message, data.title);
                else
                    toastr["success"](data.message, data.title);

                tablehtml.ajax.reload();

                initalizeControl();

                window.setTimeout(() => {
                    $("#btnclosed").click();
                }, 500);
            }
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
 * Método que ejecuta la eliminación de la Categorias.
 * 
 * @param {number} categoryid Id. de la Categorias
 */
const statusToChange = async (categoryid) => {
    try {
        const response = await fetch(`/category/status?categoryid=${categoryid}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            }
        });

        if (!response.ok) {
            Swal.fire({
                title: `Error: ${response.status}`,
                html: `<span style="font-size: 1.4rem;">${response.statusText}</span>`,
                icon: "error"
            });
        } else {
            const data = await response.json();
            
            if (data.status === "error")
                toastr["error"](data.message, data.title);
            else
                toastr["success"](data.message, data.title);

            tablehtml.ajax.reload();
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            html: `<span style="font-size: 1.4rem;">${error.message}</span>`,
            icon: "error"
        });
    }
}

closed.addEventListener("click", () => {
    //
});

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const params = new URLSearchParams();

    for ([key, value] of new FormData(form).entries()) {
        params.append(key, value);
    }

    if ($("#categoryid").val() !== "") {
        const categoryid = params.get("categoryid");
        params.delete("categoryid");
        update(categoryid, params);
    } else
        save(params);
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
    
    findAll();

    // Eventos para los botones de la tabla Categorias
    $(document).on("click", ".btn-delete-category", function() {
        const params = ($(this).attr("data-table-category")).split(";;;");

        switch (window.parseInt(params[0])) {
            case 1: case 2:
                Swal.fire({
                    title: `Eliminando`,
                    html: `¿Estas seguro de eliminar la unidad de medida: <b>${params[2]}</b> de la base de datos?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Si, Eliminarlo!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteById(params[1]);
                    }
                });
                break;
            default:
                Swal.fire({
                    title: "Atención",
                    html: `<span style="font-size: 1.2rem;">No estas autorizado para hacer esta acción</span>`,
                    icon: `warning`
                });
                break;
        }
    });

    // Botón de la tabla editar
    $(document).on("click", ".btn-edit-category", function() {
        const categoryid = $(this).attr("data-table-edit");
        findOne(Number(categoryid));
    });
});