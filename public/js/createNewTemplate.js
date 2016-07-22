$('document').ready(function() {
    var contentTextArea = $('#contentTextArea');
    var previewContentDiv = $('#templateContentDiv');
    contentTextArea.keyup(function() {
        previewContentDiv.html(contentTextArea.val());
    });
    contentTextArea.text('<!--This is a Phishing Email sponsored by {{ $companyName }} to test your awareness ' +
        'of Phishing Scams. Project Name: {{ $projectName}}-->');
    $('#firstNameButton').click(function(e) {
        e.preventDefault();
        contentTextArea.insertAtCaret('{{ $firstName }}');
        previewContentDiv.html(contentTextArea.val());
    });
    $('#imageButton').click(function(e) {
        e.preventDefault();
        contentTextArea.insertAtCaret("{!! HTML::image('http://www.url.com/image.jpg') !!}");
        previewContentDiv.html(contentTextArea.val());
    });
    $('#companyNameButton').click(function(e) {
        e.preventDefault();
        contentTextArea.insertAtCaret('{{ $companyName }}');
        previewContentDiv.html(contentTextArea.val());
    });
    $('#projectNameButton').click(function(e) {
        e.preventDefault();
        contentTextArea.insertAtCaret('{{ $projectName }}');
        previewContentDiv.html(contentTextArea.val());
    });
    $('#lastNameButton').click(function(e) {
        e.preventDefault();
        contentTextArea.insertAtCaret('{{ $lastName }}');
        previewContentDiv.html(contentTextArea.val());
    });
    $('#usernameButton').click(function(e) {
        e.preventDefault();
        contentTextArea.insertAtCaret('{{ $username }}');
        previewContentDiv.html(contentTextArea.val());
    });
    $('#uniqueURLButton').click(function(e) {
        e.preventDefault();
        contentTextArea.insertAtCaret('{{ $urlID }}');
        previewContentDiv.html(contentTextArea.val());
    });
    $('#emailSubjectButton').click(function(e) {
        e.preventDefault();
        contentTextArea.insertAtCaret('{{ $subject }}');
        previewContentDiv.html(contentTextArea.val());
    });
    $('#emailToButton').click(function(e) {
        e.preventDefault();
        contentTextArea.insertAtCaret('{{ $toEmail }}');
        previewContentDiv.html(contentTextArea.val());
    });
    $('#emailFromButton').click(function(e) {
        e.preventDefault();
        contentTextArea.insertAtCaret('{{ $fromEmail }}');
        previewContentDiv.html(contentTextArea.val());
    });

    $('#submitButton').click(function(e) {
        $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });
        var templateName = $('#fileNameText');
        if(textEmpty(templateName.val())) {
            e.preventDefault();
            templateName.css('border-color','red');
        } else {
            $.ajax({
                method: "POST",
                data: {templateName: templateName.val(), templateContent: contentTextArea.val()},
                success: function() {
                    window.location.replace("http://localhost:8888/email/generate");
                }
            });
        }
    });

    $('#checkNameButton').click(function(e) {
        e.preventDefault();
        var templateName = $('#fileNameText');
        var path = 'http://localhost:8888/files/templates/' + templateName.val();
        console.log(templateName.val());
        $.get(path,function(data) {
            if(data == 'Preview Unavailable') {
                templateName.css('border-color','');
            } else {
                templateName.css('border-color', 'red');
            }
        });
    });

});


jQuery.fn.extend({
    insertAtCaret: function(myValue){
        return this.each(function(i) {
            if (document.selection) {
                //For browsers like Internet Explorer
                this.focus();
                var sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            }
            else if (this.selectionStart || this.selectionStart == '0') {
                //For browsers like Firefox and Webkit based
                var startPos = this.selectionStart;
                var endPos = this.selectionEnd;
                var scrollTop = this.scrollTop;
                this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                this.focus();
                this.selectionStart = startPos + myValue.length;
                this.selectionEnd = startPos + myValue.length;
                this.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
        });
    }
});

function textEmpty(val) {
    return val == '';
}