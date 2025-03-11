const form = document.querySelector("form")

/**
 * Buscar a travez de usuario y contraseña
 * 
 * @param {string} username Nombre de usuario
 * @param {string} password Contraseña
 */
const findOne = async (username, password) => {
    const param = new URLSearchParams()

    param.append("username", username)
    param.append("password", password)

    const response = await fetch("/login/findone", {
        method: "POST",
        headers: {
            "content-type": "application/x-www-form-urlencoded"
        },
        body: param
    })

    const data = await response.json()

    if (Object.hasOwn(data, "status")) {
        if (data.status === "error") {
            Swal.fire({
                title: data.title,
                text: data.message,
                icon: "warning"
            })

            $("#username").val("")
            $("#password").val("")
            
        } else {
            window.location.href = "/dashboard"
        }
    }

}

form.addEventListener("submit", async(e) => {
    e.preventDefault()

    const username = $("#username").val();
    const password = $("#password").val();

    findOne(username, password)
})