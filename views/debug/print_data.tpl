{if add::current_controller()->content_type() == 'text/plain'}
   {$label}: {if is_bool($value)}{if $value}Yes{else}No{/if}{else}{$value}{/if}

{else}
   <div style="{block name='error.style'}margin:2px auto; padding:2px 10px;width:720px{/block};font-size:8px; font-family:verdana;">
      &#x25BA; <b>{$label}</b>:
         {if is_bool($value)}
            <i>{if $value}Yes{else}No{/if}</i>
         {elseif is_array($value) or is_object($value)}
            {foreach $value as $item_label => $item_value}
               {include file='debug/print_data.tpl' label=$item_label value=$item_value}
            {/foreach}
         {else}
            {$value}
         {/if}
   </div>
{/if}