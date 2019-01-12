<ul>
    <li class="<?= ($furl == 'home' ? 'wci-active' : '') ?>"><a href="<?= site_url('index'); ?>">Home</a></li>
    <li class="<?= ($furl == 'work' ? 'wci-active' : '') ?>"><a href="<?= site_url('work'); ?>">Project</a></li>
    <li class="<?= ($furl == 'about' ? 'wci-active' : '') ?>"><a href="<?= site_url('about'); ?>">About</a></li>
    <li class="<?= ($furl == 'services' ? 'wci-active' : '') ?>"><a href="<?= site_url('services'); ?>">Services</a></li>
    <li class="<?= ($furl == 'blog' ? 'wci-active' : '') ?>"><a href="<?= site_url('blog'); ?>">Blog</a></li>
    <li class="<?= ($furl == 'contact' ? 'wci-active' : '') ?>"><a href="<?= site_url('contact'); ?>">Contact</a></li>
</ul>