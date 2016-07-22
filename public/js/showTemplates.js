$('document').ready(function() {
    $('#advShowButton').click(function(e) {
        e.preventDefault();
        $('.bscTemplate').hide();
        $('.eduTemplate').hide();
        $('.advTemplate').show();
    });
    $('#bscShowButton').click(function(e) {
        e.preventDefault();
        $('.bscTemplate').show();
        $('.eduTemplate').hide();
        $('.advTemplate').hide();
    });
    $('#eduShowButton').click(function(e) {
        e.preventDefault();
        $('.bscTemplate').hide();
        $('.eduTemplate').show();
        $('.advTemplate').hide();
    });
    $('#genShowButton').click(function(e) {
        e.preventDefault();
        $('.genTemplate').show();
        $('.eduTemplate').hide();
        $('.tarTemplate').hide();
    });
    $('#tarShowButton').click(function(e) {
        e.preventDefault();
        $('.genTemplate').hide();
        $('.eduTemplate').hide();
        $('.tarTemplate').show();
    });
    $('#showAllButton').click(function(e) {
        e.preventDefault();
        $('.genTemplate').show();
        $('.eduTemplate').show();
        $('.tarTemplate').show();
    });
});



function getTemplateData(sel) {
    var templateName = sel.value;
    if(templateName == "0") {
        window.location.href = 'http://localhost:8888/templates/create';
    } else if(templateName == "-1") {
        //do nothing
    } else {
        var path = 'http://localhost:8888/files/templates/' + templateName;
        $.get(path,function(data) {
            $('#templateContentDiv').html(data);
        });
    }
}