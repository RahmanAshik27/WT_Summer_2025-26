document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("registrationForm");

    const fullName = document.getElementById("full_name");
    const username = document.getElementById("username");
    const email = document.getElementById("email");
    const phone = document.getElementById("phone");
    const password = document.getElementById("password");
    const address = document.getElementById("address");

    form.addEventListener("submit", function (event) {

        clearErrors();

        let isValid = true;

        // Full Name
        if (fullName.value.trim() === "") {
            showError(fullName, "Full name is required.");
            isValid = false;
        }

        // Username
        const usernamePattern = /^[a-zA-Z0-9_]+$/;

        if (username.value.trim() === "") {
            showError(username, "Username is required.");
            isValid = false;
        }
        else if (username.value.trim().length < 4) {
            showError(username, "Username must be at least 4 characters.");
            isValid = false;
        }
        else if (!usernamePattern.test(username.value.trim())) {
            showError(
                username,
                "Only letters, numbers and underscore are allowed."
            );
            isValid = false;
        }

        // Email
        const emailPattern =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email.value.trim() === "") {
            showError(email, "Email address is required.");
            isValid = false;
        }
        else if (!emailPattern.test(email.value.trim())) {
            showError(email, "Enter a valid email address.");
            isValid = false;
        }

        // Phone
        const phonePattern = /^01[0-9]{9}$/;

        if (phone.value.trim() === "") {
            showError(phone, "Phone number is required.");
            isValid = false;
        }
        else if (!phonePattern.test(phone.value.trim())) {
            showError(
                phone,
                "Enter a valid 11-digit Bangladeshi phone number."
            );
            isValid = false;
        }

        // Password
        if (password.value === "") {
            showError(password, "Password is required.");
            isValid = false;
        }
        else if (password.value.length < 6) {
            showError(
                password,
                "Password must be at least 6 characters."
            );
            isValid = false;
        }

        // Address
        if (address.value.trim() === "") {
            showError(address, "Home address is required.");
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
        }
    });


    function showError(input, message) {

        const formGroup = input.parentElement;

        const error = document.createElement("small");

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