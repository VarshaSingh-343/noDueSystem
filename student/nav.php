<style>
        header {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 20px;
    }

    .header-item {
        text-align: center;
        margin-bottom: 15px;
    }

    .welcome-message {
        font-size: 18px;
        color: #333;
        font-weight: bold;
        padding: 15px;
    }

    nav {
        text-align: center;
        width: 100%;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        background-color: #333;
        font-size: 16px;
        height: 50px;
    }

    nav ul {
        padding: 0;
        margin: 0;
        list-style-type: none;
        display: flex;
        flex-direction: row;
    }

    .nav-link {
        background-color: #333;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        text-decoration: none;
        border-radius: 4px;
    }

    .nav-link:hover {
        background-color: #555;
    }

    li {
        color: white;
    }
</style>


    <nav>
        <ul>
            <li><a href="studentDashboard.php" class="nav-link">Dashboard</a></li>
            <li>|</li>
            <li><a href="noDuesRequest.php" class="nav-link">No Dues Request</a></li>
            <li>|</li>
            <li><a href="studentLogout.php" class="nav-link">Logout</a></li>
        </ul>
    </nav>    

