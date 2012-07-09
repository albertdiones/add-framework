{*SMARTY*}
<!DOCTYPE html>
<html>
   <head>

{block name=metas}
      <title>{$meta_title|escape}</title>
      <meta name="description" content="{$meta_description|escape}" />
      <meta name="keywords" content="{$meta_keywords|escape}" />
{/block}

{block name=styles}
      <style type="text/css">
         body, table td, table th {
            color:#000;
            font:10px verdana;
         }
         body {
            background:-moz-linear-gradient(center top , #EEEEEE 5%, #FFFFFF 100%) no-repeat scroll 0 0 #EEEEE;
         }
         a,label {
            color:blue;
            text-decoration:none;
            cursor:pointer;
         }
         #page {
            width:900px;
            margin:0 auto;
            background:#fff;
            color:#000;
            box-shadow:0 0 10px #000000;
            border-radius:10px;
            border:1px solid #aaa;
         }
         #header {
            padding:10px 20px;
            margin:10px 0px;
            border-bottom:1px solid #eee;
         }
         #header a.title {
            color:#aaa;
            font:30px times small-caps;
            display:block;
            text-decoration:none;
         }
         #main {
            padding:10px 20px;
            margin:10px 20px;
         }
         #main form input[type=text],
         #main form input[type=password],
         #main form input[type=email],
         #main form select {
            display:block;
            margin:2px auto;
            padding:2px 5px;
            width:50%;
            height:30px;
            line-height:35px;
         }
         #main form label input[type=text],
         #main form label input[type=password],
         #main form label input[type=email],
         #main form label select {
            margin:0;
            width:98%;
         }
         #main form label {
            display:block;
            width:75%;
            text-transform:uppercase;
            cursor:label;
            color:blue;
            text-align:left;
            margin:10px auto;
         }
         #main form input[type=text],
         #main form input[type=password],
         #main form input[type=email] {
            width:90%;
         }
         #main form select {
            height:25px;
         }
         #main form {
            display:block;
            margin:10px auto;
            width:450px;
            padding:10px 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align:center;
         }
         #main form input[type=submit],
         #main form button[type=submit] {
            display:block;
            margin:10px auto;
            width:40%;
            height:40px;
         }
         #main form h1 {
            color:#888;
            font:30px times small-caps;
            text-transform:uppercase;
            text-align:center;
            margin:5px 10px;
         }
         #footer {
            padding:10px 20px;
            margin:10px 0px;
            border-top:1px solid #eee;
         }
         #footer .generator {
            font-size:8px;
            text-transform: uppercase;
         }
         .error_message,#main form label.error {
            color:red;
            font-weight:bold;
            display:block;
            text-align:center;
         }
         #main form label.error {
            margin:0;
            padding:0;
         }
         table {
            border:1px solid #ddd;
            border-collapse: collapse;
            margin:10px auto;
            max-width:90%;
         }
         table th {
            background:#eee;
         }
         table tbody th {
            text-align:right;
            border-right:1px solid #ddd;
         }
         table th, table td {
            margin:0;
            padding:0;
            border-bottom:1px solid #ddd;
         }
         table td, table th {
            padding:5px 10px;
            margin:5px 10px;
         }
         *[data-href] {
            cursor:pointer;
         }
         tr[data-href]:hover td {
            background-color:#ffe;
            color:blue;
         }
         table caption {
            font-weight:bold;
            font-size:14px;
            background:#666;
            color:#fff;
            padding:5px 10px;
            white-space:nowrap;
         }
         #main thead th.search form {
            background:transparent;
            width:auto;
            border-style:none;
            margin:0;
            padding:0;
         }
         #main thead th.search form input[type=text],
         #main thead th.search form button[type=submit],
         #main thead th.search form select {
            display:inline;
            width:200px;
            height:28px;
            margin:0;
            padding:0;
         }
         #main thead th.search form select {
            height:26px;
         }
         div.cb {
            clear:both;
         }
      </style>
{/block}

{block name=head_scripts}{/block}
   </head>
   <body class="{$ctrl_basename}">
      <div id="page">

{block name=header}
         <div id="header">
            <a href="{$C->base_url}" class="title">
               {$C->super_domain|upper}
            </a>
         </div>
{/block}
         <div id="main">