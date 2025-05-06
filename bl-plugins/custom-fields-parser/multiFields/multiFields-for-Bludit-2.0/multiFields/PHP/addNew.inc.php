<style>
    .form input {
        width: 100%;
        padding: 10px;
        box-sizing: border-box !important;
    }

    .form ul {
        margin: 10px 5px !important;
        padding: 10px !important;
    }

    .form-input {
        display: grid;
        grid-template-columns: 1fr 1fr 50px;
        gap: 10px;
        padding: 10px;
        margin: 5px 0;
        background: #fafafa;
        border: solid 1px #ddd;
        align-items: center;
        border-radius: 5px;
    }


    .form-input label {
        grid-column: 1/2
    }

    .form-input code {
        background: #fafafa;
        border: solid 1px #ddd;
        padding: 5px;
        box-sizing: border-box;
        grid-column: 2/4;
        font-size: 11px;
        text-align: center;
    }

    .form-input :is(input, select, button) {
        padding: 10px;
        background: #fff;
        border: solid 1px #ddd;
        border-radius: 5px;
    }

    .form-input input {
        grid-column: 1/2;
    }


    .form-input select {
        grid-column: 2/3;
    }

    .form .submit,
    .form .order {
        width: 200px;
        border: none;
        border-radius: 0 !important;
        font-weight: inherit !important;
        background: #146C94 !important;
        color: #fff !important;
        border: none !important;
        border-radius: 5px !important;
        text-shadow: none !important;
        cursor: pointer;
    }

    .form .order {
        padding: 10px;
        display: inline-block;
        border: none !important;
        background: #000 !important;
        border-radius: 5px;
    }

    .form-input button {
        background: #D21312;
        color: #fff;
        grid-column: 3/4;
        width: 100%;
        border: none;
    }

    .add-input {
        background: #fafafa;
        border: solid 1px #ddd;
        width: 100%;
        box-sizing: border-box;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px !important;
    }

    .add-input input {
        width: 100%;
        border: solid 1px #ddd;
        background: #fff;
        border-radius: 5px;
    }

    .add-input select {
        width: 100%;
        border: solid 1px #ddd;
        background: #fff;
        margin: 10px 0 !important;
        border-radius: 5px;
    }

    .add-input button {
        padding: 10px;
        border: none;
        background: #146C94;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
        border-radius: 5px;
        display: flex;
        align-items: center;
    }

    .title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 10px 0;
        border-bottom: solid 1px #ddd;

    }

    .title-bar h3 {
        margin: 0;
    }

    .title-bar a {
        margin: 0 !important;
        padding: 0 !important;
        background: #146C94;
        color: #fff !important;
        text-decoration: none !important;
        padding: 10px !important;
        border-radius: 5px;
        display: flex;
        align-items: center;
    }

    .title-bar a img {
        filter: invert(100%);
        width: 20px;
        margin-left: 10px;
    }
</style>

<?php
global $SITEURL;
global $GSADMIN;
$url = $link; ?>

