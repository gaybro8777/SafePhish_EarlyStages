@extends('masters.basemaster')
@section('title')
    Breach - Password Reset
@stop
@section('head')
    <script type="text/javascript" src="/js/jquery-2.2.4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/basestyles.css" />
@stop
@section('header')
    <!--{{HTML::image('/img/afg_self-service_head.png')}}-->
    <img src="/img/afg_self-service_head.png" />
@stop
@section('content')
    <p>Please answer the following security questions to change your password.</p>
    <form method="post" enctype="application/x-www-form-urlencoded" name="search" autocomplete="off">
        <h2>What city were you born in?</h2>
        <input tabindex="1" type="text" name="city" id="cityText" size="60" value="" /><br />
        <h2>What is your mother's maiden name?</h2>
        <input tabindex="2" type="text" name="mothersMaiden" id="maidenText" size="60" value="" /><br />
        <div id="buttonbar">
            <input type="hidden" name="processAction" value="search" />
            <input tabindex="3" type="submit" class="btn" name="search" value="     Search     " id="submitBtn" onclick="window.location=''"/>
            <input tabindex="4" type="reset" class="btn" name="reset" value="     Clear     " onclick="$('#cityText').value=''; $('#mothersMaiden').value='';"/>
        </div>
    </form>
@stop

@section('footer')
    @include('detailedFooter_include.blade.php')
@stop