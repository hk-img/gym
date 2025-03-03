function onlyLetters(event) {
    let char = String.fromCharCode(event.keyCode);
    if (!/^[a-zA-Z]+$/.test(char)) {
        event.preventDefault();
        return false;
    }
    return true;
}