<style>
    header {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .header-item {
        text-align: center;
    }

    .h2 {
        color: #333;
        /* font-weight: bold; */
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
        height: 45px;
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
        padding: 7px 10px;
        cursor: pointer;
        text-decoration: none;
        border-radius: 4px;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .nav-link:hover {
        background-color: #555;
        transform: scale(1.2);
        font-weight: bold;
    }

    li {
        color: white;
    }
</style>


    <nav>
        <ul>
            <li><a href="adminDashboard.php" class="nav-link">Dashboard</a></li>
            <li>|</li>
            <li><a href="viewStudent.php" class="nav-link">Students Details</a></li>
            <li>|</li>
            <li><a href="refundRequests.php" class="nav-link">No Dues Details</a></li>
            <li>|</li>
            <li><a href="departmentManagement.php" class="nav-link">Department Management</a></li>
            <li>|</li>
            <li><a href="adminLogout.php" class="nav-link">Logout</a></li>
        </ul>
    </nav>    

