function onlyLetters(event) {
    let char = String.fromCharCode(event.keyCode);
    if (!/^[a-zA-Z\s]+$/.test(char)) { // Allow letters and spaces
        event.preventDefault();
        return false;
    }
    return true;
}


function onlyNumbers(event) {
    let char = event.key; 
    if (!/^[0-9]$/.test(char)) {
        event.preventDefault();
        return false;
    }
    return true;
}

