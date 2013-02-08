{*SMARTY*}
{extends file='exceptions/e_add.tpl'}
{block name='main'}
{if add::content_type() == 'text/plain'}
= System Error =

   {$user_message|default:'Non-existing template path.'}
{else}
<h1>System Error</h1>
   {if $user_message}
      {$user_message}
   {else}
      <p>Non-existing template path.</p>
   {/if}

{/if}
{/block}


