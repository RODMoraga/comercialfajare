const ctx = document.getElementById("dialySales").getContext("2d");
const btnchartrefresh = document.querySelector("#btnchartrefresh");

const headers = new Array();
const details = new Array();

let chartJS;

const createChartJS = () => {
    chartJS = new Chart(ctx, {
        type: 'line',
        data: {
            labels: headers,
            datasets: [
                {
                    label: ' Ventas Diarias',
                    data: details,
                    borderColor: 'rgb(20, 90, 50)',
                    backgroundColor: 'rgb(34, 153, 84 )',
                    pointHoverBackgroundColor: 'rgb(51, 255, 60)',
                    pointHoverBorderColor: 'rgb(51, 255, 60)',
                    lineTension: 0.3,
                    fill: false
                }
            ]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        callback: function(value, index, values) {
                            return (value).toLocaleString();
                        }
                    }
                }]
            }
        }
    });
}

/*
 * Lista de metos 
 */

/**
 * Obtener las ventas diarias
 * 
 * @param {Array} customers Lista de Id's del cliente
 * @param {string} datestart Fecha de inicio
 * @param {string} dateend Fecha de termino
 */
const findAll = async (customers, datestart, dateend) => {
    try {
        const response = await fetch(`/report/dailysales?customers=${customers}&datestart=${datestart}&dateend=${dateend}`, {
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

            let tbody = ``;
            let total = 0;

            Object.entries(data).forEach((value) => {
                headers.push(value[1][0]);
                details.push(value[1][1]);
                
                tbody += `
                    <tr>
                        <td>${value[1][0]}</td>
                        <td class="text-bold text-right">${(Number(value[1][1])).toLocaleString()}</td>
                    </tr>
                `;

                total += Number(value[1][1]);
            });

            $("#tbldailysales tbody").append(tbody);
            $("#tbldailysales tfoot").append(`<tr><td class="text-bold">Total General:</td><td class="text-bold text-right">${(total).toLocaleString()}</td></tr>`);
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

btnchartrefresh.addEventListener("click", () => {
    const customers = $("#establishment").val();
    const datestart = $("#datestart").val();
    const dateend   = $("#dateend").val();

    while (headers.length > 0) {
        headers.pop();
    }

    while (details.length > 0) {
        details.pop();
    }

    $("#tbldailysales tbody tr").remove();
    $("#tbldailysales tfoot tr").remove();

    findAll(customers, datestart, dateend);

    window.setTimeout(() => {
        chartJS.update();
    }, 1000)
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

    window.setTimeout(() => {
        findAllCustomer();

        window.setTimeout(() => {
            createChartJS();
        }, 1000);
    }, 500);
});
