function ValidateUser(theForm) {
    var filter;
    filter = /^([a-zA-Z0-9-_]{6,12})$/;

    if ((theForm.user.value === "") || !filter.test(theForm.user.value)) {
        alert('Please provide a valid user');
        theForm.user.focus();
        return false;
    }
    return true;
}

function ValidatePhone(theForm) {
    return true;
}


function ValidatePassword(theForm) {
    var filter;
    filter = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{5,}$/;
    if ((theForm.password.value === "" && theForm.password1.value === "") || theForm.password.value !== theForm.password1.value || (!filter.test(theForm.password.value) && !filter.test(theForm.password1.value))) {
        alert('Please provide a valid password');
        theForm.password.focus();
        return false;
    }
}


function FormSignUp(theForm) {
    if (ValidateUser(theForm) === false) {
      return false;
    }
    if (ValidatePhone(theForm) === false) {
      return false;
    }
    if (ValidatePassword(theForm) === false) {
      return false;
    }
    return true;
}
