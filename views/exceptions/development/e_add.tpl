{*SMARTY*}
{extends file='exceptions/e_add.tpl'}
{* Exceptions View For Development Environment Status *}
{block name='main'}
{if add::content_type() == 'text/plain'}
= Uncaught {get_class($exception)}: {if $exception->getCode()}({$exception->getCode()}){/if} *{$exception->getMessage()}* =
{include file='debug/print_data.tpl' label="Data" value=$exception->data}
{include file='debug/print_data.tpl' label="BackTrace" value=$exception->getTrace()}
{else}
   <h1>Uncaught Exception</h1>
   <h3>{get_class($exception)} <small>#{$exception->getCode()}</small></h3>
   <h3>{$exception->getMessage()}</h3>
   <h3>Debug Data</h3>
      <xmp>{add_debug::return_var_dump(array($exception->data))}</xmp>
   <h3>Trace</h3>
      <div>{nl2br($exception->getTraceAsString())}</div>
{/if}
{/block}