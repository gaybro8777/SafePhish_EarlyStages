$('document').ready(function() {
    $('#activeShowButton').click(function(e) {
        e.preventDefault();
        $('.ActiveProject').show();
        $('.InactiveProject').hide();
    });
    $('#inactiveShowButton').click(function(e) {
        e.preventDefault();
        $('.ActiveProject').hide();
        $('.InactiveProject').show();
    });
    $('#showAllButton').click(function(e) {
        e.preventDefault();
        $('.ActiveProject').show();
        $('.InactiveProject').show();
    });
});



function getProject(sel) {
    var projectName = sel.value;
    if(projectName == "0") {
        window.location.href = 'http://localhost:8888/projects/create';
    } else if(projectName == "-1") {
        //do nothing
    } else {
        if(!window.confirm('Are you sure you want to reuse this project?')) {
            $('#projectNameSelect').val('-1');
        }
    }
}
