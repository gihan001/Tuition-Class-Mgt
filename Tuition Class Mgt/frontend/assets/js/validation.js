function validateLogin(event){
    event.preventDefault(); // Prevent form submission

    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;

if (email === "") {
    alert("Email is required.");
    return false;
} 

if (password === "") {
    alert("Password is required.");
    return false;
}

alert("Login successful!");

window.location.href = "admin/dashboard.html";

return true;
}
