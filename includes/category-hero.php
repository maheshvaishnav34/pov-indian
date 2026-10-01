<?php
$hero_variant = $landing['hero_variant'] ?? 'cinematic';
$hero_src = pov_url($cat['hero'] ?? 'assets/img/ui/banner.webp');
$cta_primary = $landing['cta_primary'] ?? 'Explore Listings';
$cta_secondary = $landing['cta_secondary'] ?? 'List Your Business';
$niche_preview = array_slice($niches, 0, 4);
?>

<?php if ($hero_variant === 'cinematic'): /* Hotels */ ?>
<section class="phero phero--cinematic">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--west"></div>
  <div class="container">
    <div class="phero-frame">
      <p class="phero-eyebrow">POV Indian Stay Collection</p>
      <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
      <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
      <div class="phero-actions">
        <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
        <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
      </div>
      <?php if ($stats): ?>
      <div class="phero-metrics">
        <?php foreach ($stats as $stat): ?>
          <div><strong><?= htmlspecialchars($stat['value']) ?></strong><span><?= htmlspecialchars($stat['label']) ?></span></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php elseif ($hero_variant === 'split-media'): /* Restaurants */ ?>
<section class="phero phero--dining">
  <div class="phero-dining-stage">
    <img src="<?= htmlspecialchars($hero_src) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" />
  </div>
  <div class="container phero-dining-copy">
    <div class="phero-dining-inner">
      <p class="phero-eyebrow">Culinary POV</p>
      <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
      <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
      <?php if ($niche_preview): ?>
      <div class="phero-chips">
        <?php foreach ($niche_preview as $n): ?>
          <a href="#listings"><?= htmlspecialchars($n['name']) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="phero-actions">
        <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
        <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
      </div>
    </div>
  </div>
</section>

