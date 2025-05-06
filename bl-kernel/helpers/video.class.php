<?php defined('BLUDIT') or die('Bludit CMS.');

class Video
{
    private $uuid;
    public $filename; // Changed to public if using Option B

    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function upload($file, $subdir = 'videos')
    {
        // Validate file
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        // Get and validate extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['mp4', 'webm', 'ogg'];

        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }
        $safeName = Text::cleanUrl(pathinfo($file['name'], PATHINFO_FILENAME));

        // Create directory if needed
        $uploadDir = PATH_UPLOADS . $subdir . '/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate filename and move file
        $safeName = Text::cleanUrl(pathinfo($file['name'], PATHINFO_FILENAME));
        $this->filename = $safeName . '-' . $this->uuid . '.' . $extension;
        $destination = $uploadDir . $this->filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            chmod($destination, 0644);
            return true;
        }

        return false;
    }

    // Only needed if using Option A
    public function getFilename()
    {
        return $this->filename;
    }

    public function getPath()
    {
        return !empty($this->filename) ? 'videos/' . $this->filename : false;
    }
}