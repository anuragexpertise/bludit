 <style>
   .sidebar {
     display: none !important;
   }

   .container {
     max-width: unset !important;
     width: 100% !important;
   }

   .col-lg-10 {
     all: unset;
     width: 100% !important;
   }

   .navbar{
    display: none !important; 
   }
 </style>

 <?php

  $count = 0;
  foreach (glob(PATH_UPLOADS_PAGES . $_GET['folder'] . '/*.*', GLOB_MARK) as $img) {

    $base = pathinfo($img)['basename'];

    echo '<a href="' . $base . '" class="photo" onclick="event.preventDefault();submitLink(' . $count . ')">
    <img src="' . $base . '" style="width:100px;height:100px;object-fit:cover;margin:10px;"></a>';
    $count++;
  }; ?>


 <script>
   document.querySelectorAll('.photo').forEach(x => {

     x.setAttribute('href', window.location.origin + '' + '<?php echo HTML_PATH_UPLOADS_PAGES . $_GET['folder']; ?>/' + x.getAttribute('href'))
     x.querySelector('img').setAttribute('src', window.location.origin + '<?php echo HTML_PATH_UPLOADS_PAGES . $_GET['folder']; ?>/' + x.querySelector('img').getAttribute('src'));

   })


   function submitLink(e) {


     let linker = document.querySelectorAll('.photo img')[e].getAttribute('src');
     console.log(linker);
     let linkerNew = linker;

     window.opener.document.querySelectorAll('*[name="multifield[]"]')[<?php echo $_GET['count'];?>].value = linkerNew;

     window.close();
   }
 </script>



 </div>
 </div>
 </div>
 </body>

 </html>