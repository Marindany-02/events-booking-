<div class="sidebar">
    <div class="sidebar-header">
        <h3>Admin Dashboard</h3>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="admin_dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="event_management.php">
                <i class="bi bi-calendar-event"></i> Event Management
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="admin_settings.php">
                <i class="bi bi-gear"></i> Admin Settings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="profile.php">
                <i class="bi bi-person-circle"></i> Profile
            </a>
        </li>
        <!-- Logout Button -->
        <li class="nav-item">
            <a class="nav-link" href="logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </ul>
</div>

<style>
    /* General sidebar styling */
    .sidebar {
        width: 250px;
        background-color: #343a40;
        color: #fff;
        position: fixed;
        top: 0;
        bottom: 0;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, width 0.3s ease;
    }

    .sidebar a {
        color: #ddd;
        text-decoration: none;
        display: block;
        padding: 10px;
        margin: 5px 0;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .sidebar a:hover {
        background-color: #495057;
    }

    .sidebar-header {
        padding: 15px;
        background-color: #23272b;
        text-align: center;
        font-size: 1.25rem;
        font-weight: bold;
    }

    /* Small screen (mobile) adjustments */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar a {
            text-align: center;
        }

        .sidebar-header {
            font-size: 1rem;
        }
    }

    /* Toggle button */
    .sidebar-toggle {
        display: none;
        background-color: #343a40;
        color: #fff;
        border: none;
        padding: 10px;
        font-size: 1.25rem;
        cursor: pointer;
        position: fixed;
        top: 10px;
        left: 10px;
        z-index: 1100;
    }

    @media (max-width: 768px) {
        .sidebar-toggle {
            display: block;
        }
    }
</style>

<!-- Toggle Button -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.sidebar');
        const toggleButton = document.getElementById('sidebarToggle');

        toggleButton.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    });
</script>
