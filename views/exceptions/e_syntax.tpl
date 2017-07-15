{*SMARTY*}
{extends file='exceptions/e_add.tpl'}
{block name='main'}
{if add::content_type() == 'text/plain'}
= Syntax Error =

   {$user_message|default:'A syntax error occured. Our developers has been notified about this and we will fix it as soon as we can.'}({$exception_datetime})
{else}
<h1>Syntax Error</h1>
   {if $user_message}
      {$user_message}
   {else}
      <p>A syntax error occured. Our developers has been notified about this and we will fix it as soon as we can.</p>
   {/if}
   <p>{$exception_datetime}</p>
{/if}
{/block}


