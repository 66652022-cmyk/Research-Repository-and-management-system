<!-- admin_nav.php -->
<header class="bg-royal-blue py-4 shadow-lg">
  <div class="container mx-auto px-4 flex justify-between items-center">
    <div>
      <h1 class="text-white text-xl font-bold">Research Management System</h1>
      <p class="text-white opacity-90 mt-1"><?php echo $greeting; ?></p>
    </div>

    <div class="text-white text-right">
      <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong><br>
      <span><?php echo ucfirst(str_replace('_', ' ', $_SESSION['admin_role'])); ?></span><br>
      <small><?php echo htmlspecialchars($_SESSION['admin_email']); ?></small><br>
      <small>Login: <?php echo date('Y-m-d H:i:s', $_SESSION['admin_login_time']); ?></small><br>
    </div>
  </div>
</header>
