<?php
$menuItems = [
  'dashboard' => ['label' => 'Dashboard', 'icon' => '📊'],
  'district_allocation' => ['label' => 'District Allocation', 'icon' => '🧭'],
  'fdp_allocations' => ['label' => 'FDP Level Allocations', 'icon' => '🧾'],
  'fdp_distributions' => ['label' => 'FDP Level Distributions', 'icon' => '📦'],
];

$currentPage = isset($_GET['page']) ? (string) $_GET['page'] : 'dashboard';
if (!array_key_exists($currentPage, $menuItems)) {
  $currentPage = 'dashboard';
}

$pageTitle = $menuItems[$currentPage]['label'];
$pageFile = __DIR__ . '/pages/' . $currentPage . '.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> - Ready‑Copy Admin</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />

    <style>
      body { font-size: 0.95rem; }
      .sidebar { min-height: 100vh; }
      .sidebar .nav-link { color: #333; }
      .sidebar .nav-link.active { background: #0d6efd; color: #fff; }
      .dashboard-header { border-bottom: 1px solid rgba(0,0,0,0.075); }
      .card-metric .display-6 { font-weight: 600; }
      footer { border-top: 1px solid rgba(0,0,0,0.075); }
    </style>
  </head>
  <body>
    <nav class="navbar navbar-dark bg-dark sticky-top">
      <div class="container-fluid">
        <button class="navbar-toggler d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="?page=dashboard">Ready‑Copy Admin</a>
        <form class="d-none d-md-flex" role="search">
          <input class="form-control form-control-dark" type="search" placeholder="Search" aria-label="Search">
        </form>
        <div class="ms-auto d-flex align-items-center gap-2">
          <button class="btn btn-outline-light btn-sm" type="button">Settings</button>
          <a class="btn btn-primary btn-sm" href="?page=dashboard">Home</a>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
          <div class="position-sticky pt-3">
            <ul class="nav flex-column">
              <?php foreach ($menuItems as $key => $item): $isActive = $key === $currentPage ? 'active' : ''; ?>
                <li class="nav-item">
                  <a class="nav-link <?= $isActive ?>" href="?page=<?= urlencode($key) ?>">
                    <span class="me-2" aria-hidden="true"><?= htmlspecialchars($item['icon']) ?></span>
                    <?= htmlspecialchars($item['label']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pb-5">
          <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 pb-3 mb-4 dashboard-header">
            <h1 class="h3 m-0"><?= htmlspecialchars($pageTitle) ?></h1>
            <div class="d-flex align-items-center gap-2">
              <input type="date" class="form-control form-control-sm" aria-label="Start date">
              <span class="text-muted">to</span>
              <input type="date" class="form-control form-control-sm" aria-label="End date">
              <button class="btn btn-sm btn-outline-secondary" type="button">Export</button>
            </div>
          </div>

          <?php if (is_file($pageFile)) { include $pageFile; } else { ?>
            <div class="alert alert-warning">Content not found.</div>
          <?php } ?>

          <footer class="text-muted small mt-4 pt-3">
            <div class="d-flex flex-wrap justify-content-between">
              <span>© <span id="year"></span> Ready‑Copy</span>
              <div class="d-flex gap-3">
                <a class="link-secondary" href="#">Privacy</a>
                <a class="link-secondary" href="#">Terms</a>
                <a class="link-secondary" href="#">Support</a>
              </div>
            </div>
          </footer>
        </main>
      </div>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <script>
      document.getElementById('year').textContent = new Date().getFullYear();
    </script>
  </body>
</html>