<?php elseif ($hero_variant === 'center-stack'): /* Colleges */ ?>
<section class="phero phero--campus">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--west"></div>
  <div class="container phero-frame">
    <p class="phero-eyebrow">Student Perspectives</p>
    <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
    <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
    <div class="phero-actions">
      <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
      <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
    </div>
    <?php if ($stats): ?>
    <div class="phero-metrics">
      <?php foreach ($stats as $stat): ?>
        <div><strong><?= htmlspecialchars($stat['value']) ?></strong><span><?= htmlspecialchars($stat['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php elseif ($hero_variant === 'script-calm'): /* Wellness */ ?>
<section class="phero phero--wellness">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--west"></div>
  <div class="container phero-frame">
    <p class="phero-eyebrow">Holistic & Ayurvedic Care</p>
    <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
    <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
    <div class="phero-actions">
      <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
      <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
    </div>
    <?php if ($stats): ?>
    <div class="phero-metrics">
      <?php foreach ($stats as $stat): ?>
        <div><strong><?= htmlspecialchars($stat['value']) ?></strong><span><?= htmlspecialchars($stat['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php elseif ($hero_variant === 'panel-right'): /* Real estate */ ?>
<section class="phero phero--estate">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--west"></div>
  <div class="container phero-frame">
    <p class="phero-eyebrow accent">Property POV</p>
    <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
    <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
    <div class="phero-actions">
      <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
      <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
    </div>
    <?php if ($stats): ?>
    <div class="phero-metrics">
      <?php foreach ($stats as $stat): ?>
        <div><strong><?= htmlspecialchars($stat['value']) ?></strong><span><?= htmlspecialchars($stat['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php elseif ($hero_variant === 'search-bottom'): /* Tourism */ ?>
<section class="phero phero--travel">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--south"></div>
  <div class="container phero-frame phero-frame--travel">
    <p class="phero-eyebrow">Beyond the checklist</p>
    <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
    <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
    <div class="phero-actions">
      <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
      <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
    </div>
    <?php if ($niche_preview): ?>
    <div class="phero-route">
      <?php foreach ($niche_preview as $n): ?>
        <a href="#listings"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($n['name']) ?></a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php elseif ($hero_variant === 'trust-split'): /* Healthcare */ ?>
<section class="phero phero--care">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--west"></div>
  <div class="container phero-frame">
    <p class="phero-eyebrow accent">Trusted Care Network</p>
    <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
    <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
    <div class="phero-trust">
      <span><i class="fa-solid fa-shield-halved"></i> Verified profiles</span>
      <span><i class="fa-solid fa-user-doctor"></i> Specialty clarity</span>
      <span><i class="fa-solid fa-phone"></i> Direct contacts</span>
    </div>
    <div class="phero-actions">
      <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
      <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
    </div>
    <?php if ($stats): ?>
    <div class="phero-metrics">
      <?php foreach ($stats as $stat): ?>
        <div><strong><?= htmlspecialchars($stat['value']) ?></strong><span><?= htmlspecialchars($stat['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php elseif ($hero_variant === 'bold-industrial'): /* Manufacturing */ ?>
<section class="phero phero--industry">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--west"></div>
  <div class="container phero-frame">
    <p class="phero-eyebrow accent">Industrial Network</p>
    <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
    <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
    <div class="phero-actions">
      <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
      <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
    </div>
    <?php if ($stats): ?>
    <div class="phero-metrics">
      <?php foreach ($stats as $stat): ?>
        <div><strong><?= htmlspecialchars($stat['value']) ?></strong><span><?= htmlspecialchars($stat['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php elseif ($hero_variant === 'editorial'): /* Legal */ ?>
<section class="phero phero--legal">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--west"></div>
  <div class="container phero-frame">
    <p class="phero-eyebrow accent">Professional Counsel</p>
    <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
    <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
    <div class="phero-actions">
      <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
      <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
    </div>
    <?php if ($stats): ?>
    <div class="phero-metrics">
      <?php foreach ($stats as $stat): ?>
        <div><strong><?= htmlspecialchars($stat['value']) ?></strong><span><?= htmlspecialchars($stat['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php elseif ($hero_variant === 'bottom-bar'): /* Home & garden */ ?>
<section class="phero phero--home">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--west"></div>
  <div class="container phero-frame">
    <p class="phero-eyebrow accent">Home Living · POV Indian</p>
    <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
    <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
    <div class="phero-actions">
      <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
      <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
    </div>
    <?php if ($stats): ?>
    <div class="phero-metrics">
      <?php foreach ($stats as $stat): ?>
        <div><strong><?= htmlspecialchars($stat['value']) ?></strong><span><?= htmlspecialchars($stat['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php elseif ($hero_variant === 'diagonal'): /* Auto */ ?>
<section class="phero phero--auto">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--west"></div>
  <div class="container phero-frame">
    <p class="phero-eyebrow accent">Driver Trusted</p>
    <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
    <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
    <div class="phero-actions">
      <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
      <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
    </div>
    <?php if ($stats): ?>
    <div class="phero-metrics">
      <?php foreach ($stats as $stat): ?>
        <div><strong><?= htmlspecialchars($stat['value']) ?></strong><span><?= htmlspecialchars($stat['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php else: /* Beauty */ ?>
<section class="phero phero--beauty">
  <img class="phero-bg" src="<?= htmlspecialchars($hero_src) ?>" alt="" />
  <div class="phero-veil phero-veil--west"></div>
  <div class="container phero-frame">
    <p class="phero-eyebrow accent">Studio Standards</p>
    <h1 class="phero-title"><?= htmlspecialchars($headline) ?></h1>
    <p class="phero-text"><?= htmlspecialchars($lead) ?></p>
    <div class="phero-actions">
      <a class="btn" href="#listings"><?= htmlspecialchars($cta_primary) ?></a>
      <a class="btn btn-white" href="<?= htmlspecialchars(pov_url('list-business.php')) ?>"><?= htmlspecialchars($cta_secondary) ?></a>
    </div>
    <?php if ($stats): ?>
    <div class="phero-metrics">
      <?php foreach ($stats as $stat): ?>
        <div><strong><?= htmlspecialchars($stat['value']) ?></strong><span><?= htmlspecialchars($stat['label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>
