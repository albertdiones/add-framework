{*SMARTY*}
{extends file='exceptions/e_add.tpl'}
{block name='main'}
<h1>Database Error</h1>
   {if $user_message}
      {$user_message}
   {else}
      <p>A database error occured. Our developers has been notified about this and we will fix it as soon as we can.</p>
   {/if}
{/block}