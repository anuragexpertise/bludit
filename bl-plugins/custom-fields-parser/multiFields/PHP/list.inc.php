<h3>MultiFields list</h3>

<style>
    .multifield-list {
        list-style-type: none;
        margin: 0 !important;
        padding: 0 !important;
    }

    .multifield-list li {
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: solid 1px #ddd;
    }

    .multifield-list li:nth-child(2) {
        background: #fafafa;
    }

    .multifield-list li a {
        margin-left: 5px;
        background: `linear-gradient(to bottom, #146C94, #009FBD);
        padding: 5px;
        color: #fff !important;
        text-decoration: none !important;
        display: inline-block;
        border-radius: 5px;
    }

    .multifield-list li a img {
        padding: 0;
        margin: 0;
    }

    .multifield-list li a:nth-child(2) {
        background: #D21312;
    }

    .multifield-list li p {
        color: #000;
        font-weight: bold;
        margin: 0 !important;
        font-size: 14px;
        font-style: italic;
        margin: 0 !important;
        padding: 0 !important;
    }

    .btn {
        background: #146C94;
        color: #fff !important;
        text-decoration: none !important;
        margin: 0;
        padding: 10px;
        display: inline-block;
        margin: 3px;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
    }
</style>


<?php
global $SITEURL;
global $GSADMIN;

$url = $link;
?>

<a href="<?php echo $url . '?&creator'; ?>" class="btn">Add New <img style="width:20px;filter:invert(100%);margin-left:5px;" src="<?php echo $thisDomainPath . "img/plus.svg"; ?>"></a>
<a href="<?php echo $url . '?&migrate'; ?>" class="btn">Migrate <img style="width:18px;filter:invert(100%);margin-left:5px;" src="<?php echo $thisDomainPath . "img/web.svg"; ?>"></a>
<a href="<?php echo $url . '?&howtouse'; ?>" class="btn">How to use <img style="width:18px;filter:invert(100%);margin-left:5px;" src="<?php echo $thisDomainPath . "img/web.svg"; ?>"></a>
<hr>
<ul class="multifield-list">


    <?php foreach (glob($pathContent . '*-settings.json') as $file) {


        $new = new Pages();
        $filePure = str_replace('-settings', '', pathinfo($file)['filename']);
        @$title = $new->db[$filePure]['title'];



        echo '
<li>
<p>';
        if ($filePure == 'allmultifield') {
            echo 'All';
        } else {
            echo  @$title ? $title : 'Page no exist';
        };

        echo '</p>
<div class="btns"><a href="' . $url . '?&creator=' . $filePure . '-settings"> <img style="width:18px;;margin-left:5px;" src="' . $thisDomainPath . 'img/edit.svg"></a>
<a href="' . $url . '?&delete=' . $filePure . '-settings" onclick="return confirm(`are you sure you want delete this item`)"><img style="width:18px;;filter:invert(100%)" src="' . $thisDomainPath . 'img/trash.svg"></a></div>
</li>
';
    }; ?>

</ul>