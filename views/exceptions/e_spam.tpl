{*SMARTY*}
{extends file='exceptions/e_add.tpl'}
{block name='main'}
<h1>Spam Detected!</h1>
   {if $user_message}
      {$user_message}
   {else}
      <p>The system has detected spam attemps.</p>
   {/if}
{/block}