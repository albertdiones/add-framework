{*SMARTY*}

{if add::current_controller()->content_type() == 'text/plain'}
================================================================================================

   {$error.type} - *{$error.message}* x {$error.num_occured}
   {$error.file}:{$error.line}
   {foreach $error['file_lines'] as $error_file_line}{$error_file_line.file}:{$error_file_line.line}
   {/foreach}
{else}
<div style="{block name='error.style'}margin:5px auto;border:1px solid #333; background:{block name='error.style.background.value'}#FFAAAA{/block}; padding:5px 10px;width:900px; font-family:verdana;{/block}">
   <div style='float:left;width:40%;'>
      <small>{$error.type}</small>
      <p><b>{$error.message}</b>{if $error.num_occured > 1} x {$error.num_occured}{/if}</p>
      <br />
      <b>{$error.file}:{$error.line}</b>
      {foreach $error['file_lines'] as $x => $error_file_line}
         <small style='font-size:{max(12-$x,8)}px'>
         &lt; {$error_file_line.file}:{$error_file_line.line}
         </small>
      {/foreach}
   </div>
   {if !empty($code_on_error)}
   <div style='float:right;font-size:12px;width:49%;background:#eee;padding:5px 10px;border:1px solid #333;overflow:hidden;'>
      <div style="float:left;width:10%;color:#000;text-align:center;">
         {for $x = $code_on_error_start to $code_on_error_end}
            <code>{if $error.line == $x}<span style='color:red'>&#x25BA;</span>{else}{$x}{/if}<br /></code>
         {/for}
      </div>
      <div style="float:left;width:90%;text-align:left;white-space:nowrap">{$code_on_error}</div>
   </div>
   {/if}
   <div style='clear:both'></div>
</div>
{/if}