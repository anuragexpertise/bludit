<?php defined('BLUDIT') or die('Bludit CMS.');

class PluginVideoupload extends Plugin {

    public function init()
    {
        // Create upload directories
        $this->createDirectories();
    }

    private function createDirectories()
    {
        $dirs = [
            PATH_UPLOADS.'videos',
            PATH_UPLOADS.'videos/thumbs'
        ];

        foreach ($dirs as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    public function adminBodyEnd()
    {
        global $url;
        
        if ($url->whereAmI() === 'edit-page' || $url->whereAmI() === 'new-page') {
            return '
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const videoInput = document.getElementById("jsvideo");
                if (videoInput) {
                    videoInput.addEventListener("change", function(e) {
                        const preview = this.closest(".glassy-video-upload").querySelector(".video-preview");
                        if (preview) preview.remove();
                        
                        if (this.files && this.files[0]) {
                            const previewDiv = document.createElement("div");
                            previewDiv.className = "video-preview";
                            
                            const video = document.createElement("video");
                            video.controls = true;
                            video.src = URL.createObjectURL(this.files[0]);
                            
                            previewDiv.appendChild(video);
                            this.closest(".glassy-video-upload").appendChild(previewDiv);
                        }
                    });
                }
            });
            </script>';
        }
        return false;
    }

    public function beforePageModify()
    {
        global $content;
        
        // Handle video upload
        if (isset($_FILES['video']) && $_FILES['video']['error'] === 0) {
            $video = new Video();
            $video->setUUID($content->uuid());
            
            if ($video->upload($_FILES['video'])) {
                $content->setField('video', $video->getPath());
                
                if ($thumbnail = $video->generateThumbnail()) {
                    $content->setField('videoThumbnail', $thumbnail);
                }
            }
        }
        
        // Handle video deletion
        if (isset($_POST['delete-video']) && $content->getField('video')) {
            $video = new Video();
            $video->setUUID($content->uuid());
            $video->delete();
            
            $content->removeField('video');
            $content->removeField('videoThumbnail');
        }
    }
}