{*SMARTY*}
{add_layout file='common_layout'}
{block name=meta_title}Error Occured{/block}
{block name="response"}
{assign var='exception_datetime' value={$smarty.server.REQUEST_TIME|date_format:'%Y-%m-%d %H:%M:%S %Z'} nocache }
{if add::content_type() == 'text/plain'}{block name=main}{$smarty.block.child}{/block}
{else}{$smarty.block.parent}
{/if}
{/block}