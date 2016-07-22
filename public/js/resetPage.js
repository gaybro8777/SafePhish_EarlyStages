var verifyUserRedirect = function() {
    var newURL = window.location.href + "/verifyUser";
    console.log(newURL);
    window.location.replace(newURL);
};

var usernameClear = function() {
    $('#usernameText').value='';
};