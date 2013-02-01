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
            background:#000;
            font:12px verdana;
            color:#fff;
         }
         section.page {
            color:#000;
            background:#fff;
            border-radius:5px;
            margin:20% auto;
            width:600px;
            box-shadow:0 0 4px 2px red;
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
         p {
            font:8px verdana;
            text-align:center;
         }
         textarea {
            width:100%;
            height:300px;
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
            <p>Contact the system administrator with the error printed below</p>
         </article>
         <footer><?php echo error_footer ?></footer>
      </section>
   </body>
</html>