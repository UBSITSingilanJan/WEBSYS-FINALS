<?php
$activePage = $activePage ?? '';
$navigation = [
    'dashboard' => ['index.php', 'Dashboard'],
    'residents' => ['resident_management.php', 'Manage Residents'],
    'applications' => ['manage_applications.php', 'Manage Applications'],
    'programs' => ['manage_programs.php', 'Manage Programs'],
    'search' => ['search.php', 'Search Residents'],
    'distributions' => ['distributions.php', 'Distributions'],
    'logout' => ['logout.php', 'Logout'],
];
?>
<input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
<label for="sidebar-toggle" class="hamburger" aria-label="Toggle navigation">☰</label>
<aside class="sidebar">
    <h3>Financial Aid System</h3>
    <?php foreach ($navigation as $page => $item): ?>
        <a href="<?php echo htmlspecialchars($item[0], ENT_QUOTES, 'UTF-8'); ?>"<?php echo $activePage === $page ? ' class="active"' : ''; ?>>
            <?php echo htmlspecialchars($item[1], ENT_QUOTES, 'UTF-8'); ?>
        </a>
    <?php endforeach; ?>
</aside>
