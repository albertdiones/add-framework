{*SMARTY*}

<div style="{block name='error.style'}margin:5px 10px;border:1px solid #333; background: #FFAAAA; padding:5px 10px;width:720px{/block};font-family:verdana;">
   <div style='float:left;width:40%;'>
      <small>{$error.type}</small>
      <p>{$error.message}</p>
      <small>
         {$error.file}:{$error.line}
         {foreach $error['file_lines'] as $error_file_line}
            &lt; {$error_file_line.file}:{$error_file_line.line}
         {/foreach}
      </small>
   </div>
   <div style='float:right;font-size:8px;width:40%;background:#eee;padding:5px 10px;border:1px solid #333;overflow:hidden;'>
      <div style="float:left;width:5%;color:#000;text-align:center;">
         {for $x = $code_on_error_start to $code_on_error_end}
            <code>
            {if $error.line == $x}
               <span style='color:red'>&#x25BA;</span>
            {else}
               {$x}
            {/if}<br />
            </code>
         {/for}
      </div>
      <div style="float:right;width:95%">
         {$code_on_error}
      </div>
   </div>
   <div style='clear:both'></div>
</div>