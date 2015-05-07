{*SMARTY*}
<div>
   {if $visible_page_urls}
      Pages:
      {foreach $visible_page_urls as $pagex => $page_url}
         {if isset($previous_pagex) and $pagex > ($previous_pagex+1) }
            &#8230;&#32;
         {/if}
         {if ( $current_page == $pagex )}
            <b>{$pagex}</b>
         {else}
            <a href='{$page_url|escape}'>{$pagex}</a>
         {/if}
         &#32;
         {assign var='previous_pagex' value=$pagex}
      {/foreach}
   {else}
      No More Pages
   {/if}
</div>