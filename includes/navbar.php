<nav class="navbar">

    <div class="logo">
        🎧 MixMaster AI
    </div>

    <div class="nav-user">

        <?php
        if (isset($_SESSION['fullname'])) {
            echo "Welcome, " . htmlspecialchars($_SESSION['fullname']);
        }
        ?>

    </div>

</nav>