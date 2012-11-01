{*SMARTY*}
{add_layout file='common_layout'}
{block name="response"}
   {if add::content_type() == 'text/plain'}
      {block name=main}{$smarty.block.child}{/block}
   {else}
      {$smarty.block.parent}
   {/if}
{/block}