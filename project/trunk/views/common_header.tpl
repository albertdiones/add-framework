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
      <link href="//presentable-css.googlecode.com/svn/project/trunk/all.css" rel="stylesheet" />
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