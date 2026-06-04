function validateLogin(event){
    // Only prevent submission when validation fails
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;

    if (email === "") {
        alert("Email is required.");
        event.preventDefault();
        return false;
    }

    if (password === "") {
        alert("Password is required.");
        event.preventDefault();
        return false;
    }

    // validation passed — allow the form to submit
    return true;
}
