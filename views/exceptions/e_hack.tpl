{*SMARTY*}
{extends file='exceptions/e_add.tpl'}
{block name='main'}
<h1>Authentication Error</h1>
   {if $user_message}
      {$user_message}
   {else}
      <p>An authentication error occured. Please make sure you have entered a valid data.</p>
   {/if}
{/block}