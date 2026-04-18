<!-- partials/lanh-switcher.php -->
<?php if (!class_exists('I18n')) return; ?>
<div class="lang-switcher">
  <a data-e2e="lang-en" href="?lang=en" class="<?= I18n::getLang()==='en'?'active':'' ?>">EN</a>
  <a data-e2e="lang-si" href="?lang=si" class="<?= I18n::getLang()==='si'?'active':'' ?>">සිං</a>
  <a data-e2e="lang-ta" href="?lang=ta" class="<?= I18n::getLang()==='ta'?'active':'' ?>">தமிழ்</a>
</div>

