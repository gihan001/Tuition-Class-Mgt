function validateLogin(event) {
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value;

    // alert() වෙනුවට ෆෝම් එක සබ්මිට් වීම පමණක් නිහඬව නතර කරයි (HTML5 Required එක ක්‍රියාත්මක වීමට ඉඩ හරියි)
    if (email === "" || password === "") {
        event.preventDefault();
        return false;
    }

    return true;
}