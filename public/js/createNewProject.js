$('document').ready(function() {
    $('#submitButton').click(function(e) {
        $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });
        var projectName = $('#projectNameText').val();
        var projectAssignee = $('#projectAssigneeText').val();
        if(projectName == '' || projectAssignee == '') {
            e.preventDefault();
            if(projectName == '') {
                $('#projectNameText').css('border-color','red');
            } else {
                $('#projectNameText').css('border-color','');
            }
            if(projectAssignee == '') {
                $('#projectAssigneeText').css('border-color','red');
            } else {
                $('#projectAssigneeText').css('border-color','');
            }
        } else {
            $.ajax({
                method: "POST",
                data: {projectNameText: projectName, projectAssigneeText: projectAssignee},
                success: function() {
                    window.location.replace("http://localhost:8888/email/generate");
                }
            });
        }
    });
});

