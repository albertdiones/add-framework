<?php
namespace add\terminal_error;
#var_dump($GLOBALS);

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
<html>
   <head>
      <style>
         body {
            background:#000;
            font:12px verdana;
            color:#fff;
         }
         section.page {
            color:#000;
            background:#fff;
            border-radius:5px;
            margin:20% auto;
            width:700px;
            box-shadow:0 0 6px 3px #f33;
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
         }
         h1 {
            padding:10px 20px;
            font-size:10px;
            font-weight:bold;
            text-align:center;
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
         footer {
            padding:5px 10px;
            background:#333;
            color:#fff;
            font-weight:bold;
            font-size:8px;
            border-radius:0 0 5px 5px;
         }
      </style>
   </head>
   <body>
      <section class="page">
         <header><?php echo error_header ?></header>
         <article>
            <h1><?php echo $error_message ?></h1>
            <ul>
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