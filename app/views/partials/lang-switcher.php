<?php if (!class_exists('I18n')) return; ?>
<div class="lang-switcher">
  <a href="?lang=en" class="<?= I18n::getLang()==='en'?'active':'' ?>">EN</a>
  <a href="?lang=si" class="<?= I18n::getLang()==='si'?'active':'' ?>">සිං</a>
  <a href="?lang=ta" class="<?= I18n::getLang()==='ta'?'active':'' ?>">தமிழ்</a>
</div>
