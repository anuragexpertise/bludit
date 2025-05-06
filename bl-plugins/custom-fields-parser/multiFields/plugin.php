<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class pluginMultifields extends Plugin {
    // Maximum file upload size (5MB)
    const MAX_FILE_SIZE = 5242880;
    // Allowed image types
    const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    // Allowed video types
    const ALLOWED_VIDEO_TYPES = ['mp4', 'webm', 'ogg'];

    public function init() {
        try {
            $this->dbFields = [
                'fields' => json_encode([])
            ];
            
            if (empty($this->getValue('fields'))) {
                $this->loadDefaultFields();
            }
        } catch (Exception $e) {
            error_log('Multifields Plugin init error: '.$e->getMessage());
        }
    }

    private function loadDefaultFields() {
        try {
            $defaultFields = file_exists(__DIR__.'/fields.json') 
                ? file_get_contents(__DIR__.'/fields.json')
                : '{}';
            
            $this->setDbField('fields', $defaultFields);
            return $this->save();
        } catch (Exception $e) {
            error_log('Multifields Plugin Error: '.$e->getMessage());
            return false;
        }
    }

    public function getFields() {
        $fields = $this->getValue('fields');
        return !empty($fields) ? json_decode($fields, true) : [];
    }

    public function setFields(array $fields) {
        $this->setField('fields', json_encode($fields));
        return $this->save();
    }

    public function install($position = 0) {
        $uploadDir = PATH_UPLOADS.'multifields/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        return parent::install($position);
    }

    public function adminSidebar() {
        return '<a class="nav-link" href="'.HTML_PATH_ADMIN_ROOT.'plugin/multifields">Multifields Settings</a>';
    }

    public function adminController() {
        try {
            ob_start();
            
            $adminFile = __DIR__.'/admin/main.php';
            if (!file_exists($adminFile)) {
                throw new Exception("Admin file not found");
            }
            
            $plugin = $this;
            require($adminFile);
            
            return ob_get_clean();
        } catch (Exception $e) {
            if (ob_get_level() > 0) ob_end_clean();
            return '<div class="alert alert-danger">Plugin Error: '.htmlspecialchars($e->getMessage()).'</div>';
        }
    }

    public function pageForm() {
        global $page;
        
        ob_start();
        require(__DIR__.'/admin/content.php');
        return ob_get_clean();
    }

    private function saveCustomFields() {
        global $page;
        
        if (!isset($_POST['multifields']) && !isset($_FILES['multifields'])) {
            return;
        }

        $fields = $this->getValue('fields');
        
        foreach ($fields as $fieldName => $fieldConfig) {
            $key = 'mf_'.$fieldName;
            
            // Handle file deletions first
            if (isset($_POST['multifields']['delete_'.$fieldName])) {
                $this->deleteFile($page->custom($key));
                $page->setField($key, '');
                continue;
            }
            
            // Handle file uploads
            if ($fieldConfig['type'] === 'image' || $fieldConfig['type'] === 'video') {
                if (isset($_FILES['multifields']['name'][$fieldName]) && 
                    $_FILES['multifields']['error'][$fieldName] === UPLOAD_ERR_OK) {
                    $uploadResult = $this->handleFileUpload($fieldName, $page->slug(), $fieldConfig['type']);
                    if ($uploadResult) {
                        // Delete old file if exists
                        $oldFile = $page->custom($key);
                        if (!empty($oldFile)) {
                            $this->deleteFile($oldFile);
                        }
                        $page->setField($key, $uploadResult);
                    }
                }
            } 
            // Handle text inputs
            elseif (isset($_POST['multifields'][$fieldName])) {
                $page->setField($key, Sanitize::html($_POST['multifields'][$fieldName]));
            }
        }
    }

    private function deleteFile($filePath) {
        if (empty($filePath)) return false;
        
        // Convert URL to filesystem path
        $relativePath = str_replace(DOMAIN_UPLOADS, '', $filePath);
        $absolutePath = PATH_UPLOADS . ltrim($relativePath, '/');
        
        if (file_exists($absolutePath)) {
            return unlink($absolutePath);
        }
        return false;
    }

    private function handleFileUpload($fieldName, $slug, $type) {
        $uploadDir = PATH_UPLOADS.'multifields/'.$slug.'/';
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file = $_FILES['multifields'];
        $originalName = basename($file['name'][$fieldName]);
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $fileSize = $file['size'][$fieldName];
        
        // Validate file
        if ($fileSize > self::MAX_FILE_SIZE) {
            throw new Exception("File size exceeds maximum allowed size of 5MB");
        }
        
        if ($type === 'image' && !in_array($fileExt, self::ALLOWED_IMAGE_TYPES)) {
            throw new Exception("Invalid image type. Allowed: ".implode(', ', self::ALLOWED_IMAGE_TYPES));
        }
        
        if ($type === 'video' && !in_array($fileExt, self::ALLOWED_VIDEO_TYPES)) {
            throw new Exception("Invalid video type. Allowed: ".implode(', ', self::ALLOWED_VIDEO_TYPES));
        }
        
        // Generate unique filename
        $filename = uniqid().'.'.$fileExt;
        $target = $uploadDir.$filename;
        
        if (move_uploaded_file($file['tmp_name'][$fieldName], $target)) {
            return DOMAIN_UPLOADS.'multifields/'.$slug.'/'.$filename;
        }
        
        return false;
    }

    public function afterPageCreate() {
        $this->saveCustomFields();
    }

    public function afterPageModify() {
        $this->saveCustomFields();
    }
}