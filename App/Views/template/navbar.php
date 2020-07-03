<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="<?= baseUrl() ?>">Project Home</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item <?= isset($menu) && $menu === 'hm' ? 'active' : '' ?>">
          <a class="nav-link" href="<?= baseUrl('form') ?>">Data Form <?= isset($menu) && $menu === 'hm' ? '<span class="sr-only">(current)</span>' : '' ?></a>
      </li>
      <li class="nav-item <?= isset($menu) && $menu === 'rp' ? 'active' : '' ?>">
        <a class="nav-link" href="<?= baseUrl('report') ?>">Report <?= isset($menu) && $menu === 'rp' ? '<span class="sr-only">(current)</span>' : '' ?></a>
      </li>
    </ul>
  </div>
</nav>
