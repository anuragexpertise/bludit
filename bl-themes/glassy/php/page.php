<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<article class="glassy-content">
    <?php if ($page->custom('video-field-name')): ?>
        <div class="video-container">
            <video controls width="100%">
                <source src="<?php echo DOMAIN_UPLOADS . $page->custom('video-field-name'); ?>"
                    type="video/<?php echo pathinfo($page->custom('video-field-name'), PATHINFO_EXTENSION); ?>">
            </video>
        </div>
    <?php endif; ?>

    <!-- Rest of your existing page template -->
</article>