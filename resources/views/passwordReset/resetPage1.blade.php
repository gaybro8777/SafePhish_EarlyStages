@extends('masters.basemaster')
@section('title')
    Breach - Password Reset
@stop
@section('head')
    <meta name="csrf_token" content="{{csrf_token()}}" />
    <script type="text/javascript" src="/js/jquery-2.2.4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/basestyles.css" />
@stop
@section('header')
    <!--{{HTML::image('/img/afg_self-service_head.png')}}-->
    <img src="/img/afg_self-service_head.png" />
@stop
@section('content')
    <p>Please follow the prompts to change your password.</p>
    <p>To start, please enter your username.</p>
    <h2>Username</h2>

    <form method="post" enctype="application/x-www-form-urlencoded" autocomplete="off">
        <input tabindex="1" type="text" name="username" id="usernameText" size="60" value="" /><br />
        <div id="buttonbar">
            <input type="hidden" name="processAction" value="search" />
            <input tabindex="3" type="submit" class="btn" name="search" value="     Search     " id="submitBtn" />
            <input tabindex="4" type="reset" class="btn" name="reset" value="     Clear     " onclick="usernameClear()" />
        </div>
    </form>
@stop

@section('footer')
    @include('detailedFooter_include.blade.php')
@stop