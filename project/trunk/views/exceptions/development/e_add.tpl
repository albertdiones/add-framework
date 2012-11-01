{*SMARTY*}
{extends file='exceptions/e_add.tpl'}
{* Exceptions View For Development Environment Status *}
{block name='main'}
{if add::current_controller()->content_type() == 'text/plain'}
   Uncaught Exception {if $exception->getCode()}({$exception->getCode()}){/if} {$exception->getMessage()}
   {print_r($exception->data,true)}
   {print_r($exception->getTrace())}
{else}
   <h1>Uncaught Exception</h1>
   <h2>{$exception->getMessage()}</h2>
   <small>{$exception->getCode()}</small>
   <h3>Debug Data</h3>
      <xmp>{add_debug::return_var_dump(array($exception->data))}</xmp>
   <h3>Trace</h3>
      <div>{nl2br($exception->getTraceAsString())}</div>
{/if}
{/block}