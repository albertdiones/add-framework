{*SMARTY*}
{extends file='common_layout.tpl'}
{block name='main'}
   <h1>New Website</h1>
   Hello, this website is very new, please comeback again later to see contents.
   {if isset($log.warnings)}
      <h5>Warnings</h5>
      <ul>
      {foreach $log.warnings as $warning}
         <li>{$warning}</li>
      {/foreach}
      </ul>
   {/if}
   {if isset($log.dirs)}
      <h2>Directories Created</h2>
      <ul>
      {foreach $log.dirs as $dir}
         <li>{$dir}</li>
      {/foreach}
      </ul>
   {/if}
{/block}