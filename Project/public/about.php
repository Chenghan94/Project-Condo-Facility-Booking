<?php require_once __DIR__ . '/../views/partials/header.php'; ?>

<h2>About Management Team</h2>
<p>Meet the condo management team that keeps your community running smoothly.</p>

<?php
// Edit this array with your real team info & image filenames
$team = [
  [
    'name' => 'OH CHENG HAN',
    'role' => 'Condo Manager',
    'email'=> 'manager@condo.sg',
    'phone'=> '+65 8888 0001',
    'img'  => '/Project/public/assets/images/team/manager.jpg',
    'links'=> [
      ['label'=>'Email', 'href'=>'mailto:manager@condo.sg'],
    ],
  ],
  [
    'name' => 'Alex Tan',
    'role' => 'Assistant Manager',
    'email'=> 'asst.manager@condo.sg',
    'phone'=> '+65 8888 0002',
    'img'  => '/Project/public/assets/images/team/asst_manager.jpg',
    'links'=> [
      ['label'=>'Email', 'href'=>'mailto:asst.manager@condo.sg'],
    ],
  ],
  [
    'name' => 'Priya Nair',
    'role' => 'Operations Lead',
    'email'=> 'ops@condo.sg',
    'phone'=> '+65 8888 0003',
    'img'  => '/Project/public/assets/images/team/ops.jpg',
    'links'=> [
      ['label'=>'Email', 'href'=>'mailto:ops@condo.sg'],
    ],
  ],
  [
    'name' => 'Jason Lim',
    'role' => 'IT / Systems',
    'email'=> 'it@condo.sg',
    'phone'=> '+65 8888 0004',
    'img'  => '/Project/public/assets/images/team/tech.jpg',
    'links'=> [
      ['label'=>'Email', 'href'=>'mailto:it@condo.sg'],
    ],
  ],
];
?>

<div class="section">
  <div class="team-grid">
    <?php foreach ($team as $m): ?>
      <div class="team-card">
        <img src="<?php echo htmlspecialchars($m['img']); ?>" alt="<?php echo htmlspecialchars($m['name']); ?>"
             onerror="this.src='https://via.placeholder.com/96?text=Photo';">
        <div class="team-name"><strong><?php echo htmlspecialchars($m['name']); ?></strong></div>
        <div class="team-role"><?php echo htmlspecialchars($m['role']); ?></div>
        <div class="team-meta"><?php echo htmlspecialchars($m['email']); ?> · <?php echo htmlspecialchars($m['phone']); ?></div>
        <?php if (!empty($m['links'])): ?>
          <div class="team-links" style="margin-top:8px;">
            <?php foreach ($m['links'] as $lnk): ?>
              <a href="<?php echo htmlspecialchars($lnk['href']); ?>"><?php echo htmlspecialchars($lnk['label']); ?></a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>
