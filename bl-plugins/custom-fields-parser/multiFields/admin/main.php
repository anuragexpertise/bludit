<?php
if (!defined('BLUDIT')) {
    die('Unauthorized access.');
}

global $L, $security, $plugin;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get current fields
    $fields = $plugin->getFields();

    // Handle form submission
    if (isset($_POST['saveFields'])) {
        if (!$security->validateTokenCSRF($_POST['tokenCSRF'])) {
            throw new Exception('Invalid CSRF token');
        }

        $newFields = [];
        
        if (isset($_POST['field']) && is_array($_POST['field'])) {
            foreach ($_POST['field'] as $index => $fieldName) {
                $fieldName = Sanitize::slug($fieldName);
                if (!empty($fieldName)) {
                    $newFields[$fieldName] = [
                        'type' => $_POST['type'][$index] ?? 'string',
                        'label' => $_POST['label'][$index] ?? ucfirst($fieldName),
                        'tip' => $_POST['tip'][$index] ?? '',
                        'position' => $_POST['position'][$index] ?? 'top'
                    ];
                }
            }
            
            if ($plugin->setFields($newFields)) {
                header('Location: '.$_SERVER['REQUEST_URI']);
                exit();
            }
        }
    }

    // Handle field deletion
    if (isset($_GET['delete']) && $security->validateTokenCSRF($_GET['tokenCSRF'])) {
        $fieldToDelete = Sanitize::slug($_GET['delete']);
        if (isset($fields[$fieldToDelete])) {
            unset($fields[$fieldToDelete]);
            if ($plugin->setFields($fields)) {
                header('Location: '.strtok($_SERVER['REQUEST_URI'], '?'));
                exit();
            }
        }
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>


<div class="container mt-4">
    <h2><?php echo $L->g('Multifields Manager'); ?></h2>
    
    <form method="post" class="mb-4">
        <input type="hidden" name="tokenCSRF" value="<?php echo $security->getTokenCSRF(); ?>">
        
        <div class="table-responsive">
            <table class="table table-striped mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>Field Name (Slug)</th>
                        <th>Type</th>
                        <th>Label</th>
                        <th>Tooltip</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="fieldsContainer">
                    <?php foreach ($fields as $name => $config): ?>
                    <tr class="field-row">
                        <td>
                            <input type="text" name="field[]" class="form-control" 
                                   value="<?php echo htmlspecialchars($name); ?>" required>
                        </td>
                        <td>
                            <select name="type[]" class="form-control">
                                <option value="string" <?php echo ($config['type'] === 'string') ? 'selected' : ''; ?>>Text</option>
                                <option value="image" <?php echo ($config['type'] === 'image') ? 'selected' : ''; ?>>Image</option>
                                <option value="video" <?php echo ($config['type'] === 'video') ? 'selected' : ''; ?>>Video</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="label[]" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['label']); ?>">
                        </td>
                        <td>
                            <input type="text" name="tip[]" class="form-control" 
                                   value="<?php echo htmlspecialchars($config['tip']); ?>">
                        </td>
                        <td>
                            <select name="position[]" class="form-control">
                                <option value="top" <?php echo ($config['position'] === 'top') ? 'selected' : ''; ?>>Top</option>
                                <option value="bottom" <?php echo ($config['position'] === 'bottom') ? 'selected' : ''; ?>>Bottom</option>
                            </select>
                        </td>
                        <td>
                            <a href="?delete=<?php echo urlencode($name); ?>&tokenCSRF=<?php echo $security->getTokenCSRF(); ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this field?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($fields)): ?>
                    <tr class="field-row">
                        <td>
                            <input type="text" name="field[]" class="form-control" required>
                        </td>
                        <td>
                            <select name="type[]" class="form-control">
                                <option value="string">Text</option>
                                <option value="image">Image</option>
                                <option value="video">Video</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="label[]" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="tip[]" class="form-control">
                        </td>
                        <td>
                            <select name="position[]" class="form-control">
                                <option value="top">Top</option>
                                <option value="bottom">Bottom</option>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mb-3">
            <button type="button" id="addFieldBtn" class="btn btn-secondary mr-2">
                <i class="fas fa-plus"></i> Add Field
            </button>
            <button type="submit" name="saveFields" class="btn btn-primary">
                <i class="fas fa-save"></i> Save All Fields
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add new field row
    document.getElementById('addFieldBtn').addEventListener('click', function() {
        const container = document.getElementById('fieldsContainer');
        const newRow = document.createElement('tr');
        newRow.className = 'field-row';
        newRow.innerHTML = `
            <td>
                <input type="text" name="field[]" class="form-control" required>
            </td>
            <td>
                <select name="type[]" class="form-control">
                    <option value="string">Text</option>
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                </select>
            </td>
            <td>
                <input type="text" name="label[]" class="form-control">
            </td>
            <td>
                <input type="text" name="tip[]" class="form-control">
            </td>
            <td>
                <select name="position[]" class="form-control">
                    <option value="top">Top</option>
                    <option value="bottom">Bottom</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        container.appendChild(newRow);
        
        // Add event listener to remove button
        newRow.querySelector('.remove-row').addEventListener('click', function() {
            newRow.remove();
        });
    });
    
    // Add remove functionality to existing rows
    document.querySelectorAll('.remove-row').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('tr').remove();
        });
    });
});
</script>