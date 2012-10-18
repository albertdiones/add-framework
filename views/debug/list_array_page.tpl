{*SMARTY*}
{extends file='common_layout.tpl'}
{block name='head.post'}
<style>
</style>
{/block}
{block name='main'}
   <div>
   <h1>{$ctrl_basename|replace:'_',' '}</h1>
   {include file='debug/list_array.tpl' array=$template_vars}
   </div>
{/block}