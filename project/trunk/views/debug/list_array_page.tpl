{*SMARTY*}
{extends file='common_layout.tpl'}
{block name='head.post'}
<style>
</style>
{/block}
{block name='main'}
   <div style="overflow:scroll">
   <h1>Contents</h1>
   {include file='debug/list_array.tpl' array=$template_vars}
   </div>
{/block}