<div class="title-bar">
    <h3>MultiField Create</h3>
    <a href="<?php echo $url; ?>">Back to list <img src="<?php echo $thisDomainPath . 'img/back.svg'; ?>"></a>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<form class="form" <?php

                    if (isset($_POST['creator'])) {

                        echo " action ='" . $link . "?&creator=" . $_POST['creator'] . "'";
                    }; ?> method="post">

    <input type="hidden" id="jstokenCSRF" name="tokenCSRF" value="<?php echo $tokenCSRF; ?>">

    <div x-data="content">



        <h4><i x-text="selectPage"></i></h4>

        <select name="filename" style="padding:10px;background:#fafafa;border:solid 1px #ddd;width:100%;margin:10px 0;border-radius:5px;">
            <option value="allmultifield">All</option>
            <?php foreach (glob(PATH_PAGES . '*', GLOB_ONLYDIR) as $file) {
                $new = new Pages();
                $filePure = pathinfo($file)['filename'];
                $filePureSet = $filePure . '-settings';
                $title = $new->db[$filePure]['title'];
                echo $filePure;
                echo '<option ' . ($_GET['creator'] ==   $filePureSet ? 'selected="selected"' : '') . ' value="' . $filePure . '">' . $title . '</option>';
            }; ?>
        </select>

        <h4><i x-text="addNewInput"></i></h4>

        <div class="add-input">
            <input type="text" x-model="title">
            <br>
            <select x-model="select" style="width:100%;padding:10px;margin:5px;">

                <template x-for="(selector,index) in selectList">
                    <option :value="selector" x-text="selectListLang[index]"></option>
                </template>
            </select>

            <br>
            <button @click.prevent="inputList.unshift({ 'label': title, 'value': '', 'type': select });count++" x-html="buttonAdd"></button>

        </div>

        <br>


        <h4><i x-text="inputNameList"></i></h4>
        <hr>

        <div style="margin:10px 0;" id="sortable">
            <template x-for="(input,index) in inputList" :key="index">
                <div class="form-input">
                    <label x-text="input.label.replace(/-/g,' ')"></label>
                    <code>&#60;?php multiFields('<span x-text="input.label.replace(/ /g,'-')"></span>') ;?&#62;</code>
                    <input :value="input.label.replace(/-/g,' ')" required style="width:100%;" x-model="input.label">
                    <input type="hidden" name="multi-field-label[]" :value="input.label.replace(/ /g,'-')" placeholder="label">
                    <select name="multi-field-type[]">
                        <template x-for="(selector,index) in selectList">
                            <option :value="selector" :selected="input.type==selector" x-text="selectListLang[index]"></option>
                        </template>
                    </select>
                    <button @click.prevent="if(confirm(sureQuestion)){inputList.splice(index, 1);}" x-html="remove"></button>


                </div>
            </template>
        </div>


        <input type="submit" :value="save" class="submit" name="submit">
        <button style="display:inline-flex;align-items:center;justify-content:center;" class="sortable order" x-html="sortName" @click.prevent="sortable"></button>


    </div>

</form>


<script>
    const content = {
        buttonAdd: 'Add New Input <img style="margin-left:10px;width:20px;filter:invert(100%);" src="<?php echo $thisDomainPath . 'img/plus.svg'; ?>">',
        title: 'Title',
        selectPage: 'Select the page on which you want to show the multifields',
        count: 0,
        select: '',
        inputNameList: 'MultiFields list',
        remove: '<img style="width:17px;filter:invert(100%);" src="<?php echo $thisDomainPath . "img/trash.svg"; ?>">',
        save: 'Save Inputs',
        addNewInput: 'Add New Input',
        sureQuestion: 'are you sure you want delete this item?',

        selectList: ["text", "textarea", "wysywig", "color", "date", "foto", "link", "checkbox", "dropdown"],
        selectListLang: ["text", "textarea", "wysywig", "color", "date", "foto", "link", "checkbox", "dropdown"],
        inputList: <?php

                    if ($_GET['creator'] !== '') {
                        echo file_get_contents($pathContent . $_GET['creator'] . '.json');
                    } else {
                        echo '[]';
                    }; ?>,

        sortableCheck: false,

        sortName: 'Change Order <img style="width:15px;filter:invert(100%);margin-left:5px;" src="<?php echo  $thisDomainPath . "img/order.svg"; ?>">',
        sortBtn: 'Change Order <img style="width:15px;filter:invert(100%);margin-left:5px;" src="<?php echo $thisDomainPath . "img/order.svg"; ?>">',
        sortBtnDone: 'Done <img style="width:15px;filter:invert(100%);margin-left:5px;" src="<?php echo $thisDomainPath . "img/done.svg"; ?>">',

        sortable: function() {



            if (this.sortableCheck == false) {
                this.sortableCheck = true;
                this.sortName = this.sortBtnDone;
                $("#sortable").sortable();
                $("#sortable").sortable("enable");
                document.querySelectorAll('.form-input').forEach(x => {
                    x.style.cursor = 'ns-resize';
                });

            } else {
                this.sortableCheck = false;
                this.sortName = this.sortBtn;
                $("#sortable").sortable("disable");
                document.querySelectorAll('.form-input').forEach(x => {
                    x.style.cursor = 'revert';
                });

            }

        },




    }
</script>


<script>

</script>