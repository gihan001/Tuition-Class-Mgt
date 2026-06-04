function validateLogin(event){
    event.preventDefault(); // Prevent form submission

    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;

if (email === "") {
    alert("Email is required.");
    return false;
} 

// Toggle password visibility for any button with .toggle-password
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.toggle-password').forEach(function(btn){
        btn.addEventListener('click', function(){
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;
            if (input.type === 'password'){
                input.type = 'text';
                this.textContent = '🙈';
                this.setAttribute('aria-label','Hide password');
            } else {
                input.type = 'password';
                this.textContent = '👁️';
                this.setAttribute('aria-label','Show password');
            }
        });
    });
});

if (password === "") {
    alert("Password is required.");
    return false;
}

alert("Login successful!");

//window.location.href = "admin/dashboard.php";

return true;
}
