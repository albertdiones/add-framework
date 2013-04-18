<?php
namespace add\terminal_error;
#var_dump($GLOBALS);

header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

$error_message = error_message;

$error_message = preg_replace('/(?<= )(\S{30,})(?= )/','<br />\1<br />',$error_message);

$backtraces = error_trace;
$file_lines = array();
$dirname = dirname(__FILE__);
foreach (explode("\n",$backtraces) as $backtrace) {
   $backtrace = preg_replace('/^\#\d+\s+/',"",$backtrace);
   $backtrace = str_replace($dirname,".",$backtrace);
   $backtrace = str_replace(": ","\r\n   ",$backtrace);
   $backtrace = str_replace("::","\r\n   ::",$backtrace);
   $backtrace = str_replace("->","\r\n   ->",$backtrace);
   $file_lines[] = $backtrace;
}
?>
<!DOCTYPE html>
<html>
   <head>
      <style>
         body {
            background:#000;
            font:12px verdana;
            color:#fff;
         }

         section,header,footer,article {
            display:block;
         }

         section.page {
            color:#000;
            background:#fff;
            border-radius:5px;
            margin:20% auto;
            width:700px;
            box-shadow:0 0 6px 3px #f33;
            filter: progid:DXImageTransform.Microsoft.Shadow(color=#ff3333,direction=45,strength=6),
               progid:DXImageTransform.Microsoft.Shadow(color=#ff3333,direction=135,strength=6),
               progid:DXImageTransform.Microsoft.Shadow(color=#ff3333,direction=225,strength=6),
               progid:DXImageTransform.Microsoft.Shadow(color=#ff3333,direction=315,strength=6);
         }
         header {
            padding:5px 10px;
            background:#333;
            color:#fff;
            font-size:8px;
            font-weight:bold;
            border-radius:5px 5px 0 0;
         }
         article {
            color:#333;
            background:#fff;
            text-align:center;
         }
         h1 {
            padding:10px 20px;
            font-size:10px;
            font-weight:bold;
            line-height:20px;
            color:#a00;
         }
         p {
            font:8px verdana;
            text-align:center;
         }
         textarea {
            width:90%;
            height:300px;
            margin:0 auto;
            display:block;
         }
         ul {
            cursor:pointer;
            display:none;
            text-align:left;
         }
         a {
            color:#933;
            font-size:8px;
            text-decoration:none;
         }
         footer {
            padding:5px 10px;
            background:#333;
            color:#fff;
            font-weight:bold;
            font-size:8px;
            border-radius:0 0 5px 5px;
         }
      </style>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
      <script>
         $().ready(
            function() {
               $('a[href="#backtrace"]').click(
                  function() {
                     var $ul = $('ul#backtrace');
                     if ($ul.not(':visible').length) {
                        $ul.slideDown();
                     }
                     else {
                        $ul.slideUp();
                     }
                     return false;
                  }
               );
            }
            );
         document.createElement("section");
         document.createElement("article");
         document.createElement("footer");
         document.createElement("header");
      </script>
   </head>
   <body>
      <section class="page">
         <header><?php echo error_header ?></header>
         <article>
            <h1><?php echo $error_message ?></h1>
            <a href="#backtrace">&uarr;&darr; Toggle Backtrace</a>
            <ul id="backtrace">
               <?php foreach ($file_lines as $file_line): ?>
               <li><xmp><?php echo $file_line ?></xmp></li>
               <?php endforeach ?>
            </ul>
            <p>Contact the system administrator with the error printed above</p>
         </article>
         <footer><?php echo error_footer ?></footer>
      </section>
   </body>
</html>