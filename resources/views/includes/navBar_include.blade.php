<header>
    <div id="headerBar"></div>
    <div id="logoBar">
        <a target="_blank" href="http://www.greatamericaninsurancegroup.com/Pages/default.aspx">
            <img src="/img/gaig_logo_color.jpg" />
        </a>
    </div>
    @yield('header')
</header>
<div id="navBar">
    <div id="navButtonsDiv">
        <button class="navButton @yield('resultsClassDefault')" id="resultsNavButton">Results</button>
        <button class="navButton @yield('projectsClassDefault')" id="projectsNavButton">Projects</button>
        <button class="navButton @yield('templatesClassDefault')" id="templatesNavButton">Templates</button>
        <button class="navButton @yield('emailClassDefault')" id="emailNavButton">Send Email</button>
    </div>
    @if(\Session::get('authUserId'))
        <a class="authenticationLink" target="_blank" href="https://www.myafglink.com">MY ACCOUNT</a>
        <a class="authenticationLink" href="/auth/logout">LOGOUT</a>
    @else
        <a class="authenticationLink" href="/auth/login">LOGIN</a>
    @endif
    <div id="navInfoDiv">
        <div class="templatesInfo">
            <img class='scalable' src="/img/templatePaper.png" /><h2><a href="/templates/show">View Templates</a></h2>
            <p>View all the templates currently accessible through the interface.</p>
        </div>
        <div class="templatesInfo">
            <img class='scalable' src="/img/createNewTemplate.png" /><h2><a href="/templates/create">New Template</a></h2>
            <p>Create a New Template to be used in the interface.</p>
        </div>
        <div class="projectsInfo">
            <img class='scalable' src="/img/templatePaper.png" /><h2><a href="/projects/show">View Projects</a></h2>
            <p>View all the projects, both active and inactive.</p>
        </div>
        <div class="projectsInfo">
            <img class='scalable' src="/img/createNewProject.png" /><h2><a href="/projects/create">New Project</a></h2>
            <p>Create a New Project to be used in managing phishing email scams.</p>
        </div>
        <div class="emailInfo">
            <img class='scalable' src="/img/createNewEmail.png" /><h2><a href="/email/generate">Start Project</a></h2>
            <p>Start a new phishing project or add to a current one.</p>
        </div>
        <div class="emailInfo">
            <img class='scalable' src="/img/emailSettings.png" /><h2><a href="/email/settings">Settings</a></h2>
            <p>Set default settings for your email environment.</p>
        </div>
        <div class="authInfo">
            <p>Please login to access information on this site.</p>
        </div>
    </div>
</div>