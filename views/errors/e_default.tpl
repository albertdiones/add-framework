{*SMARTY*}

{if add::current_controller()->content_type() == 'text/plain'}
================================================================================================

   {$error.type} - {$error.message}
   {$error.file}:{$error.line}
   {foreach $error['file_lines'] as $error_file_line}{$error_file_line.file}:{$error_file_line.line}
   {/foreach}
{else}
<div style="{block name='error.style'}margin:5px auto;border:1px solid #333; background: #FFAAAA; padding:5px 10px;width:720px{/block};font-family:verdana;">
   <div style='float:left;width:40%;'>
      <small>{$error.type}</small>
      <p>{$error.message}</p>
      <br />
      <small style="font-size:8px;">
         {$error.file}:{$error.line}
         {foreach $error['file_lines'] as $error_file_line}
            &lt; {$error_file_line.file}:{$error_file_line.line}
         {/foreach}
      </small>
   </div>
   {if !empty($code_on_error)}
   <div style='float:right;font-size:8px;width:40%;background:#eee;padding:5px 10px;border:1px solid #333;overflow:hidden;'>
      <div style="float:left;width:10%;color:#000;text-align:center;">
         {for $x = $code_on_error_start to $code_on_error_end}
            <code>{if $error.line == $x}<span style='color:red'>&#x25BA;</span>{else}{$x}{/if}<br /></code>
         {/for}
      </div>
      <div style="float:left;width:10%;text-align:left">{$code_on_error}</div>
   </div>
   {/if}
   <div style='clear:both'></div>
</div>
{/if}