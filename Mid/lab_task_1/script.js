var hour = new Date().getHours();

var greet = (hour < 12) ? "Good Morning" : (hour < 18) ? "Good Afternoon" : "Good Evening";

document.getElementById("greeting").innerText = greet;


function updateClock() {
    var now = new Date();
    document.getElementById("liveClock").innerText = now.toLocaleTimeString();
}

setInterval(updateClock, 1000);


function toggleTheme() {
    var body = document.body;
    var btn = document.getElementById("themeBtn");

    body.classList.toggle("dark-mode");

    if (body.classList.contains("dark-mode")) {
        btn.innerText = "Click to switch to Light Mode";
    } else {
        btn.innerText = "Click to switch to Dark Mode";
    }
}


document.getElementById("myForm").addEventListener("submit", function(e) {
    e.preventDefault();

    var name = document.getElementById("name").value;
    var email = document.getElementById("email").value;
    var phone = document.getElementById("phone").value;
    var age = document.getElementById("age").value;
    var pass = document.getElementById("password").value;
    var confirmPass = document.getElementById("confirmPassword").value;
    var msg = document.getElementById("message").value;

    var valid = true;

    function setError(id, msg) {
        document.getElementById(id).innerText = msg;
        if (msg !== "") {
            valid = false;
        }
    }

    if (name.length < 3) {
        setError("nameError", "Name: Min 3 characters");
    } else {
        setError("nameError", "");
    }

    if (!email.includes("@")) {
        setError("emailError", "Invalid Email format");
    } else {
        setError("emailError", "");
    }

    if (phone.length !== 10) {
        setError("phoneError", "Phone: Must be 10 digits");
    } else {
        setError("phoneError", "");
    }

    if (age < 18) {
        setError("ageError", "Age: Must be 18+");
    } else {
        setError("ageError", "");
    }

    var passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!passRegex.test(pass)) {
        setError("passwordError", "Min 8 chars, 1 Upper, 1 Lower, 1 Num, 1 Special");
    } else {
        setError("passwordError", "");
    }

    if (pass !== confirmPass) {
        setError("confirmError", "Passwords do not match");
    } else {
        setError("confirmError", "");
    }

    if (msg.length < 10) {
        setError("messageError", "Message: Min 10 characters");
    } else {
        setError("messageError", "");
    }

    if (valid) {
        document.getElementById("myForm").style.display = "none";
        
        var success = document.getElementById("successMessage");
        success.style.display = "block";
        
        success.innerHTML = 
            "<h3>Registration Successful!</h3>" +
            "<p><strong>Name:</strong> " + name + "</p>" +
            "<p><strong>Email:</strong> " + email + "</p>" +
            "<p><strong>Phone:</strong> " + phone + "</p>" +
            "<p><strong>Age:</strong> " + age + "</p>" +
            "<p><strong>Message:</strong> " + msg + "</p>" +
            "<p>Thank you for registering successfully.</p>";
    }
});