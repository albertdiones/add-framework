{*SMARTY*}
{* Exceptions View For Development Environment Status *}
{if add::current_controller()->content_type() == 'text/plain'}
   Uncaught Exception {if $exception->getCode()}({$exception->getCode()}){/if} {$exception->getMessage()}
   {print_r($exception->data,true)}
   {print_r($exception->getTrace())}
{else}
   {block name='main'}
   <h1>Uncaught Exception</h1>
   <h2>{$exception->getMessage()}</h2>
   <small>{$exception->getCode()}</small>
   <h3>Debug Data</h3>
   <xmp>
   {add_debug::return_var_dump(array($exception->data))}
   </xmp>
   {nl2br($exception->getTraceAsString())}
   {/block}
{/if}