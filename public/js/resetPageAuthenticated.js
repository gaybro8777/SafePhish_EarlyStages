var verifyUserRedirect = function() {
    $validUsername = $('#usernameAction');
    $username = $('#usernameText');
    if($username == $validUsername) {
        $newURL = window.location.href + "/verifyUser";
        console.log($newURL);
        window.location.replace($newURL);
    }
    else {
        $('#errorLabel').value = "Username does not match out records for your username. Please enter valid username.";
    }
};

var usernameClear = function() {
    $('#usernameText').value='';
};