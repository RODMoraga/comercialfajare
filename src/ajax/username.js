const form = document.querySelector("form");
const closed = document.querySelector("#btnclosed");

let tablehtml;

/**
 * Método que ejecuta la eliminación del usuario.
 * 
 * @param {number} userid Id. del usuario a eliminar
 */
const deleteById = async (userid) => {
    try {
        const response = await fetch(`/username/delete?userid=${userid}`, {
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
 * Mostrar los usuarios disponibles
 */
const findAll = async () => {
    tablehtml = $('#tblusername').dataTable({
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
            { "targets":[0, 1, 2, 3, 4, 5], "width": "14,285%" },
            { "targets":[0, 3, 4, 5], "orderable": false }
        ],
        "ajax": {
            url: `/username/findall`,
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
        "order": [[1, "asc"], [2, "asc"]]  // Ordenar (columna, orden)
    }).DataTable();
}


/**
 * Cargar todos los perfiles en el ComboBox
 */
const findAllProfile = async () => {
    try {
        const response = await fetch("/username/findallprofile", {
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
            const data = await response.text();
            const position = data.search("error");

            if (position !== -1) {
                const message = data.split(";;;");

                Swal.fire({
                    title: `${message[1]}`,
                    html: `<span>${message[0]}</span>`,
                    icon: "error"
                });
            } else {
                $("#profile").html(data);
                $("#profile").selectpicker("refresh");
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
 * Método para buscar el id. del usuario
 * 
 * @param {string} userid Id del usuario
 */
const findOne = async (userid) => {
    try {
        const response = await fetch(`/username/findone?userid=${userid}`, {
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
                console.log(data);
                $("#userid").val(data.userid);
                $("#username").val(data.username);
                $("#description").val(data.fullname);
                $("#password").val("");
                $("#confirmpassword").val("");
                $("#profile").val(data.profile);
                $("#profile").selectpicker("refresh");
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
    $("#userid").val("");
    $("#username").val("");
    $("#description").val("");
    $("#password").val("");
    $("#confirmpassword").val("");
    $("#profile").val("0");
    $("#profile").selectpicker("refresh");
}

/**
 * Método para guardar los cambios realizados en el usuario
 * 
 * @param {URLSearchParams} params Los valores a guardar
 */
const save = async (params) => {
    try {
        const response = await fetch("/username/save", {
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
 * Método para actualizar los cambios realizados en el usuario
 * 
 * @param {URLSearchParams} params Los valores a actualizar
 * @param {string} userid Id del usuario
 */
const update = async (userid, params) => {
    try {
        const response = await fetch(`/username/update?userid=${userid}`, {
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
 * Método que ejecuta la eliminación del usuario.
 * 
 * @param {number} userid Id. del usuario a eliminar
 */
const statusToChange = async (userid) => {
    try {
        const response = await fetch(`/username/status?userid=${userid}`, {
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
    console.log("Close");
});

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const params = new URLSearchParams();

    for ([key, value] of new FormData(form).entries()) {
        params.append(key, value);
    }

    if ($("#userid").val() !== "") {
        const userid = params.get("userid");
        params.delete("userid");
        update(userid, params);
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
    
    window.setTimeout(() => {
        findAll();

        window.setTimeout(() => {
            findAllProfile()
        }, 500)

    }, 0);

    // Eventos para los botones de la tabla usuarios
    $(document).on("click", ".btn-delete-user", function() {
        const params = ($(this).attr("data-table-user")).split(";;;");
        
        switch (window.parseInt(params[0])) {
            case 1: case 2:
                Swal.fire({
                    title: `Eliminando`,
                    html: `¿Estas seguro de eliminar el usuario: <b>${params[2]}</b> de la base de datos?`,
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

    // Botón de la tabla cambiar status del usuario.
    $(document).on("click", ".btn-status-user", function() {
        const userid = $(this).attr("data-table-status");
        statusToChange(Number(userid));
    });

    // Botón de la tabla editar
    $(document).on("click", ".btn-edit-user", function() {
        const userid = $(this).attr("data-table-edit");
        findOne(Number(userid));
    });
});