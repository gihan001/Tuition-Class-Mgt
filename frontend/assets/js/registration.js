// Register පිටුවේදී Student/Teacher අනුව කොටු මාරු වන පිරිසිදු කේතය
function toggleFields() {
    var role = document.getElementById("roleSelect").value;
    var studentFields = document.getElementById("studentFields");
    var teacherFields = document.getElementById("teacherFields");

    var studentInputs = studentFields.querySelectorAll("input");
    var teacherInputs = teacherFields.querySelectorAll("input");

    if (role === "student") {
        studentFields.style.display = "block";
        teacherFields.style.display = "none";
        
        studentInputs.forEach(input => input.required = true);
        teacherInputs.forEach(input => input.required = false);
    } 
    else if (role === "teacher") {
        studentFields.style.display = "none";
        teacherFields.style.display = "block";
        
        studentInputs.forEach(input => input.required = false);
        teacherInputs.forEach(input => input.required = true);
    } 
    else {
        studentFields.style.display = "none";
        teacherFields.style.display = "none";
        
        studentInputs.forEach(input => input.required = false);
        teacherInputs.forEach(input => input.required = false);
    }
}