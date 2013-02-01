<?php
namespace add\terminal_error;
#var_dump($GLOBALS);

$error_message = error_message;

$error_message = preg_replace('/(?<= )(\S{30,})(?= )/','<br />\1<br />',$error_message);

?>
<html>
   <head>
      <style>
         body {
            background:#eee;
            font:12px verdana;
         }
         section.page {
            color:#000;
            background:#fff;
            border:1px solid #000;
            border-radius:5px;
            margin:20% auto;
            width:600px;
         }
         header {
            padding:5px 10px;
            background:#333;
            color:#fff;
            font-size:8px;
            font-weight:bold;
            border-radius:5px 5px 0 0;
         }
         h1 {
            padding:10px 20px;
            font-size:10px;
            font-weight:bold;
            text-align:center;
            line-height:20px;
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
         </article>
         <footer><?php echo error_footer ?></footer>
      </section>
   </body>
</html>