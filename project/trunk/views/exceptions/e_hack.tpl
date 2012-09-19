{*SMARTY*}
{extends file='exceptions/e_add.tpl'}
{block name='main'}
{if add::content_type() == 'text/plain'}
Authentication Error

   {$user_message|default:'An authentication error occured. Please make sure you have entered a valid data.'} 
{else}
<h1>Authentication Error</h1>
   {if $user_message}
      {$user_message}
   {else}
      <p>An authentication error occured. Please make sure you have entered a valid data.</p>
   {/if}
{/if}
{/block}