<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>RMS Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f6fa; }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 { margin: 0; font-size: 24px; }
        .header .user-info { font-size: 14px; }
        .main-content { padding: 30px; max-width: 1200px; margin: 0 auto; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { 
            background: white; 
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }
        .stat-number { font-size: 36px; font-weight: bold; color: #667eea; margin-bottom: 5px; }
        .stat-label { color: #666; font-size: 14px; }
        .quick-actions { 
            background: white; 
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        .quick-actions h3 { margin-top: 0; color: #333; }
        .action-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px; }
        .action-btn { 
            padding: 15px 20px; 
            background: #f8f9fa; 
            border: 1px solid #e9ecef; 
            border-radius: 8px; 
            text-decoration: none; 
            color: #495057; 
            text-align: center;
            transition: all 0.3s;
        }
        .action-btn:hover { 
            background: #667eea; 
            color: white; 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.3);
        }
        .logout-btn { 
            background: #e74c3c; 
            color: white; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 6px; 
            transition: background 0.3s;
        }
        .logout-btn:hover { background: #c0392b; }
        .role-badge { 
            display: inline-block; 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: bold; 
            margin-left: 10px;
        }
        .role-super_admin { background: #e74c3c; color: white; }
        .role-research_director { background: #3498db; color: white; }
        .role-adviser { background: #27ae60; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Research Management System</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;"><?php echo $greeting; ?></p>
        </div>
        <div class="user-info">
            <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong>
            <span class="role-badge role-<?php echo $_SESSION['admin_role']; ?>">
                <?php echo ucfirst(str_replace('_', ' ', $_SESSION['admin_role'])); ?>
            </span>
            <br>
            <small><?php echo htmlspecialchars($_SESSION['admin_email']); ?></small>
            <br>
            <small>Login: <?php echo date('Y-m-d H:i:s', $_SESSION['admin_login_time']); ?></small>
            <br>
            <a href="/admin/logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['active_users']; ?></div>
                <div class="stat-label">Active Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_students']; ?></div>
                <div class="stat-label">Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_admins']; ?></div>
                <div class="stat-label">Administrators</div>
            </div>
        </div>
        
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-grid">
                <a href="/admin/manage_users.php" class="action-btn">Manage Users</a>
                <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
                    <a href="/admin/manage_admins.php" class="action-btn">Manage Admins</a>
                    <a href="/admin/system_settings.php" class="action-btn">System Settings</a>
                <?php endif; ?>
                <?php if (in_array($_SESSION['admin_role'], ['super_admin', 'research_director'])): ?>
                    <a href="/admin/research_management.php" class="action-btn">Research Management</a>
                <?php endif; ?>
                <?php if (in_array($_SESSION['admin_role'], ['super_admin', 'research_director', 'adviser'])): ?>
                    <a href="/admin/student_monitoring.php" class="action-btn">Student Monitoring</a>
                <?php endif; ?>
                <a href="/admin/reports.php" class="action-btn">Reports</a>
                <a href="/admin/activity_logs.php" class="action-btn">Activity Logs</a>
            </div>
        </div>
        
        <?php if ($_SESSION['admin_specialization']): ?>
        <div class="quick-actions">
            <h3>Your Profile</h3>
            <p><strong>Specialization:</strong> <?php echo htmlspecialchars($_SESSION['admin_specialization']); ?></p>
            <?php if ($_SESSION['admin_course']): ?>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($_SESSION['admin_course']); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>