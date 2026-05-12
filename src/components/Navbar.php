<!-- Navbar Component -->
<nav>
    <a href="index.php" class="nav-logo">
        <div class="logo-icon">🌀</div>
        ICONNEX
    </a>
    <ul class="nav-links" id="navLinks">
        <li><a href="index.php#hero"><?php echo __('nav_home'); ?></a></li>
        <li><a href="index.php#courses"><?php echo __('nav_courses'); ?></a></li>
        <li><a href="index.php#portfolio"><?php echo __('nav_portfolio'); ?></a></li>
        <li><a href="index.php#contact"><?php echo __('nav_contact'); ?></a></li>
        <li class="lang-switcher">
            <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'th'])); ?>" class="<?php echo $current_lang === 'th' ? 'active' : ''; ?>">TH</a>
            <span>|</span>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'en'])); ?>" class="<?php echo $current_lang === 'en' ? 'active' : ''; ?>">EN</a>
        </li>
    </ul>
    
    <div class="nav-actions-wrapper" style="display: flex; align-items: center; gap: 20px;">
        <div class="nav-actions" style="display: flex; align-items: center; gap: 20px;">
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php renderComponent('notification/NotificationDropdown'); ?>
            <?php endif; ?>

            <a href="#" onclick="openCartModal(event)" style="position:relative; font-size: 1.2rem; color: #fff; text-decoration:none; transition: 0.3s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='#fff'">
                <i class="fas fa-shopping-cart"></i>
                <span id="cart-count" style="display:none; position:absolute; top:-8px; right:-12px; background:var(--gold); color:var(--navy-deep); border-radius:50%; width:18px; height:18px; font-size:.7rem; align-items:center; justify-content:center; font-weight:800; border: 2px solid var(--navy-deep);">0</span>
            </a>

            <?php if(isset($_SESSION['user_id'])): ?>
                <div style="display: flex; align-items: center; gap: 15px; border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px;">
                    <a href="my_orders.php" style="color: var(--gold-soft); font-size: 0.85rem; font-weight: 700; text-decoration:none;">
                        <i class="fas fa-history"></i> <?php echo __('nav_my_orders'); ?>
                    </a>
                    <a href="logout.php" style="background: rgba(255,255,255,0.05); color: #fff; padding: 6px 15px; border-radius: 20px; font-size: 0.75rem; text-decoration:none;">
                        <?php echo __('nav_logout'); ?>
                    </a>
                </div>
            <?php else: ?>
                <a href="login.php" style="background: var(--gold); color: var(--navy-deep); padding: 8px 25px; border-radius: 30px; font-weight: 800; text-decoration:none; font-size: 0.85rem;">
                    <?php echo __('btn_login'); ?>
                </a>
            <?php endif; ?>
        </div>
        
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>
