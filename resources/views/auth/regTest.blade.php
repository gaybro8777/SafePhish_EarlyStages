<html>
<head>
    <title>Generate Emails Form</title>
    <meta name="_token" content="{{ csrf_token() }}" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
</head>
<body>
<div class="rootContainer">{!! Form::open(array('action'=>'PhishingController@postRegister')) !!}
    {!! Form::text('usernameText','tthrockmorton',array('name'=>'usernameText')) !!}
    {!! Form::password('passwordText',array('name'=>'passwordText')) !!}
    {!! Form::text('firstNameText','Tyler',array('name'=>'firstNameText')) !!}
    {!! Form::text('lastNameText','Throckmorton',array('name'=>'lastNameText')) !!}
    {!! Form::submit('Submit',array('id'=>'submitButton')) !!}
{!! Form::close() !!}
</div>
<footer>
    <p></p>
</footer>
</body>
</html>