
<nav class="navbar navbar-expand-lg">
<div class="container-fluid">
    <a class="navbar-brand cim" href="index.php">MySeriesList</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="true" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
        <div class="navbar-nav">
            <a <?php if ($isadmin || $loggedin) : ?> style="display: none;" <?php endif ?> class="btn login m-1" href="login.php">Bejelentkezés</a>
            <a <?php if ($isadmin || $loggedin) : ?> style="display: none;" <?php endif ?> class="btn register m-1" href="register.php">Regisztáció</a>
            <a <?php if ($isadmin === false && $loggedin === false) : ?> style="display: none;" <?php endif ?> class="btn logout m-1" href="sessiondestroy.php">Kijelentkezés</a>
        </div>
    </div>
</div>
</nav>


