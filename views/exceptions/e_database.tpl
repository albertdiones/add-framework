{*SMARTY*}
{extends file='exceptions/e_add.tpl'}
{block name='main'}
{if add::content_type() == 'text/plain'}
= Database Error =

   {$user_message|default: 'A database error occured. Our developers has been notified about this and we will fix it as soon as we can.' }({$exception_datetime})
{else}
<h1>Database Error</h1>
   {if $user_message}
      <p>{$user_message}</p>
   {else}
      <p>A database error occured. Our developers has been notified about this and we will fix it as soon as we can.</p>
   {/if}
   <p>{$exception_datetime}</p>
{/if}
{/block}