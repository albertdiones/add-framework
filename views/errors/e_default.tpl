{*SMARTY*}

{if add::current_controller()->content_type() == 'text/plain'}
================================================================================================

   {$error.type} - *{$error.message}* x {$error.num_occured}
   {$error.file}:{$error.line}
   {foreach $error['file_lines'] as $error_file_line}{$error_file_line.file}:{$error_file_line.line}
   {/foreach}
{else}
<div style="{block name='error.style'}margin:5px auto;border:1px solid #333; background:{block name='error.style.background.value'}#FFAAAA{/block}; padding:5px 10px;width:900px; font-family:verdana; font-size:12px{/block}">
   <div style='float:left;text-align:center;min-width:46%;margin:5px 10px;'>
      <small>{$error.type}</small>
      <p><b>{$error.message}</b>{if $error.num_occured > 1} x {$error.num_occured}{/if}</p>
      <br />
      <span style="font-weight:bold">{$error.file}:{$error.line}</span>
      {foreach $error['file_lines'] as $x => $error_file_line}
         <span style='font-size:{max(12-$x,8)}px'>
         &lt; {$error_file_line.file}:{$error_file_line.line}
         </span>
      {/foreach}
   </div>
   {if !empty($code_on_error)}
   <div style='float:left;margin:5px 10px;min-width:49%;font-size:12px;border:1px solid #333;overflow:hidden;background:white'>
      <div style="float:left;color:#000;text-align:center;background-color:#ddd;padding:3px 5px;">
         {for $x = $code_on_error_start to $code_on_error_end}
            <code style="background:transparent">{if $error.line == $x}<span style='color:red'>&#x25BA;</span>{else}{$x}{/if}<br /></code>
         {/for}
      </div>
      <div style="float:left;width:87%;text-align:left;white-space:nowrap; padding:5px 10px;">{$code_on_error}</div>
   </div>
   {/if}
   <div style='clear:both'></div>
</div>
{/if}