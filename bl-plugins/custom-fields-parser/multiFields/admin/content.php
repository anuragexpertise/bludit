<?php
if (!defined('BLUDIT')) {
    die('Unauthorized access.');
}

global $page, $plugin;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $fields = $plugin->getValue('fields');
    if (!is_array($fields)) {
        $fields = [];
    }

    // Group fields by position
    $topFields = [];
    $bottomFields = [];
    
    foreach ($fields as $fieldName => $config) {
        if (!is_array($config)) continue;
        
        $position = isset($config['position']) ? $config['position'] : 'top';
        if ($position === 'bottom') {
            $bottomFields[$fieldName] = $config;
        } else {
            $topFields[$fieldName] = $config;
        }
    }

    // Display top fields first
    if (!empty($topFields)): ?>
    <div class="form-group mt-4">
        <h3><i class="fas fa-cog"></i> Additional Fields</h3>
        
        <?php foreach ($topFields as $fieldName => $config): 
            $fieldKey = 'mf_'.$fieldName;
            $fieldValue = $page->custom($fieldKey) ?? '';
        ?>
        <div class="mb-3">
            <label class="form-label">
                <?php echo isset($config['label']) ? htmlspecialchars($config['label']) : ucfirst($fieldName); ?>
            </label>
            
            <?php if (!empty($config['tip'])): ?>
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($config['tip']); ?>
            </small>
            <?php endif; ?>
            
            <?php if ($config['type'] === 'string'): ?>
                <input type="text" name="multifields[<?php echo htmlspecialchars($fieldName); ?>]" 
                       class="form-control" 
                       value="<?php echo htmlspecialchars($fieldValue); ?>">
                       
            <?php elseif ($config['type'] === 'image' || $config['type'] === 'video'): ?>
                <input type="file" name="multifields[<?php echo htmlspecialchars($fieldName); ?>]" 
                       class="form-control-file" 
                       accept="<?php echo $config['type'] === 'image' ? 'image/*' : 'video/*'; ?>">
                
                <?php if (!empty($fieldValue)): ?>
                    <div class="mt-2">
                        <?php if ($config['type'] === 'image'): ?>
                            <img src="<?php echo htmlspecialchars($fieldValue); ?>" 
                                 style="max-width: 200px; max-height: 200px;" 
                                 class="img-thumbnail d-block">
                        <?php else: ?>
                            <video src="<?php echo htmlspecialchars($fieldValue); ?>" 
                                   controls style="max-width: 200px;" class="d-block">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>
                        
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" 
                                   name="multifields[delete_<?php echo htmlspecialchars($fieldName); ?>]" 
                                   id="delete_<?php echo htmlspecialchars($fieldName); ?>">
                            <label class="form-check-label text-danger" for="delete_<?php echo htmlspecialchars($fieldName); ?>">
                                <i class="fas fa-trash"></i> Remove current file
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif;

    // Display bottom fields after main content
    if (!empty($bottomFields)): ?>
    <div class="form-group mt-5">
        <h3><i class="fas fa-ellipsis-h"></i> Additional Bottom Fields</h3>
        
        <?php foreach ($bottomFields as $fieldName => $config): 
            $fieldKey = 'mf_'.$fieldName;
            $fieldValue = $page->custom($fieldKey) ?? '';
        ?>
        <div class="mb-3">
            <label class="form-label">
                <?php echo isset($config['label']) ? htmlspecialchars($config['label']) : ucfirst($fieldName); ?>
            </label>
            
            <?php if (!empty($config['tip'])): ?>
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($config['tip']); ?>
            </small>
            <?php endif; ?>
            
            <?php if ($config['type'] === 'string'): ?>
                <input type="text" name="multifields[<?php echo htmlspecialchars($fieldName); ?>]" 
                       class="form-control" 
                       value="<?php echo htmlspecialchars($fieldValue); ?>">
                       
            <?php elseif ($config['type'] === 'image' || $config['type'] === 'video'): ?>
                <input type="file" name="multifields[<?php echo htmlspecialchars($fieldName); ?>]" 
                       class="form-control-file" 
                       accept="<?php echo $config['type'] === 'image' ? 'image/*' : 'video/*'; ?>">
                
                <?php if (!empty($fieldValue)): ?>
                    <div class="mt-2">
                        <?php if ($config['type'] === 'image'): ?>
                            <img src="<?php echo htmlspecialchars($fieldValue); ?>" 
                                 style="max-width: 200px; max-height: 200px;" 
                                 class="img-thumbnail d-block">
                        <?php else: ?>
                            <video src="<?php echo htmlspecialchars($fieldValue); ?>" 
                                   controls style="max-width: 200px;" class="d-block">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>
                        
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" 
                                   name="multifields[delete_<?php echo htmlspecialchars($fieldName); ?>]" 
                                   id="delete_<?php echo htmlspecialchars($fieldName); ?>">
                            <label class="form-check-label text-danger" for="delete_<?php echo htmlspecialchars($fieldName); ?>">
                                <i class="fas fa-trash"></i> Remove current file
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif;

} catch (Exception $e) {
    error_log('Multifields content error: ' . $e->getMessage());
    echo '<div class="alert alert-danger">Error loading custom fields: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>