   <ul style="padding-left:20px">
   {foreach $array as $index => $value}
      <li>
         {if not ctype_digit("$index")}
            <b>{preg_replace('/[\W_]+/',' ',$index)|capitalize}</b>
            {if preg_match('/[\W_]+/',$index)}
               ({$index}) :
            {/if}
         {else}
            <b>#{$index} &darr;</b>
         {/if}
         {if is_array($value) or is_object($value)}
            {include file='debug/list_array.tpl' array=$value}
         {else}
            <xmp>{$value}</xmp>
         {/if}
      </li>
   {/foreach}
   </ul>