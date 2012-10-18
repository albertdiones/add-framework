{*SMARTY*}
{extends file='common_layout.tpl'}
{block name='head.post'}
<style>
</style>
{/block}
{block name='main'}
   <div>
   <h1>{$ctrl_basename|replace:'_',' '}</h1>
   {if $template_vars}
      {include file='debug/list_array.tpl' array=$template_vars}
   {else}
      No contents found
   {/if}
   </div>
{/block}