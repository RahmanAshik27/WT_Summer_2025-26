document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("loginForm");

    const loginIdentifier =
        document.getElementById("login_identifier");

    const password =
        document.getElementById("password");

    const passwordToggle =
        document.getElementById("passwordToggle");


    
    // Password Show / Hide
    

    passwordToggle.addEventListener("click", function () {

        if (password.type === "password") {

            password.type = "text";

            passwordToggle.innerText = "🙈";

            passwordToggle.setAttribute(
                "aria-label",
                "Hide password"
            );

        } else {

            password.type = "password";

            passwordToggle.innerText = "👁";

            passwordToggle.setAttribute(
                "aria-label",
                "Show password"
            );
        }

    });


    
    // Login Form Validation
    

    form.addEventListener("submit", function (event) {

        clearErrors();

        let isValid = true;


        // Username / Email
        if (loginIdentifier.value.trim() === "") {

            showError(
                loginIdentifier,
                "Username or email is required."
            );

            isValid = false;
        }


        // Password
        if (password.value === "") {

            showError(
                password,
                "Password is required."
            );

            isValid = false;
        }


        if (!isValid) {

            event.preventDefault();
        }

    });


    function showError(input, message) {

        const formGroup =
            input.closest(".form-group");

        const error =
            document.createElement("small");

        error.className = "field-error";

        error.innerText = message;

        formGroup.appendChild(error);

        input.classList.add("input-error");
    }


    function clearErrors() {

        const errors =
            document.querySelectorAll(".field-error");

        errors.forEach(function (error) {

            error.remove();
        });


        const inputs =
            document.querySelectorAll(".input-error");

        inputs.forEach(function (input) {

            input.classList.remove("input-error");
        });
    }

});