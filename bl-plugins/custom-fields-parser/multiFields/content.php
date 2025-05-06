<?php

// Security check
if (!defined('BLUDIT')) {
    die('Unauthorized access.');
}

// Get fields list from plugin settings
$fields = $plugin->getValue('fields');

// Only show if there are fields
if (!empty($fields)) {
    echo '<div class="plugin-multifield mt-4">';
    echo '<h2>Extra Fields</h2>';

    foreach ($fields as $fieldKey => $fieldType) {
        $fieldLabel = ucfirst($fieldKey);
        $fieldName = 'multifield-' . $fieldKey;
        $fieldValue = '';

        // When editing existing page, load saved custom field value
        if (isset($page)) {
            $fieldValue = $page->custom($fieldKey) !== null ? $page->custom($fieldKey) : '';
        }

        echo '<div class="form-group">';
        echo '<label for="' . $fieldName . '">' . $fieldLabel . '</label>';

        if ($fieldType === 'string') {
            echo '<input type="text" id="' . $fieldName . '" name="' . $fieldName . '" class="form-control" value="' . Sanitize::html($fieldValue) . '">';
        } elseif ($fieldType === 'image' || $fieldType === 'video') {
            echo '<input type="file" id="' . $fieldName . '" name="' . $fieldName . '" class="form-control">';

            // Show existing uploaded file
            if (!empty($fieldValue)) {
                $fileUrl = DOMAIN_UPLOADS . 'multiField/' . $page->slug() . '/' . $fieldValue;
                if ($fieldType === 'image') {
                    echo '<div class="mt-2"><img src="' . $fileUrl . '" alt="' . $fieldLabel . '" style="max-width:200px;"></div>';
                } elseif ($fieldType === 'video') {
                    echo '<div class="mt-2">
                            <video width="320" height="240" controls>
                                <source src="' . $fileUrl . '" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                          </div>';
                }
            }
        }
        echo '</div>'; // .form-group
    }

    echo '</div>'; // .plugin-multifield
}
?>
