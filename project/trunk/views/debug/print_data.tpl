{if add::content_type() == 'text/plain'}{*
   *}{if is_bool($value)}{*
      *}{$label|escape}: {*
      *}_{if $value}Yes{else}No{/if}_{*
   *}{elseif is_array($value) or is_object($value)}{*
         *}{$label|escape}:
{*          *}{foreach $value as $item_label => $item_value}
{*          *}{'  '|str_repeat:($indentations+1)}{*
            *}{if is_int($item_label)}#{else}*{/if} {include file='debug/print_data.tpl' label=$item_label value=$item_value indentations=$indentations+1}{*
         *}{foreachelse}{*
            *}_[]_{*
         *}{/foreach}{*
   *}{elseif is_null($value)}{*
         *}{$label|escape}: _null_{*
   *}{else}{*
         *}{$label|escape}: {$value|escape}{*
   *}{/if}

{else}
   <div style="{block name='error.style'}margin:2px auto; padding:2px 10px;width:720px{/block};font-size:8px; font-family:verdana;">
      &#x25BA; <b>{$label|escape}</b>:
         {if is_bool($value)}
            <i>{if $value}Yes{else}No{/if}</i>
         {elseif is_array($value) or is_object($value)}
            {foreach $value as $item_label => $item_value}
               {include file='debug/print_data.tpl' label=$item_label value=$item_value}
            {foreachelse}
               <i>Blank Array</i>
            {/foreach}
         {else}
            {$value|escape}
         {/if}
   </div>
{/if}