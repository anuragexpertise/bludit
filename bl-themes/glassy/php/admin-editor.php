<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<div class="glassy-video-upload">
    <label for="jsvideo"><?php echo $L->g('Video Upload') ?></label>
    <input type="file" name="video" id="jsvideo" accept="video/mp4,video/webm,video/ogg">
    
    <?php if ($content->getField('video')): ?>
    <div class="current-video">
        <video width="100%" controls>
            <source src="<?php echo DOMAIN_UPLOADS.$content->getField('video') ?>">
        </video>
        <label class="glassy-checkbox">
            <input type="checkbox" name="delete-video">
            <?php echo $L->g('Remove video') ?>
        </label>
    </div>
    <?php endif; ?>
</div>