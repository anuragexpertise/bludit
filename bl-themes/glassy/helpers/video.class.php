<?php defined('BLUDIT') or die('Bludit CMS.');

class Video {

    private $uuid;
    private $filename;

    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function upload($file)
    {
        // Validate
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        // Check extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['mp4', 'webm', 'ogg'])) {
            return false;
        }

        // Prepare destination
        $this->filename = $this->uuid . '.' . $ext;
        $destination = PATH_UPLOADS . 'videos/' . $this->filename;

        return move_uploaded_file($file['tmp_name'], $destination);
    }

    public function generateThumbnail()
    {
        if (empty($this->filename)) return false;

        $videoPath = PATH_UPLOADS . 'videos/' . $this->filename;
        $thumbPath = PATH_UPLOADS . 'videos/thumbs/' . pathinfo($this->filename, PATHINFO_FILENAME) . '.jpg';

        $command = "ffmpeg -i {$videoPath} -ss 00:00:01 -vframes 1 -q:v 2 {$thumbPath} 2>&1";
        shell_exec($command);

        return file_exists($thumbPath) ? 'videos/thumbs/' . basename($thumbPath) : false;
    }

    public function delete()
    {
        if (empty($this->filename)) return false;

        $paths = [
            PATH_UPLOADS . 'videos/' . $this->filename,
            PATH_UPLOADS . 'videos/thumbs/' . pathinfo($this->filename, PATHINFO_FILENAME) . '.jpg'
        ];

        $success = true;
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $success = $success && unlink($path);
            }
        }

        return $success;
    }

    public function getPath()
    {
        return empty($this->filename) ? false : 'videos/' . $this->filename;
    }
}